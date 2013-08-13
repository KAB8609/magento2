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
 * Adminhtml customer orders grid block
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Adminhtml_Block_Customer_Edit_Tab_Orders extends Magento_Adminhtml_Block_Widget_Grid
{

    protected function _construct()
    {
        parent::_construct();
        $this->setId('customer_orders_grid');
        $this->setDefaultSort('created_at', 'desc');
        $this->setUseAjax(true);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('Mage_Sales_Model_Resource_Order_Grid_Collection')
            ->addFieldToSelect('entity_id')
            ->addFieldToSelect('increment_id')
            ->addFieldToSelect('customer_id')
            ->addFieldToSelect('created_at')
            ->addFieldToSelect('grand_total')
            ->addFieldToSelect('order_currency_code')
            ->addFieldToSelect('store_id')
            ->addFieldToSelect('billing_name')
            ->addFieldToSelect('shipping_name')
            ->addFieldToFilter('customer_id', Mage::registry('current_customer')->getId())
            ->setIsCustomerMode(true);

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('increment_id', array(
            'header'    => Mage::helper('Magento_Customer_Helper_Data')->__('Order'),
            'width'     => '100',
            'index'     => 'increment_id',
        ));

        $this->addColumn('created_at', array(
            'header'    => Mage::helper('Magento_Customer_Helper_Data')->__('Purchase Date'),
            'index'     => 'created_at',
            'type'      => 'datetime',
        ));

        /*$this->addColumn('shipping_firstname', array(
            'header'    => Mage::helper('Magento_Customer_Helper_Data')->__('Shipped to First Name'),
            'index'     => 'shipping_firstname',
        ));

        $this->addColumn('shipping_lastname', array(
            'header'    => Mage::helper('Magento_Customer_Helper_Data')->__('Shipped to Last Name'),
            'index'     => 'shipping_lastname',
        ));*/
        $this->addColumn('billing_name', array(
            'header'    => Mage::helper('Magento_Customer_Helper_Data')->__('Bill-to Name'),
            'index'     => 'billing_name',
        ));

        $this->addColumn('shipping_name', array(
            'header'    => Mage::helper('Magento_Customer_Helper_Data')->__('Ship-to Name'),
            'index'     => 'shipping_name',
        ));

        $this->addColumn('grand_total', array(
            'header'    => Mage::helper('Magento_Customer_Helper_Data')->__('Order Total'),
            'index'     => 'grand_total',
            'type'      => 'currency',
            'currency'  => 'order_currency_code',
        ));

        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('store_id', array(
                'header'    => Mage::helper('Magento_Customer_Helper_Data')->__('Purchase Point'),
                'index'     => 'store_id',
                'type'      => 'store',
                'store_view' => true
            ));
        }

        if (Mage::helper('Mage_Sales_Helper_Reorder')->isAllow()) {
            $this->addColumn('action', array(
                'header'    => ' ',
                'filter'    => false,
                'sortable'  => false,
                'width'     => '100px',
                'renderer'  => 'Magento_Adminhtml_Block_Sales_Reorder_Renderer_Action'
            ));
        }

        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/sales_order/view', array('order_id' => $row->getId()));
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/orders', array('_current' => true));
    }

}
