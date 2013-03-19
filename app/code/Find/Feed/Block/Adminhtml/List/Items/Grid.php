<?php
/**
 * {license_notice}
 *
 * @category
 * @package     _home
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * TheFind feed product grid container
 *
 * @category    Find
 * @package     Find_Feed
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Find_Feed_Block_Adminhtml_List_Items_Grid  extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Initialize grid settings
     *
     */
    protected function _construct()
    {
        parent::_construct();

        $this->setId('find_feed_list_items');
        $this->setDefaultSort('id');
        $this->setUseAjax(true);
    }

    /**
     * Return Current work store
     *
     * @return Mage_Core_Model_Store
     */
    protected function _getStore()
    {
        return Mage::app()->getStore();
    }

    /**
     * Prepare product collection
     *
     * @return Find_Feed_Block_Adminhtml_List_Items_Grid
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('Mage_Catalog_Model_Product')->getCollection()
            ->setStore($this->_getStore())
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('sku')
            ->addAttributeToSelect('price')
            ->addAttributeToSelect('attribute_set_id')
            ->addAttributeToSelect('is_imported');
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * Prepare grid columns
     *
     * @return Find_Feed_Block_Adminhtml_List_Items_Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('id', array(
            'header'            => Mage::helper('Find_Feed_Helper_Data')->__('ID'),
            'sortable'          => true,
            'width'             => '60px',
            'index'             => 'entity_id'
        ));

        $this->addColumn('name', array(
            'header'            => Mage::helper('Find_Feed_Helper_Data')->__('Product Name'),
            'index'             => 'name',
            'column_css_class'  => 'name'
        ));

        $this->addColumn('type', array(
            'header'            => Mage::helper('Find_Feed_Helper_Data')->__('Type'),
            'width'             => '60px',
            'index'             => 'type_id',
            'type'              => 'options',
            'options'           => Mage::getSingleton('Mage_Catalog_Model_Product_Type')->getOptionArray(),
        ));

        $entityTypeId =  Mage::helper('Find_Feed_Helper_Data')->getProductEntityType();
        $sets           = Mage::getResourceModel('Mage_Eav_Model_Resource_Entity_Attribute_Set_Collection')
            ->setEntityTypeFilter($entityTypeId)
            ->load()
            ->toOptionHash();

        $this->addColumn('set_name', array(
            'header'            => Mage::helper('Find_Feed_Helper_Data')->__('Attrib. Set Name'),
            'width'             => '100px',
            'index'             => 'attribute_set_id',
            'type'              => 'options',
            'options'           => $sets,
        ));

        $this->addColumn('sku', array(
            'header'            => Mage::helper('Find_Feed_Helper_Data')->__('SKU'),
            'width'             => '80px',
            'index'             => 'sku',
            'column_css_class'  => 'sku'
        ));

        $this->addColumn('price', array(
            'header'            => Mage::helper('Find_Feed_Helper_Data')->__('Price'),
            'align'             => 'center',
            'type'              => 'currency',
            'currency_code'     => $this->_getStore()->getCurrentCurrencyCode(),
            'rate'              => $this->_getStore()->getBaseCurrency()->getRate(
                $this->_getStore()->getCurrentCurrencyCode()
            ),
            'index'             => 'price'
        ));

        $source = Mage::getModel('Mage_Eav_Model_Entity_Attribute_Source_Boolean');
        $isImportedOptions = $source->getOptionArray();

        $this->addColumn('is_imported', array(
            'header'    => Mage::helper('Find_Feed_Helper_Data')->__('In Feed'),
            'width'     => '100px',
            'index'     => 'is_imported',
            'type'      => 'options',
            'options'   => $isImportedOptions
        ));

        return parent::_prepareColumns();
    }

    /**
     * Prepare massaction
     *
     * @return Find_Feed_Block_Adminhtml_List_Items_Grid
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('entity_id');
        $this->getMassactionBlock()->setFormFieldName('item_id');

        $this->getMassactionBlock()->addItem('enable', array(
            'label'         => Mage::helper('Find_Feed_Helper_Data')->__('Publish'),
            'url'           => $this->getUrl('*/items_grid/massEnable'),
            'selected'      => true,
        ));
        $this->getMassactionBlock()->addItem('disable', array(
            'label'         => Mage::helper('Find_Feed_Helper_Data')->__('Not publish'),
            'url'           => $this->getUrl('*/items_grid/massDisable'),
        ));

        return $this;
    }

    /**
     * Return Grid URL for AJAX query
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current'=>true));
    }
}
