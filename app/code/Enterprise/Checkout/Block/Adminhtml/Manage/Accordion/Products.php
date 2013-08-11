<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Checkout
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Accordion grid for catalog salable products
 *
 * @category   Enterprise
 * @package    Enterprise_Checkout
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Checkout_Block_Adminhtml_Manage_Accordion_Products
    extends Enterprise_Checkout_Block_Adminhtml_Manage_Accordion_Abstract
{
    /**
     * Block initializing, grid parameters
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('source_products');
        $this->setDefaultSort('entity_id');
        $this->setPagerVisibility(true);
        $this->setFilterVisibility(true);
        $this->setHeaderText(__('Products'));
    }

    /**
     * Return custom object name for js grid
     *
     * @return string
     */
    public function getJsObjectName()
    {
        return 'productsGrid';
    }

    /**
     * Return items collection
     *
     * @return Mage_Core_Model_Resource_Db_Collection_Abstract
     */
    public function getItemsCollection()
    {
        if (!$this->hasData('items_collection')) {
            $attributes = Mage::getSingleton('Mage_Catalog_Model_Config')->getProductAttributes();
            $collection = Mage::getModel('Mage_Catalog_Model_Product')->getCollection()
                ->setStore($this->_getStore())
                ->addAttributeToSelect($attributes)
                ->addAttributeToSelect('sku')
                ->addAttributeToFilter(
                    'type_id',
                    array_keys(
                        Mage::getConfig()->getNode('adminhtml/sales/order/create/available_product_types')->asArray()
                    )
                )->addAttributeToFilter('status', Mage_Catalog_Model_Product_Status::STATUS_ENABLED)
                ->addStoreFilter($this->_getStore());
            Mage::getSingleton('Mage_CatalogInventory_Model_Stock_Status')->addIsInStockFilterToCollection($collection);
            $this->setData('items_collection', $collection);
        }
        return $this->getData('items_collection');
    }

    /**
     * Prepare Grid columns
     *
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('entity_id', array(
            'header'    => __('ID'),
            'sortable'  => true,
            'width'     => '60',
            'index'     => 'entity_id'
        ));

        $this->addColumn('name', array(
            'header'    => __('Product'),
            'renderer'  => 'Mage_Adminhtml_Block_Sales_Order_Create_Search_Grid_Renderer_Product',
            'index'     => 'name'
        ));

        $this->addColumn('sku', array(
            'header'    => __('SKU'),
            'width'     => '80',
            'index'     => 'sku'
        ));

        $this->addColumn('price', array(
            'header'    => __('Price'),
            'type'      => 'currency',
            'column_css_class' => 'price',
            'currency_code' => $this->_getStore()->getCurrentCurrencyCode(),
            'rate' => $this->_getStore()->getBaseCurrency()->getRate($this->_getStore()->getCurrentCurrencyCode()),
            'index'     => 'price',
            'renderer'  => 'Mage_Adminhtml_Block_Sales_Order_Create_Search_Grid_Renderer_Price'
        ));

        $this->_addControlColumns();

        return $this;
    }

    /**
     * Custom products grid search callback
     *
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $this->getChildBlock('search_button')->setOnclick('checkoutObj.searchProducts()');
        return $this;
    }

    /**
     * Search by selected products
     *
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _addColumnFilterToCollection($column)
    {
        // Set custom filter for in product flag
        if ($column->getId() == 'in_products') {
            $productIds = $this->_getSelectedProducts();
            if (!$productIds) {
                $productIds = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('entity_id', array('in' => $productIds));
            } elseif($productIds) {
                $this->getCollection()->addFieldToFilter('entity_id', array('nin'=>$productIds));
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }

    /**
     * Return array of selected product ids from request
     *
     * @return array
     */
    protected function _getSelectedProducts()
    {
        if ($this->getRequest()->getPost('source')) {
            $source = Mage::helper('Mage_Core_Helper_Data')->jsonDecode($this->getRequest()->getPost('source'));
            if (isset($source['source_products']) && is_array($source['source_products'])) {
                return array_keys($source['source_products']);
            }
        }
        return false;
    }

    /**
     * Return grid URL for sorting and filtering
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/products', array('_current'=>true));
    }

    /**
     * Add columns with controls to manage added products and their quantity
     *
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _addControlColumns()
    {
        parent::_addControlColumns();
        $this->getColumn('in_products')->setHeader(" ");
    }

    /*
     * Add custom options to product collection
     *
     * return Enterprise_Checkout_Block_Adminhtml_Manage_Accordion_Products
     */
    protected function _afterLoadCollection()
    {
        $this->getCollection()->addOptionsToResult();
        return parent::_afterLoadCollection();
    }
}
