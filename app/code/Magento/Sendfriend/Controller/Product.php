<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sendfriend
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Email to a Friend Product Controller
 *
 * @category    Magento
 * @package     Magento_Sedfriend
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Sendfriend\Controller;

use Magento\App\Action\NotFoundException;

class Product extends \Magento\App\Action\Action
{
    /**
     * Core registry
     *
     * @var \Magento\Core\Model\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Magento\Core\App\Action\FormKeyValidator
     */
    protected $_formKeyValidator;

    /**
     * @param \Magento\App\Action\Context $context
     * @param \Magento\Core\Model\Registry $coreRegistry
     * @param \Magento\Core\App\Action\FormKeyValidator $formKeyValidator
     */
    public function __construct(
        \Magento\App\Action\Context $context,
        \Magento\Core\Model\Registry $coreRegistry,
        \Magento\Core\App\Action\FormKeyValidator $formKeyValidator
    ) {
        $this->_coreRegistry = $coreRegistry;
        $this->_formKeyValidator = $formKeyValidator;
        parent::__construct($context);
    }

    /**
     * Predispatch: check is enable module
     * If allow only for customer - redirect to login page
     *
     * @return \Magento\Sendfriend\Controller\Product
     * @throws NotFoundException
     */
    public function preDispatch()
    {
        parent::preDispatch();

        /* @var $helper \Magento\Sendfriend\Helper\Data */
        $helper = $this->_objectManager->get('Magento\Sendfriend\Helper\Data');
        /* @var $session \Magento\Customer\Model\Session */
        $session = $this->_objectManager->get('Magento\Customer\Model\Session');

        if (!$helper->isEnabled()) {
            throw new NotFoundException();
        }

        if (!$helper->isAllowForGuest() && !$session->authenticate($this)) {
            $this->setFlag('', self::FLAG_NO_DISPATCH, true);
            if ($this->getRequest()->getActionName() == 'sendemail') {
                $session->setBeforeAuthUrl($this->_objectManager
                        ->create('Magento\Core\Model\Url')
                        ->getUrl('*/*/send', array(
                            '_current' => true
                        )));
                $this->_objectManager->get('Magento\Catalog\Model\Session')
                    ->setSendfriendFormData($this->getRequest()->getPost());
            }
        }

        return $this;
    }

    /**
     * Initialize Product Instance
     *
     * @return \Magento\Catalog\Model\Product
     */
    protected function _initProduct()
    {
        $productId  = (int)$this->getRequest()->getParam('id');
        if (!$productId) {
            return false;
        }
        $product = $this->_objectManager->create('Magento\Catalog\Model\Product')
            ->load($productId);
        if (!$product->getId() || !$product->isVisibleInCatalog()) {
            return false;
        }

        $this->_coreRegistry->register('product', $product);
        return $product;
    }

    /**
     * Initialize send friend model
     *
     * @return \Magento\Sendfriend\Model\Sendfriend
     */
    protected function _initSendToFriendModel()
    {
        /** @var \Magento\HTTP\PhpEnvironment\RemoteAddress $remoteAddress */
        $remoteAddress = $this->_objectManager->get('Magento\HTTP\PhpEnvironment\RemoteAddress');

        /** @var \Magento\Core\Model\Cookie $cookie */
        $cookie = $this->_objectManager->get('Magento\Core\Model\Cookie');

        /** @var \Magento\Core\Model\StoreManagerInterface $store */
        $store = $this->_objectManager->get('Magento\Core\Model\StoreManagerInterface');

        /** @var \Magento\Sendfriend\Model\Sendfriend $model */
        $model  = $this->_objectManager->create('Magento\Sendfriend\Model\Sendfriend');
        $model->setRemoteAddr($remoteAddress->getRemoteAddress(true));
        $model->setCookie($cookie);
        $model->setWebsiteId($store->getStore()->getWebsiteId());

        $this->_coreRegistry->register('send_to_friend_model', $model);

        return $model;
    }

    /**
     * Show Send to a Friend Form
     *
     */
    public function sendAction()
    {
        $product    = $this->_initProduct();
        $model      = $this->_initSendToFriendModel();

        if (!$product) {
            $this->_forward('noroute');
            return;
        }
        /* @var $session \Magento\Catalog\Model\Session */
        $catalogSession = $this->_objectManager->get('Magento\Catalog\Model\Session');

        if ($model->getMaxSendsToFriend() && $model->isExceedLimit()) {
            $catalogSession->addNotice(
                __('You can\'t send messages more than %1 times an hour.', $model->getMaxSendsToFriend())
            );
        }

        $this->loadLayout();
        $this->getLayout()->initMessages('Magento\Catalog\Model\Session');

        $this->_eventManager->dispatch('sendfriend_product', array('product' => $product));
        $data = $catalogSession->getSendfriendFormData();
        if ($data) {
            $catalogSession->setSendfriendFormData(true);
            $block = $this->getLayout()->getBlock('sendfriend.send');
            if ($block) {
                $block->setFormData($data);
            }
        }

        $this->renderLayout();
    }

    /**
     * Send Email Post Action
     *
     */
    public function sendmailAction()
    {
        if (!$this->_formKeyValidator->validate($this->getRequest())) {
            return $this->_redirect('*/*/send', array('_current' => true));
        }

        $product    = $this->_initProduct();
        $model      = $this->_initSendToFriendModel();
        $data       = $this->getRequest()->getPost();

        if (!$product || !$data) {
            $this->_forward('noroute');
            return;
        }

        $categoryId = $this->getRequest()->getParam('cat_id', null);
        if ($categoryId) {
            $category = $this->_objectManager->create('Magento\Catalog\Model\Category')
                ->load($categoryId);
            $product->setCategory($category);
            $this->_coreRegistry->register('current_category', $category);
        }

        $model->setSender($this->getRequest()->getPost('sender'));
        $model->setRecipients($this->getRequest()->getPost('recipients'));
        $model->setProduct($product);

        /* @var $session \Magento\Catalog\Model\Session */
        $catalogSession = $this->_objectManager->get('Magento\Catalog\Model\Session');
        try {
            $validate = $model->validate();
            if ($validate === true) {
                $model->send();
                $catalogSession->addSuccess(__('The link to a friend was sent.'));
                $this->_redirectSuccess($product->getProductUrl());
                return;
            }
            else {
                if (is_array($validate)) {
                    foreach ($validate as $errorMessage) {
                        $catalogSession->addError($errorMessage);
                    }
                } else {
                    $catalogSession->addError(__('We found some problems with the data.'));
                }
            }
        } catch (\Magento\Core\Exception $e) {
            $catalogSession->addError($e->getMessage());
        } catch (\Exception $e) {
            $catalogSession->addException($e, __('Some emails were not sent.'));
        }

        // save form data
        $catalogSession->setSendfriendFormData($data);

        $this->_redirectError(
            $this->_objectManager
                ->create('Magento\Core\Model\Url')
                ->getUrl('*/*/send', array('_current' => true))
        );
    }
}
