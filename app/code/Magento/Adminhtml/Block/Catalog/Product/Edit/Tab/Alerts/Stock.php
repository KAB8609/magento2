<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Sign up for an alert when the product price changes grid
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Adminhtml_Block_Catalog_Product_Edit_Tab_Alerts_Stock extends Magento_Adminhtml_Block_Widget_Grid
{
    protected function _construct()
    {
        parent::_construct();

        $this->setId('alertStock');
        $this->setDefaultSort('add_date');
        $this->setDefaultSort('DESC');
        $this->setUseAjax(true);
        $this->setFilterVisibility(false);
        $this->setEmptyText(Mage::helper('Magento_Catalog_Helper_Data')->__('There are no customers for this alert.'));
    }

    protected function _prepareCollection()
    {
        $productId = $this->getRequest()->getParam('id');
        $websiteId = 0;
        if ($store = $this->getRequest()->getParam('store')) {
            $websiteId = Mage::app()->getStore($store)->getWebsiteId();
        }
        if (Mage::helper('Magento_Catalog_Helper_Data')->isModuleEnabled('Mage_ProductAlert')) {
            $collection = Mage::getModel('Mage_ProductAlert_Model_Stock')
                ->getCustomerCollection()
                ->join($productId, $websiteId);
            $this->setCollection($collection);
        }
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('firstname', array(
            'header'    => Mage::helper('Magento_Catalog_Helper_Data')->__('First Name'),
            'index'     => 'firstname',
        ));

        $this->addColumn('lastname', array(
            'header'    => Mage::helper('Magento_Catalog_Helper_Data')->__('Last Name'),
            'index'     => 'lastname',
        ));

        $this->addColumn('email', array(
            'header'    => Mage::helper('Magento_Catalog_Helper_Data')->__('Email'),
            'index'     => 'email',
        ));

        $this->addColumn('add_date', array(
            'header'    => Mage::helper('Magento_Catalog_Helper_Data')->__('Subscribe Date'),
            'index'     => 'add_date',
            'type'      => 'date'
        ));

        $this->addColumn('send_date', array(
            'header'    => Mage::helper('Magento_Catalog_Helper_Data')->__('Last Notified'),
            'index'     => 'send_date',
            'type'      => 'date'
        ));

        $this->addColumn('send_count', array(
            'header'    => Mage::helper('Magento_Catalog_Helper_Data')->__('Send Count'),
            'index'     => 'send_count',
        ));

        return parent::_prepareColumns();
    }

    public function getGridUrl()
    {
        $productId = $this->getRequest()->getParam('id');
        $storeId   = $this->getRequest()->getParam('store', 0);
        if ($storeId) {
            $storeId = Mage::app()->getStore($storeId)->getId();
        }
        return $this->getUrl('*/catalog_product/alertsStockGrid', array(
            'id'    => $productId,
            'store' => $storeId
        ));
    }
}
