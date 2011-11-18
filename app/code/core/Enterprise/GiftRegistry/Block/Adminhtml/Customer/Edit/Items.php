<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_GiftRegistry
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml customer view gift registry items block
 */
class Enterprise_GiftRegistry_Block_Adminhtml_Customer_Edit_Items
    extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('giftregistry_customer_items_grid');
        $this->setSortable(false);
        $this->setPagerVisibility(false);
        $this->setFilterVisibility(false);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('Enterprise_GiftRegistry_Model_Item')->getCollection()
            ->addRegistryFilter($this->getEntity()->getId());

        $collection->updateItemAttributes();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('product_id', array(
            'header' => Mage::helper('Enterprise_GiftRegistry_Helper_Data')->__('Product ID'),
            'index'  => 'product_id',
            'type'   => 'number',
            'width'  => '120px'
        ));

        $this->addColumn('name', array(
            'header' => Mage::helper('Enterprise_GiftRegistry_Helper_Data')->__('Product Name'),
            'index'  => 'product_name'
        ));

        $this->addColumn('sku', array(
            'header' => Mage::helper('Enterprise_GiftRegistry_Helper_Data')->__('Product SKU'),
            'index'  => 'sku',
            'width'  => '200px'
        ));

        $this->addColumn('price', array(
            'header' => Mage::helper('Enterprise_GiftRegistry_Helper_Data')->__('Price'),
            'index'  => 'price',
            'type'  => 'currency',
            'width' => '120px',
            'currency_code' => (string) Mage::getStoreConfig(Mage_Directory_Model_Currency::XML_PATH_CURRENCY_BASE),
        ));

        $this->addColumn('qty', array(
            'header'   => Mage::helper('Enterprise_GiftRegistry_Helper_Data')->__('Requested Quantity'),
            'index'    => 'qty',
            'width'    => '120px',
            'renderer' => 'Enterprise_GiftRegistry_Block_Adminhtml_Widget_Grid_Column_Renderer_Qty'
        ));

        $this->addColumn('qty_fulfilled', array(
            'header' => Mage::helper('Enterprise_GiftRegistry_Helper_Data')->__('Fulfilled Quantity'),
            'index'  => 'qty_fulfilled',
            'type'   => 'number',
            'width'  => '120px'
        ));

        $this->addColumn('note', array(
            'header' => Mage::helper('Enterprise_GiftRegistry_Helper_Data')->__('Note'),
            'index'  => 'note',
            'width'  => '120px'
        ));

        $this->addColumn('action', array(
            'header' => Mage::helper('Enterprise_GiftRegistry_Helper_Data')->__('Action'),
            'width'  => '120px',
            'options'   => array(
                 0 => Mage::helper('Enterprise_GiftRegistry_Helper_Data')->__('Action'),
                'update' => Mage::helper('Enterprise_GiftRegistry_Helper_Data')->__('Update Qty'),
                'remove' => Mage::helper('Enterprise_GiftRegistry_Helper_Data')->__('Remove Item')
            ),
            'renderer' => 'Enterprise_GiftRegistry_Block_Adminhtml_Widget_Grid_Column_Renderer_Action'
        ));

        return parent::_prepareColumns();
    }

    /**
     * Return grid row url
     *
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/catalog_product/edit', array('id' => $row->getProductId()));
    }

    /**
     * Return gift registry entity object
     *
     * @return Enterprise_GiftRegistry_Model_Entity
     */
    public function getEntity()
    {
        return Mage::registry('current_giftregistry_entity');
    }
}
