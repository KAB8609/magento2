<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Wishlist
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Wishlist front controller
 *
 * @category    Magento
 * @package     Magento_Wishlist
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Wishlist\Controller;

use Magento\App\Action\NotFoundException;
use Magento\App\RequestInterface;

class Index
    extends \Magento\Wishlist\Controller\AbstractController
    implements \Magento\Catalog\Controller\Product\View\ViewInterface
{
    /**
     * @var \Magento\App\Response\Http\FileFactory
     */
    protected $_fileResponseFactory;

    /**
     * @var \Magento\Wishlist\Model\Config
     */
    protected $_wishlistConfig;

    /**
     * If true, authentication in this controller (wishlist) could be skipped
     *
     * @var bool
     */
    protected $_skipAuthentication = false;

    /**
     * Core registry
     *
     * @var \Magento\Core\Model\Registry
     */
    protected $_coreRegistry;

    /**
     * @var \Magento\Core\Model\Url
     */
    protected $_url;

    /**
     * @var \Magento\Core\App\Action\FormKeyValidator
     */
    protected $_formKeyValidator;

    /**
     * @param \Magento\App\Action\Context $context
     * @param \Magento\Core\Model\Registry $coreRegistry
     * @param \Magento\Wishlist\Model\Config $wishlistConfig
     * @param \Magento\Core\Model\Url $url
     * @param \Magento\App\Response\Http\FileFactory $fileResponseFactory
     * @param \Magento\Core\App\Action\FormKeyValidator $formKeyValidator
     */
    public function __construct(
        \Magento\App\Action\Context $context,
        \Magento\Core\Model\Registry $coreRegistry,
        \Magento\Wishlist\Model\Config $wishlistConfig,
        \Magento\Core\Model\Url $url,
        \Magento\App\Response\Http\FileFactory $fileResponseFactory,
        \Magento\Core\App\Action\FormKeyValidator $formKeyValidator
    ) {
        $this->_coreRegistry = $coreRegistry;
        $this->_wishlistConfig = $wishlistConfig;
        $this->_url = $url;
        $this->_fileResponseFactory = $fileResponseFactory;
        $this->_formKeyValidator = $formKeyValidator;
        parent::__construct($context);
    }

    /**
     * Dispatch request
     *
     * @param RequestInterface $request
     * @return mixed
     * @throws \Magento\App\Action\NotFoundException
     */
    public function dispatch(RequestInterface $request)
    {
        if (!$this->_skipAuthentication
            && !$this->_objectManager->get('Magento\Customer\Model\Session')->authenticate($this)
        ) {
            $this->setFlag('', 'no-dispatch', true);
            $customerSession = $this->_objectManager->get('Magento\Customer\Model\Session');
            if (!$customerSession->getBeforeWishlistUrl()) {
                $customerSession->setBeforeWishlistUrl($this->_redirect->getRefererUrl());
            }
            $customerSession->setBeforeWishlistRequest($request->getParams());
        }
        if (!$this->_objectManager->get('Magento\Core\Model\Store\Config')->getConfigFlag('wishlist/general/active')) {
            throw new NotFoundException();
        }
        return parent::dispatch($request);
    }

    /**
     * Set skipping authentication in actions of this controller (wishlist)
     *
     * @return \Magento\Wishlist\Controller\Index
     */
    public function skipAuthentication()
    {
        $this->_skipAuthentication = true;
        return $this;
    }

    /**
     * Retrieve wishlist object
     * @param int $wishlistId
     * @return \Magento\Wishlist\Model\Wishlist|bool
     */
    protected function _getWishlist($wishlistId = null)
    {
        $wishlist = $this->_coreRegistry->registry('wishlist');
        if ($wishlist) {
            return $wishlist;
        }

        try {
            if (!$wishlistId) {
                $wishlistId = $this->getRequest()->getParam('wishlist_id');
            }
            $customerId = $this->_objectManager->get('Magento\Customer\Model\Session')->getCustomerId();
            /* @var \Magento\Wishlist\Model\Wishlist $wishlist */
            $wishlist = $this->_objectManager->create('Magento\Wishlist\Model\Wishlist');
            if ($wishlistId) {
                $wishlist->load($wishlistId);
            } else {
                $wishlist->loadByCustomer($customerId, true);
            }

            if (!$wishlist->getId() || $wishlist->getCustomerId() != $customerId) {
                $wishlist = null;
                throw new \Magento\Core\Exception(
                    __("The requested wish list doesn't exist.")
                );
            }

            $this->_coreRegistry->register('wishlist', $wishlist);
        } catch (\Magento\Core\Exception $e) {
            $this->_objectManager->get('Magento\Wishlist\Model\Session')->addError($e->getMessage());
            return false;
        } catch (\Exception $e) {
            $this->_objectManager->get('Magento\Wishlist\Model\Session')->addException($e,
                __('Wish List could not be created.')
            );
            return false;
        }

        return $wishlist;
    }

    /**
     * Display customer wishlist
     *
     * @throws NotFoundException
     */
    public function indexAction()
    {
        if (!$this->_getWishlist()) {
            throw new NotFoundException();
        }
        $this->_layoutServices->loadLayout();

        $session = $this->_objectManager->get('Magento\Customer\Model\Session');
        $block   = $this->_layoutServices->getLayout()->getBlock('customer.wishlist');
        $referer = $session->getAddActionReferer(true);
        if ($block) {
            $block->setRefererUrl($this->_redirect->getRefererUrl());
            if ($referer) {
                $block->setRefererUrl($referer);
            }
        }

        $messageStores = array(
            'Magento\Customer\Model\Session',
            'Magento\Checkout\Model\Session',
            'Magento\Catalog\Model\Session',
            'Magento\Wishlist\Model\Session'
        );
        $this->_layoutServices->getLayout()->initMessages($messageStores);

        $this->renderLayout();
    }

    /**
     * Adding new item
     *
     * @throws NotFoundException
     */
    public function addAction()
    {
        $wishlist = $this->_getWishlist();
        if (!$wishlist) {
            throw new NotFoundException();
        }

        $session = $this->_objectManager->get('Magento\Customer\Model\Session');

        $productId = (int) $this->getRequest()->getParam('product');
        if (!$productId) {
            $this->_redirect('*/');
            return;
        }

        $product = $this->_objectManager->create('Magento\Catalog\Model\Product')->load($productId);
        if (!$product->getId() || !$product->isVisibleInCatalog()) {
            $session->addError(__('We can\'t specify a product.'));
            $this->_redirect('*/');
            return;
        }

        try {
            $requestParams = $this->getRequest()->getParams();
            if ($session->getBeforeWishlistRequest()) {
                $requestParams = $session->getBeforeWishlistRequest();
                $session->unsBeforeWishlistRequest();
            }
            $buyRequest = new \Magento\Object($requestParams);

            $result = $wishlist->addNewItem($product, $buyRequest);
            if (is_string($result)) {
                throw new \Magento\Core\Exception($result);
            }
            $wishlist->save();

            $this->_eventManager->dispatch(
                'wishlist_add_product',
                array(
                    'wishlist'  => $wishlist,
                    'product'   => $product,
                    'item'      => $result
                )
            );

            $referer = $session->getBeforeWishlistUrl();
            if ($referer) {
                $session->setBeforeWishlistUrl(null);
            } else {
                $referer = $this->_redirect->getRefererUrl();
            }

            /**
             *  Set referer to avoid referring to the compare popup window
             */
            $session->setAddActionReferer($referer);

            /** @var $helper \Magento\Wishlist\Helper\Data */
            $helper = $this->_objectManager->get('Magento\Wishlist\Helper\Data')->calculate();
            $message = __('%1 has been added to your wishlist. Click <a href="%2">here</a> to continue shopping.', $this->_objectManager->get('Magento\Escaper')->escapeHtml($product->getName()), $this->_objectManager->get('Magento\Escaper')->escapeUrl($referer));
            $session->addSuccess($message);
        }
        catch (\Magento\Core\Exception $e) {
            $session->addError(__('An error occurred while adding item to wish list: %1', $e->getMessage()));
        }
        catch (\Exception $e) {
            $session->addError(__('An error occurred while adding item to wish list.'));
            $this->_objectManager->get('Magento\Logger')->logException($e);
        }

        $this->_redirect('*', array('wishlist_id' => $wishlist->getId()));
    }

    /**
     * Action to reconfigure wishlist item
     *
     * @throws NotFoundException
     */
    public function configureAction()
    {
        $id = (int) $this->getRequest()->getParam('id');
        try {
            /* @var $item \Magento\Wishlist\Model\Item */
            $item = $this->_objectManager->create('Magento\Wishlist\Model\Item');
            $item->loadWithOptions($id);
            if (!$item->getId()) {
                throw new \Magento\Core\Exception(__('We can\'t load the wish list item.'));
            }
            $wishlist = $this->_getWishlist($item->getWishlistId());
            if (!$wishlist) {
                throw new NotFoundException();
            }

            $this->_coreRegistry->register('wishlist_item', $item);

            $params = new \Magento\Object();
            $params->setCategoryId(false);
            $params->setConfigureMode(true);
            $buyRequest = $item->getBuyRequest();
            if (!$buyRequest->getQty() && $item->getQty()) {
                $buyRequest->setQty($item->getQty());
            }
            if ($buyRequest->getQty() && !$item->getQty()) {
                $item->setQty($buyRequest->getQty());
                $this->_objectManager->get('Magento\Wishlist\Helper\Data')->calculate();
            }
            $params->setBuyRequest($buyRequest);
            $this->_objectManager->get('Magento\Catalog\Helper\Product\View')
                ->prepareAndRender($item->getProductId(), $this, $params);
        } catch (\Magento\Core\Exception $e) {
            $this->_objectManager->get('Magento\Customer\Model\Session')->addError($e->getMessage());
            $this->_redirect('*');
            return;
        } catch (\Exception $e) {
            $this->_objectManager->get('Magento\Customer\Model\Session')
                ->addError(__('We can\'t configure the product.'));
            $this->_objectManager->get('Magento\Logger')->logException($e);
            $this->_redirect('*');
            return;
        }
    }

    /**
     * Action to accept new configuration for a wishlist item
     */
    public function updateItemOptionsAction()
    {
        $session = $this->_objectManager->get('Magento\Customer\Model\Session');
        $productId = (int) $this->getRequest()->getParam('product');
        if (!$productId) {
            $this->_redirect('*/');
            return;
        }

        $product = $this->_objectManager->create('Magento\Catalog\Model\Product')->load($productId);
        if (!$product->getId() || !$product->isVisibleInCatalog()) {
            $session->addError(__('We can\'t specify a product.'));
            $this->_redirect('*/');
            return;
        }

        try {
            $id = (int) $this->getRequest()->getParam('id');
            /* @var \Magento\Wishlist\Model\Item */
            $item = $this->_objectManager->create('Magento\Wishlist\Model\Item');
            $item->load($id);
            $wishlist = $this->_getWishlist($item->getWishlistId());
            if (!$wishlist) {
                $this->_redirect('*/');
                return;
            }

            $buyRequest = new \Magento\Object($this->getRequest()->getParams());

            $wishlist->updateItem($id, $buyRequest)
                ->save();

            $this->_objectManager->get('Magento\Wishlist\Helper\Data')->calculate();
            $this->_eventManager->dispatch('wishlist_update_item', array(
                'wishlist' => $wishlist, 'product' => $product, 'item' => $wishlist->getItem($id))
            );

            $this->_objectManager->get('Magento\Wishlist\Helper\Data')->calculate();

            $message = __('%1 has been updated in your wish list.', $product->getName());
            $session->addSuccess($message);
        } catch (\Magento\Core\Exception $e) {
            $session->addError($e->getMessage());
        } catch (\Exception $e) {
            $session->addError(__('An error occurred while updating wish list.'));
            $this->_objectManager->get('Magento\Logger')->logException($e);
        }
        $this->_redirect('*/*', array('wishlist_id' => $wishlist->getId()));
    }

    /**
     * Update wishlist item comments
     *
     * @throws NotFoundException
     */
    public function updateAction()
    {
        if (!$this->_formKeyValidator->validate($this->getRequest())) {
            return $this->_redirect('*/*/');
        }
        $wishlist = $this->_getWishlist();
        if (!$wishlist) {
            throw new NotFoundException();
        }

        $post = $this->getRequest()->getPost();
        if ($post && isset($post['description']) && is_array($post['description'])) {
            $updatedItems = 0;

            foreach ($post['description'] as $itemId => $description) {
                $item = $this->_objectManager->create('Magento\Wishlist\Model\Item')->load($itemId);
                if ($item->getWishlistId() != $wishlist->getId()) {
                    continue;
                }

                // Extract new values
                $description = (string) $description;

                if ($description == $this->_objectManager->get('Magento\Wishlist\Helper\Data')->defaultCommentString()) {
                    $description = '';
                } elseif (!strlen($description)) {
                    $description = $item->getDescription();
                }

                $qty = null;
                if (isset($post['qty'][$itemId])) {
                    $qty = $this->_processLocalizedQty($post['qty'][$itemId]);
                }
                if (is_null($qty)) {
                    $qty = $item->getQty();
                    if (!$qty) {
                        $qty = 1;
                    }
                } elseif (0 == $qty) {
                    try {
                        $item->delete();
                    } catch (\Exception $e) {
                        $this->_objectManager->get('Magento\Logger')->logException($e);
                        $this->_objectManager->get('Magento\Customer\Model\Session')->addError(
                            __('Can\'t delete item from wishlist')
                        );
                    }
                }

                // Check that we need to save
                if (($item->getDescription() == $description) && ($item->getQty() == $qty)) {
                    continue;
                }
                try {
                    $item->setDescription($description)
                        ->setQty($qty)
                        ->save();
                    $updatedItems++;
                } catch (\Exception $e) {
                    $this->_objectManager->get('Magento\Customer\Model\Session')->addError(
                        __('Can\'t save description %1', $this->_objectManager->get('Magento\Escaper')->escapeHtml($description))
                    );
                }
            }

            // save wishlist model for setting date of last update
            if ($updatedItems) {
                try {
                    $wishlist->save();
                    $this->_objectManager->get('Magento\Wishlist\Helper\Data')->calculate();
                }
                catch (\Exception $e) {
                    $this->_objectManager->get('Magento\Customer\Model\Session')->addError(__('Can\'t update wish list'));
                }
            }

            if (isset($post['save_and_share'])) {
                $this->_redirect('*/*/share', array('wishlist_id' => $wishlist->getId()));
                return;
            }
        }
        $this->_redirect('*', array('wishlist_id' => $wishlist->getId()));
    }

    /**
     * Remove item
     *
     * @throws NotFoundException
     */
    public function removeAction()
    {
        $id = (int) $this->getRequest()->getParam('item');
        $item = $this->_objectManager->create('Magento\Wishlist\Model\Item')->load($id);
        if (!$item->getId()) {
            throw new NotFoundException();
        }
        $wishlist = $this->_getWishlist($item->getWishlistId());
        if (!$wishlist) {
            throw new NotFoundException();
        }
        try {
            $item->delete();
            $wishlist->save();
        } catch (\Magento\Core\Exception $e) {
            $this->_objectManager->get('Magento\Customer\Model\Session')->addError(
                __('An error occurred while deleting the item from wish list: %1', $e->getMessage())
            );
        } catch(\Exception $e) {
            $this->_objectManager->get('Magento\Customer\Model\Session')->addError(
                __('An error occurred while deleting the item from wish list.')
            );
        }

        $this->_objectManager->get('Magento\Wishlist\Helper\Data')->calculate();

        $url = $this->_redirect->getRedirectUrl($this->_url->getUrl('*/*'));
        $this->getResponse()->setRedirect($url);
    }

    /**
     * Add wishlist item to shopping cart and remove from wishlist
     *
     * If Product has required options - item removed from wishlist and redirect
     * to product view page with message about needed defined required options
     */
    public function cartAction()
    {
        $itemId = (int) $this->getRequest()->getParam('item');

        /* @var $item \Magento\Wishlist\Model\Item */
        $item = $this->_objectManager->create('Magento\Wishlist\Model\Item')->load($itemId);
        if (!$item->getId()) {
            return $this->_redirect('*/*');
        }
        $wishlist = $this->_getWishlist($item->getWishlistId());
        if (!$wishlist) {
            return $this->_redirect('*/*');
        }

        // Set qty
        $qty = $this->getRequest()->getParam('qty');
        if (is_array($qty)) {
            if (isset($qty[$itemId])) {
                $qty = $qty[$itemId];
            } else {
                $qty = 1;
            }
        }
        $qty = $this->_processLocalizedQty($qty);
        if ($qty) {
            $item->setQty($qty);
        }

        /* @var $session \Magento\Core\Model\Session\Generic */
        $session    = $this->_objectManager->get('Magento\Wishlist\Model\Session');
        $cart       = $this->_objectManager->get('Magento\Checkout\Model\Cart');

        $redirectUrl = $this->_url->getUrl('*/*');

        try {
            $options = $this->_objectManager->create('Magento\Wishlist\Model\Item\Option')->getCollection()
                    ->addItemFilter(array($itemId));
            $item->setOptions($options->getOptionsByItem($itemId));

            $buyRequest = $this->_objectManager->get('Magento\Catalog\Helper\Product')->addParamsToBuyRequest(
                $this->getRequest()->getParams(),
                array('current_config' => $item->getBuyRequest())
            );

            $item->mergeBuyRequest($buyRequest);
            $item->addToCart($cart, true);
            $cart->save()->getQuote()->collectTotals();
            $wishlist->save();

            $this->_objectManager->get('Magento\Wishlist\Helper\Data')->calculate();

            if ($this->_objectManager->get('Magento\Checkout\Helper\Cart')->getShouldRedirectToCart()) {
                $redirectUrl = $this->_objectManager->get('Magento\Checkout\Helper\Cart')->getCartUrl();
            } else if ($this->_redirect->getRefererUrl()) {
                $redirectUrl = $this->_redirect->getRefererUrl();
            }
            $this->_objectManager->get('Magento\Wishlist\Helper\Data')->calculate();
        } catch (\Magento\Core\Exception $e) {
            if ($e->getCode() == \Magento\Wishlist\Model\Item::EXCEPTION_CODE_NOT_SALABLE) {
                $session->addError(__('This product(s) is out of stock.'));
            } else if ($e->getCode() == \Magento\Wishlist\Model\Item::EXCEPTION_CODE_HAS_REQUIRED_OPTIONS) {
                $this->_objectManager->create('Magento\Catalog\Model\Session')->addNotice($e->getMessage());
                $redirectUrl = $this->_url->getUrl('*/*/configure/', array('id' => $item->getId()));
            } else {
                $this->_objectManager->get('Magento\Catalog\Model\Session')->addNotice($e->getMessage());
                $redirectUrl = $this->_url->getUrl('*/*/configure/', array('id' => $item->getId()));
            }
        } catch (\Exception $e) {
            $session->addException($e, __('Cannot add item to shopping cart'));
        }

        $this->_objectManager->get('Magento\Wishlist\Helper\Data')->calculate();

        return $this->getResponse()->setRedirect($redirectUrl);
    }

    /**
     * Add cart item to wishlist and remove from cart
     *
     * @throws NotFoundException
     */
    public function fromcartAction()
    {
        $wishlist = $this->_getWishlist();
        if (!$wishlist) {
            throw new NotFoundException();
        }
        $itemId = (int) $this->getRequest()->getParam('item');

        /* @var \Magento\Checkout\Model\Cart $cart */
        $cart = $this->_objectManager->get('Magento\Checkout\Model\Cart');
        $session = $this->_objectManager->get('Magento\Checkout\Model\Session');

        try{
            $item = $cart->getQuote()->getItemById($itemId);
            if (!$item) {
                throw new \Magento\Core\Exception(
                    __("The requested cart item doesn\'t exist.")
                );
            }

            $productId  = $item->getProductId();
            $buyRequest = $item->getBuyRequest();

            $wishlist->addNewItem($productId, $buyRequest);

            $productIds[] = $productId;
            $cart->getQuote()->removeItem($itemId);
            $cart->save();
            $this->_objectManager->get('Magento\Wishlist\Helper\Data')->calculate();
            $productName = $this->_objectManager->get('Magento\Escaper')->escapeHtml($item->getProduct()->getName());
            $wishlistName = $this->_objectManager->get('Magento\Escaper')->escapeHtml($wishlist->getName());
            $session->addSuccess(
                __("%1 has been moved to wish list %2", $productName, $wishlistName)
            );
            $wishlist->save();
        } catch (\Magento\Core\Exception $e) {
            $session->addError($e->getMessage());
        } catch (\Exception $e) {
            $session->addException($e, __('We can\'t move the item to the wish list.'));
        }

        return $this->getResponse()->setRedirect($this->_objectManager->get('Magento\Checkout\Helper\Cart')->getCartUrl());
    }

    /**
     * Prepare wishlist for share
     */
    public function shareAction()
    {
        $this->_getWishlist();
        $this->_layoutServices->loadLayout();
        $this->_layoutServices->getLayout()->initMessages(array('Magento\Customer\Model\Session', 'Magento\Wishlist\Model\Session'));
        $this->renderLayout();
    }

    /**
     * Share wishlist
     *
     * @return \Magento\App\Action\Action|void
     * @throws NotFoundException
     */
    public function sendAction()
    {
        if (!$this->_formKeyValidator->validate($this->getRequest())) {
            return $this->_redirect('*/*/');
        }

        $wishlist = $this->_getWishlist();
        if (!$wishlist) {
            throw new NotFoundException();
        }

        $sharingLimit = $this->_wishlistConfig->getSharingEmailLimit();
        $textLimit = $this->_wishlistConfig->getSharingTextLimit();
        $emailsLeft = $sharingLimit - $wishlist->getShared();
        $emails  = explode(',', $this->getRequest()->getPost('emails'));
        $error   = false;
        $message = (string) $this->getRequest()->getPost('message');
        if (strlen($message) > $textLimit) {
            $error = __('Message length must not exceed %1 symbols', $textLimit);
        } else {
            $message = nl2br(htmlspecialchars($message));
            if (empty($emails)) {
                $error = __('Email address can\'t be empty.');
            } else if (count($emails) > $emailsLeft) {
                $error = __('This wishlist can be shared %1 more times.', $emailsLeft);
            } else {
                foreach ($emails as $index => $email) {
                    $email = trim($email);
                    if (!\Zend_Validate::is($email, 'EmailAddress')) {
                        $error = __('Please input a valid email address.');
                        break;
                    }
                    $emails[$index] = $email;
                }
            }
        }

        if ($error) {
            $this->_objectManager->get('Magento\Wishlist\Model\Session')->addError($error);
            $this->_objectManager->get('Magento\Wishlist\Model\Session')
                ->setSharingForm($this->getRequest()->getPost());
            $this->_redirect('*/*/share');
            return;
        }

        $translate = $this->_objectManager->get('Magento\Core\Model\Translate');
        /* @var $translate \Magento\Core\Model\Translate */
        $translate->setTranslateInline(false);
        $sent = 0;

        try {
            $customer = $this->_objectManager->get('Magento\Customer\Model\Session')->getCustomer();

            /*if share rss added rss feed to email template*/
            if ($this->getRequest()->getParam('rss_url')) {
                $rss_url = $this->_layoutServices->getLayout()
                    ->createBlock('Magento\Wishlist\Block\Share\Email\Rss')
                    ->setWishlistId($wishlist->getId())
                    ->toHtml();
                $message .= $rss_url;
            }
            $wishlistBlock = $this->_layoutServices->getLayout()->createBlock('Magento\Wishlist\Block\Share\Email\Items')->toHtml();

            $emails = array_unique($emails);
            /* @var $emailModel \Magento\Core\Model\Email\Template */
            $emailModel = $this->_objectManager->create('Magento\Core\Model\Email\Template');

            $sharingCode = $wishlist->getSharingCode();

            try {
                foreach ($emails as $email) {
                    $emailModel->sendTransactional(
                        $this->_objectManager->get('Magento\Core\Model\Store\Config')->getConfig('wishlist/email/email_template'),
                        $this->_objectManager->get('Magento\Core\Model\Store\Config')->getConfig('wishlist/email/email_identity'),
                        $email,
                        null,
                        array(
                            'customer'      => $customer,
                            'salable'       => $wishlist->isSalable() ? 'yes' : '',
                            'items'         => $wishlistBlock,
                            'addAllLink'    => $this->_url->getUrl('*/shared/allcart', array('code' => $sharingCode)),
                            'viewOnSiteLink'=> $this->_url->getUrl('*/shared/index', array('code' => $sharingCode)),
                            'message'       => $message
                        )
                    );
                    $sent++;
                }
            } catch (\Exception $e) {
                $wishlist->setShared($wishlist->getShared() + $sent);
                $wishlist->save();
                throw $e;
            }
            $wishlist->setShared($wishlist->getShared() + $sent);
            $wishlist->save();

            $translate->setTranslateInline(true);

            $this->_eventManager->dispatch('wishlist_share', array('wishlist'=>$wishlist));
            $this->_objectManager->get('Magento\Customer\Model\Session')->addSuccess(
                __('Your wish list has been shared.')
            );
            $this->_redirect('*/*', array('wishlist_id' => $wishlist->getId()));
        } catch (\Exception $e) {
            $translate->setTranslateInline(true);
            $this->_objectManager->get('Magento\Wishlist\Model\Session')->addError($e->getMessage());
            $this->_objectManager->get('Magento\Wishlist\Model\Session')
                ->setSharingForm($this->getRequest()->getPost());
            $this->_redirect('*/*/share');
        }
    }

    /**
     * Custom options download action
     * @return void
     */
    public function downloadCustomOptionAction()
    {
        $option = $this->_objectManager->create('Magento\Wishlist\Model\Item\Option')
            ->load($this->getRequest()->getParam('id'));

        if (!$option->getId()) {
            return $this->_forward('noroute');
        }

        $optionId = null;
        if (strpos($option->getCode(), \Magento\Catalog\Model\Product\Type\AbstractType::OPTION_PREFIX) === 0) {
            $optionId = str_replace(\Magento\Catalog\Model\Product\Type\AbstractType::OPTION_PREFIX, '', $option->getCode());
            if ((int)$optionId != $optionId) {
                return $this->_forward('noroute');
            }
        }
        $productOption = $this->_objectManager->create('Magento\Catalog\Model\Product\Option')->load($optionId);

        if (!$productOption
            || !$productOption->getId()
            || $productOption->getProductId() != $option->getProductId()
            || $productOption->getType() != 'file'
        ) {
            return $this->_forward('noroute');
        }

        try {
            $info      = unserialize($option->getValue());
            $filePath  = $this->_objectManager->get('Magento\App\Dir')->getDir() . $info['quote_path'];
            $secretKey = $this->getRequest()->getParam('key');

            if ($secretKey == $info['secret_key']) {
                $this->_fileResponseFactory->create($info['title'], array(
                    'value' => $filePath,
                    'type'  => 'filename'
                ));
            }

        } catch(\Exception $e) {
            $this->_forward('noroute');
        }
        exit(0);
    }
}
