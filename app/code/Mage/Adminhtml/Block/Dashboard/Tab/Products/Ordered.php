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
 * Adminhtml dashboard most ordered products grid
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Adminhtml_Block_Dashboard_Tab_Products_Ordered extends Mage_Adminhtml_Block_Dashboard_Grid
{

    protected function _construct()
    {
        parent::_construct();
        $this->setId('productsOrderedGrid');
    }

    protected function _prepareCollection()
    {
        if (!Mage::helper('Mage_Core_Helper_Data')->isModuleEnabled('Mage_Sales')) {
            return $this;
        }
        if ($this->getParam('website')) {
            $storeIds = Mage::app()->getWebsite($this->getParam('website'))->getStoreIds();
            $storeId = array_pop($storeIds);
        } else if ($this->getParam('group')) {
            $storeIds = Mage::app()->getGroup($this->getParam('group'))->getStoreIds();
            $storeId = array_pop($storeIds);
        } else {
            $storeId = (int)$this->getParam('store');
        }

        $collection = Mage::getResourceModel('Mage_Sales_Model_Resource_Report_Bestsellers_Collection')
            ->setModel('Mage_Catalog_Model_Product')
            ->addStoreFilter($storeId)
        ;

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {

        $this->addColumn('name', array(
            'header'    => Mage::helper('Mage_Sales_Helper_Data')->__('Product Name'),
            'sortable'  => false,
            'index'     => 'product_name'
        ));

        $this->addColumn('price', array(
            'header'    => Mage::helper('Mage_Sales_Helper_Data')->__('Price'),
            'width'     => '120px',
            'type'      => 'currency',
            'currency_code' => (string) Mage::app()->getStore((int)$this->getParam('store'))->getBaseCurrencyCode(),
            'sortable'  => false,
            'index'     => 'product_price'
        ));

        $this->addColumn('ordered_qty', array(
            'header'    => Mage::helper('Mage_Sales_Helper_Data')->__('Quantity Ordered'),
            'width'     => '120px',
            'align'     => 'right',
            'sortable'  => false,
            'index'     => 'qty_ordered',
            'type'      => 'number'
        ));

        $this->setFilterVisibility(false);
        $this->setPagerVisibility(false);

        return parent::_prepareColumns();
    }

    /*
     * Returns row url to show in admin dashboard
     * $row is bestseller row wrapped in Product model
     *
     * @param Mage_Catalog_Model_Product $row
     *
     * @return string
     */
    public function getRowUrl($row)
    {
        // getId() would return id of bestseller row, and product id we get by getProductId()
        $productId = $row->getProductId();

        // No url is possible for non-existing products
        if (!$productId) {
            return '';
        }

        $params = array('id' => $productId);
        if ($this->getRequest()->getParam('store')) {
            $params['store'] = $this->getRequest()->getParam('store');
        }
        return $this->getUrl('*/catalog_product/edit', $params);
    }
}