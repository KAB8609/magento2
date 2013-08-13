<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Rma
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Admin RMA create order grid block
 *
 * @category    Enterprise
 * @package     Enterprise_Rma
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Enterprise_Rma_Block_Adminhtml_Rma_Create_Order_Grid extends Magento_Adminhtml_Block_Widget_Grid
{

    /**
     * Block constructor
     */
    public function _construct()
    {
        parent::_construct();
        $this->setId('enterprise_rma_rma_create_order_grid');
        $this->setDefaultSort('entity_id');
    }

    /**
     * Prepare grid collection object
     *
     * @return Enterprise_Rma_Block_Adminhtml_Rma_Create_Order_Grid
     */
    protected function _prepareCollection()
    {
        /** @var $collection Mage_Sales_Model_Resource_Order_Grid_Collection */
        $collection = Mage::getResourceModel('Mage_Sales_Model_Resource_Order_Grid_Collection')
            ->setOrder('entity_id');
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Prepare columns
     *
     * @return Magento_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('real_order_id', array(
            'header' => Mage::helper('Mage_Sales_Helper_Data')->__('Order'),
            'width' => '80px',
            'type' => 'text',
            'index' => 'increment_id',
        ));

        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('store_id', array(
                'header' => Mage::helper('Mage_Sales_Helper_Data')->__('Purchase Point'),
                'index' => 'store_id',
                'type' => 'store',
                'store_view' => true,
                'display_deleted' => true,
            ));
        }

        $this->addColumn('created_at', array(
            'header' => Mage::helper('Mage_Sales_Helper_Data')->__('Purchase Date'),
            'index' => 'created_at',
            'type' => 'datetime',
            'width' => '100px',
        ));

        $this->addColumn('billing_name', array(
            'header' => Mage::helper('Mage_Sales_Helper_Data')->__('Bill-to Name'),
            'index' => 'billing_name',
        ));

        $this->addColumn('shipping_name', array(
            'header' => Mage::helper('Mage_Sales_Helper_Data')->__('Ship-to Name'),
            'index' => 'shipping_name',
        ));

        $this->addColumn('base_grand_total', array(
            'header' => Mage::helper('Mage_Sales_Helper_Data')->__('Grand Total (Base)'),
            'index' => 'base_grand_total',
            'type' => 'currency',
            'currency' => 'base_currency_code',
        ));

        $this->addColumn('grand_total', array(
            'header' => Mage::helper('Mage_Sales_Helper_Data')->__('Grand Total (Purchased)'),
            'index' => 'grand_total',
            'type' => 'currency',
            'currency' => 'order_currency_code',
        ));

        $this->addColumn('status', array(
            'header' => Mage::helper('Mage_Sales_Helper_Data')->__('Status'),
            'index' => 'status',
            'type' => 'options',
            'width' => '70px',
            'options' => Mage::getSingleton('Mage_Sales_Model_Order_Config')->getStatuses(),
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
        return $this->getUrl('*/*/new', array('order_id'=>$row->getId()));
    }

}
