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
class Mage_Adminhtml_Block_Report_Product_Lowstock_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
//    protected $_saveParametersInSession = true;

    public function __construct()
    {
        parent::__construct();
        $this->setId('gridLowstock');
        $this->setUseAjax(false);
    }

    protected function _prepareCollection()
    {
        if ($this->getRequest()->getParam('website')) {
            $storeIds = Mage::app()->getWebsite($this->getRequest()->getParam('website'))->getStoreIds();
            $storeId = array_pop($storeIds);
        } else if ($this->getRequest()->getParam('group')) {
            $storeIds = Mage::app()->getGroup($this->getRequest()->getParam('group'))->getStoreIds();
            $storeId = array_pop($storeIds);
        } else if ($this->getRequest()->getParam('store')) {
            $storeId = (int)$this->getRequest()->getParam('store');
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
            ->setOrder('qty', Varien_Data_Collection::SORT_ORDER_ASC);

        if( $storeId ) {
            $collection->addStoreFilter($storeId);
        }

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('name', array(
            'header'    =>Mage::helper('Mage_Reports_Helper_Data')->__('Product Name'),
            'sortable'  =>false,
            'index'     =>'name'
        ));

        $this->addColumn('sku', array(
            'header'    =>Mage::helper('Mage_Reports_Helper_Data')->__('Product SKU'),
            'sortable'  =>false,
            'index'     =>'sku'
        ));

        $this->addColumn('qty', array(
            'header'    =>Mage::helper('Mage_Reports_Helper_Data')->__('Stock Qty'),
            'width'     =>'215px',
            'align'     =>'right',
            'sortable'  =>false,
            'filter'    =>'Mage_Adminhtml_Block_Widget_Grid_Column_Filter_Range',
            'index'     =>'qty',
            'type'      =>'number'
        ));

        $this->addExportType('*/*/exportLowstockCsv', Mage::helper('Mage_Reports_Helper_Data')->__('CSV'));
        $this->addExportType('*/*/exportLowstockExcel', Mage::helper('Mage_Reports_Helper_Data')->__('Excel XML'));

        return parent::_prepareColumns();
    }
}
