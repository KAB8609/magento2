<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Catalog composite product configuration controller
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Controller_Customer_Wishlist_Product_Composite_Wishlist
    extends Mage_Adminhtml_Controller_Action
{
     /**
     * Wishlist we're working with
     *
     * @var Mage_Wishlist_Model_Wishlist
     */
    protected $_wishlist = null;

    /**
     * Wishlist item we're working with
     *
     * @var Mage_Wishlist_Model_Wishlist
     */
    protected $_wishlistItem = null;

    /**
     * Loads wishlist and wishlist item
     *
     * @return Mage_Adminhtml_Controller_Customer_Wishlist_Product_Composite_Wishlist
     */
    protected function _initData()
    {
        $wishlistItemId = (int) $this->getRequest()->getParam('id');
        if (!$wishlistItemId) {
            Mage::throwException(__('No wishlist item ID is defined.'));
        }

        /* @var $wishlistItem Mage_Wishlist_Model_Item */
        $wishlistItem = Mage::getModel('Mage_Wishlist_Model_Item')
            ->loadWithOptions($wishlistItemId);

        if (!$wishlistItem->getWishlistId()) {
            Mage::throwException(__('Please load the wish list item.'));
        }

        $this->_wishlist = Mage::getModel('Mage_Wishlist_Model_Wishlist')
            ->load($wishlistItem->getWishlistId());

        $this->_wishlistItem = $wishlistItem;

        return $this;
    }

    /**
     * Ajax handler to response configuration fieldset of composite product in customer's wishlist
     *
     * @return Mage_Adminhtml_Controller_Customer_Wishlist_Product_Composite_Wishlist
     */
    public function configureAction()
    {
        $configureResult = new Magento_Object();
        try {
            $this->_initData();

            $configureResult->setProductId($this->_wishlistItem->getProductId());
            $configureResult->setBuyRequest($this->_wishlistItem->getBuyRequest());
            $configureResult->setCurrentStoreId($this->_wishlistItem->getStoreId());
            $configureResult->setCurrentCustomerId($this->_wishlist->getCustomerId());

            $configureResult->setOk(true);
        } catch (Exception $e) {
            $configureResult->setError(true);
            $configureResult->setMessage($e->getMessage());
        }

        /* @var $helper Mage_Adminhtml_Helper_Catalog_Product_Composite */
        $helper = Mage::helper('Mage_Adminhtml_Helper_Catalog_Product_Composite');
        $helper->renderConfigureResult($this, $configureResult);

        return $this;
    }

    /**
     * IFrame handler for submitted configuration for wishlist item
     *
     * @return false
     */
    public function updateAction()
    {
        // Update wishlist item
        $updateResult = new Magento_Object();
        try {
            $this->_initData();

            $buyRequest = new Magento_Object($this->getRequest()->getParams());

            $this->_wishlist
                ->updateItem($this->_wishlistItem->getId(), $buyRequest)
                ->save();

            $updateResult->setOk(true);
        } catch (Exception $e) {
            $updateResult->setError(true);
            $updateResult->setMessage($e->getMessage());
        }
        $updateResult->setJsVarName($this->getRequest()->getParam('as_js_varname'));
        Mage::getSingleton('Mage_Adminhtml_Model_Session')->setCompositeProductResult($updateResult);
        $this->_redirect('*/catalog_product/showUpdateResult');

        return false;
    }

    /**
     * Check the permission to Manage Customers
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Mage_Customer::manage');
    }
}
