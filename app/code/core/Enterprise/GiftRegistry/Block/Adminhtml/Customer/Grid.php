<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_GiftRegistry
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Enterprise_GiftRegistry_Block_Adminhtml_Customer_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Set default sort
     */
    protected function _construct()
    {
        parent::_construct();

        $this->setId('customerGrid');
        $this->setUseAjax(true);
        $this->setDefaultSort('registry_id');
        $this->setDefaultDir('ASC');
    }

    /**
     * Instantiate and prepare collection
     *
     * @return Enterprise_GiftRegistry_Block_Adminhtml_Giftregistry_Customer_Grid
     */
    protected function _prepareCollection()
    {
        /** @var $collection Enterprise_GiftRegistry_Model_Resource_Entity_Collection */
        $collection = Mage::getModel('Enterprise_GiftRegistry_Model_Entity')->getCollection();
        $collection->filterByCustomerId($this->getRequest()->getParam('id'));
        $collection->addRegistryInfo();

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Prepare columns for grid
     *
     * @return Enterprise_GiftRegistry_Block_Adminhtml_Giftregistry_Customer_Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('title', array(
            'header' => Mage::helper('Enterprise_GiftRegistry_Helper_Data')->__('Event'),
            'index'  => 'title'
        ));

        $this->addColumn('registrants', array(
            'header' => Mage::helper('Enterprise_GiftRegistry_Helper_Data')->__('Recipients'),
            'index'  => 'registrants'
        ));

        $this->addColumn('event_date', array(
            'header'  => Mage::helper('Enterprise_GiftRegistry_Helper_Data')->__('Event Date'),
            'index'   => 'event_date',
            'type'    => 'date',
            'default' => '--'
        ));

        $this->addColumn('qty', array(
            'header' => Mage::helper('Enterprise_GiftRegistry_Helper_Data')->__('Total Items'),
            'index'  => 'qty',
            'type'   => 'number'
        ));

        $this->addColumn('qty_fulfilled', array(
            'header' => Mage::helper('Enterprise_GiftRegistry_Helper_Data')->__('Items Fulfilled'),
            'index'  => 'qty_fulfilled',
            'type'   => 'number',
        ));

        $this->addColumn('qty_remaining', array(
            'header' => Mage::helper('Enterprise_GiftRegistry_Helper_Data')->__('Items Remaining'),
            'index'  => 'qty_remaining',
            'type'   => 'number'
        ));

        $this->addColumn('is_public', array(
            'header'  => Mage::helper('Enterprise_GiftRegistry_Helper_Data')->__('Is Public'),
            'index'   => 'is_public',
            'type'    => 'options',
            'options' => array(
                '0' => Mage::helper('Enterprise_GiftRegistry_Helper_Data')->__('No'),
                '1' => Mage::helper('Enterprise_GiftRegistry_Helper_Data')->__('Yes'),
            )
        ));

        $this->addColumn('website_id', array(
            'header' => Mage::helper('Enterprise_GiftRegistry_Helper_Data')->__('Website'),
            'index'  => 'website_id',
            'type'   => 'options',
            'options' => Mage::getSingleton('Mage_Core_Model_System_Store')->getWebsiteOptionHash()
        ));

        return parent::_prepareColumns();
    }

    /**
     * Retrieve row url
     *
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array(
            'id'       => $row->getId(),
            'customer' => $row->getCustomerId()
        ));
    }

    /**
     * Retrieve grid URL
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current'=>true));
    }
}
