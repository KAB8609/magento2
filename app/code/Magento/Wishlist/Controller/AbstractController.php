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
 * Wishlist Abstract Front Controller Action
 *
 * @category    Magento
 * @package     Magento_Wishlist
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Wishlist\Controller;

abstract class AbstractController extends \Magento\App\Action\Action
{
    /**
     * Filter to convert localized values to internal ones
     * @var \Zend_Filter_LocalizedToNormalized
     */
    protected $_localFilter = null;

    /**
     * Processes localized qty (entered by user at frontend) into internal php format
     *
     * @param string $qty
     * @return float|int|null
     */
    protected function _processLocalizedQty($qty)
    {
        if (!$this->_localFilter) {
            $this->_localFilter = new \Zend_Filter_LocalizedToNormalized(
                array('locale' => $this->_objectManager->get('Magento\Core\Model\LocaleInterface')->getLocaleCode())
            );
        }
        $qty = $this->_localFilter->filter((float)$qty);
        if ($qty < 0) {
            $qty = null;
        }
        return $qty;
    }

    /**
     * Retrieve current wishlist instance
     *
     * @return \Magento\Wishlist\Model\Wishlist|false
     */
    abstract protected function _getWishlist();

    /**
     * Add all items from wishlist to shopping cart
     *
     */
    public function allcartAction()
    {
        $wishlist   = $this->_getWishlist();
        if (!$wishlist) {
            $this->_forward('noroute');
            return ;
        }
        $isOwner    = $wishlist->isOwner($this->_objectManager->get('Magento\Customer\Model\Session')->getCustomerId());

        $messages   = array();
        $addedItems = array();
        $notSalable = array();
        $hasOptions = array();

        $cart       = $this->_objectManager->get('Magento\Checkout\Model\Cart');
        $collection = $wishlist->getItemCollection()
                ->setVisibilityFilter();

        $qtys = $this->getRequest()->getParam('qty');
        foreach ($collection as $item) {
            /** @var \Magento\Wishlist\Model\Item */
            try {
                $disableAddToCart = $item->getProduct()->getDisableAddToCart();
                $item->unsProduct();

                // Set qty
                if (isset($qtys[$item->getId()])) {
                    $qty = $this->_processLocalizedQty($qtys[$item->getId()]);
                    if ($qty) {
                        $item->setQty($qty);
                    }
                }
                $item->getProduct()->setDisableAddToCart($disableAddToCart);
                // Add to cart
                if ($item->addToCart($cart, $isOwner)) {
                    $addedItems[] = $item->getProduct();
                }

            } catch (\Magento\Core\Exception $e) {
                if ($e->getCode() == \Magento\Wishlist\Model\Item::EXCEPTION_CODE_NOT_SALABLE) {
                    $notSalable[] = $item;
                } else if ($e->getCode() == \Magento\Wishlist\Model\Item::EXCEPTION_CODE_HAS_REQUIRED_OPTIONS) {
                    $hasOptions[] = $item;
                } else {
                    $messages[] = __('%1 for "%2".', trim($e->getMessage(), '.'), $item->getProduct()->getName());
                }
            } catch (\Exception $e) {
                $this->_objectManager->get('Magento\Logger')->logException($e);
                $messages[] = __('We cannot add this item to your shopping cart.');
            }
        }

        if ($isOwner) {
            $indexUrl = $this->_objectManager->get('Magento\Wishlist\Helper\Data')->getListUrl($wishlist->getId());
        } else {
            $indexUrl = $this->_objectManager->create('Magento\Core\Model\Url')
                ->getUrl('wishlist/shared', array('code' => $wishlist->getSharingCode()));
        }
        if ($this->_objectManager->get('Magento\Checkout\Helper\Cart')->getShouldRedirectToCart()) {
            $redirectUrl = $this->_objectManager->get('Magento\Checkout\Helper\Cart')->getCartUrl();
        } else if ($this->_redirect->getRefererUrl()) {
            $redirectUrl = $this->_redirect->getRefererUrl();
        } else {
            $redirectUrl = $indexUrl;
        }

        if ($notSalable) {
            $products = array();
            foreach ($notSalable as $item) {
                $products[] = '"' . $item->getProduct()->getName() . '"';
            }
            $messages[] = __('We couldn\'t add the following product(s) to the shopping cart: %1.', join(', ', $products));
        }

        if ($hasOptions) {
            $products = array();
            foreach ($hasOptions as $item) {
                $products[] = '"' . $item->getProduct()->getName() . '"';
            }
            $messages[] = __('Product(s) %1 have required options. Each product can only be added individually.', join(', ', $products));
        }

        if ($messages) {
            $isMessageSole = (count($messages) == 1);
            if ($isMessageSole && count($hasOptions) == 1) {
                $item = $hasOptions[0];
                if ($isOwner) {
                    $item->delete();
                }
                $redirectUrl = $item->getProductUrl();
            } else {
                $wishlistSession = $this->_objectManager->get('Magento\Wishlist\Model\Session');
                foreach ($messages as $message) {
                    $wishlistSession->addError($message);
                }
                $redirectUrl = $indexUrl;
            }
        }

        if ($addedItems) {
            // save wishlist model for setting date of last update
            try {
                $wishlist->save();
            }
            catch (\Exception $e) {
                $this->_objectManager->get('Magento\Wishlist\Model\Session')
                    ->addError(__('We can\'t update wish list.'));
                $redirectUrl = $indexUrl;
            }

            $products = array();
            foreach ($addedItems as $product) {
                $products[] = '"' . $product->getName() . '"';
            }

            $this->_objectManager->get('Magento\Checkout\Model\Session')->addSuccess(
                __('%1 product(s) have been added to shopping cart: %2.', count($addedItems), join(', ', $products))
            );
        }
        // save cart and collect totals
        $cart->save()->getQuote()->collectTotals();

        $this->_objectManager->get('Magento\Wishlist\Helper\Data')->calculate();

        $this->getResponse()->setRedirect($redirectUrl);
    }
}