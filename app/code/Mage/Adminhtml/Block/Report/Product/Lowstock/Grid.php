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
 * Adminhtml low stock products report grid block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Report_Product_Lowstock_Grid extends Mage_Backend_Block_Widget_Grid
{

    /**
     * @return Mage_Backend_Block_Widget_Grid
     */
    protected function _prepareCollection()
    {
        $website = $this->getRequest()->getParam('website');
        $group = $this->getRequest()->getParam('group');
        $store = $this->getRequest()->getParam('store');

        if ($website) {
            $storeIds = $this->_storeManager->getWebsite($website)->getStoreIds();
            $storeId = array_pop($storeIds);
        } else if ($group) {
            $storeIds = $this->_storeManager->getGroup($group)->getStoreIds();
            $storeId = array_pop($storeIds);
        } else if ($store) {
            $storeId = (int)$store;
        } else {
            $storeId = '';
        }

        /** @var $collection Mage_Reports_Model_Resource_Product_Lowstock_Collection  */
        $collection = Mage::getResourceModel('Mage_Reports_Model_Resource_Product_Lowstock_Collection')
            ->addAttributeToSelect('*')
            ->setStoreId($storeId)
            ->filterByIsQtyProductTypes()
            ->joinInventoryItem('qty')
            ->useManageStockFilter($storeId)
            ->useNotifyStockQtyFilter($storeId)
            ->setOrder('qty', Magento_Data_Collection::SORT_ORDER_ASC);

        if( $storeId ) {
            $collection->addStoreFilter($storeId);
        }

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }
}
