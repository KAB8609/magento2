<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_GoogleShopping
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Google Shopping Observer
 *
 * @category    Mage
 * @package     Mage_GoogleShopping
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_GoogleShopping_Model_Observer
{
    /**
     * Update product item in Google Content
     *
     * @param Varien_Object $observer
     * @return Mage_GoogleShopping_Model_Observer
     */
    public function saveProductItem($observer)
    {
        $product = $observer->getEvent()->getProduct();
        $items = $this->_getItemsCollection($product);

        try {
            Mage::getModel('Mage_GoogleShopping_Model_MassOperations')
                ->synchronizeItems($items);
        } catch (Zend_Gdata_App_CaptchaRequiredException $e) {
            Mage::getSingleton('Mage_Adminhtml_Model_Session')
                ->addError('Cannot update Google Content Item. Google requires CAPTCHA.');
        }

        return $this;
    }

    /**
     * Delete product item from Google Content
     *
     * @param Varien_Object $observer
     * @return Mage_GoogleShopping_Model_Observer
     */
    public function deleteProductItem($observer)
    {
        $product = $observer->getEvent()->getProduct();
        $items = $this->_getItemsCollection($product);

        try {
            Mage::getModel('Mage_GoogleShopping_Model_MassOperations')
                ->deleteItems($items);
        } catch (Zend_Gdata_App_CaptchaRequiredException $e) {
            Mage::getSingleton('Mage_Adminhtml_Model_Session')
                ->addError('Cannot delete Google Content Item. Google requires CAPTCHA.');
        }

        return $this;
    }

    /**
     * Get items which are available for update/delete when product is saved
     *
     * @param Mage_Catalog_Model_Product $product
     * @return Mage_GoogleShopping_Model_Resource_Item_Collection
     */
    protected function _getItemsCollection($product)
    {
        $items = Mage::getResourceModel('Mage_GoogleShopping_Model_Resource_Item_Collection')
            ->addProductFilterId($product->getId());
        if ($product->getStoreId()) {
            $items->addStoreFilter($product->getStoreId());
        }

        foreach ($items as $item) {
            if (!Mage::getStoreConfigFlag('google/googleshopping/observed', $item->getStoreId())) {
                $items->removeItemByKey($item->getId());
            }
        }

        return $items;
    }

    /**
     * Check if synchronize process is finished and generate notification message
     *
     * @param  Varien_Event_Observer $observer
     * @return Mage_GoogleShopping_Model_Observer
     */
    public function checkSynchronizationOperations(Varien_Event_Observer $observer)
    {
        $flag = Mage::getSingleton('googleshopping/flag')->loadSelf();
        if ($flag->isExpired()) {
            Mage::getModel('adminnotification/inbox')->addMajor(
                Mage::helper('googleshopping')->__('Google Shopping operation has expired.'),
                Mage::helper('googleshopping')->__('One or more google shopping synchronization operations failed because of timeout.')
            );
            $flag->unlock();
        }
        return $this;
    }
}
