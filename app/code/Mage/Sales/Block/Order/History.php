<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Sales order history block
 *
 * @category   Mage
 * @package    Mage_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Sales_Block_Order_History extends Mage_Core_Block_Template
{

    protected $_template = 'order/history.phtml';


    protected function _construct()
    {
        parent::_construct();


        $orders = Mage::getResourceModel('Mage_Sales_Model_Resource_Order_Collection')
            ->addFieldToSelect('*')
            ->addFieldToFilter('customer_id', Mage::getSingleton('Mage_Customer_Model_Session')->getCustomer()->getId())
            ->addFieldToFilter('state', array('in' => Mage::getSingleton('Mage_Sales_Model_Order_Config')->getVisibleOnFrontStates()))
            ->setOrder('created_at', 'desc')
        ;

        $this->setOrders($orders);

        if (Mage::app()->getFrontController()->getAction()) {
            Mage::app()->getFrontController()->getAction()->getLayout()->getBlock('root')->setHeaderTitle(
                Mage::helper('Mage_Sales_Helper_Data')->__('My Orders')
            );
        }
    }

    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        $pager = $this->getLayout()->createBlock('Mage_Page_Block_Html_Pager', 'sales.order.history.pager')
            ->setCollection($this->getOrders());
        $this->setChild('pager', $pager);
        $this->getOrders()->load();
        return $this;
    }

    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }

    public function getViewUrl($order)
    {
        return $this->getUrl('*/*/view', array('order_id' => $order->getId()));
    }

    public function getTrackUrl($order)
    {
        return $this->getUrl('*/*/track', array('order_id' => $order->getId()));
    }

    public function getReorderUrl($order)
    {
        return $this->getUrl('*/*/reorder', array('order_id' => $order->getId()));
    }

    public function getBackUrl()
    {
        return $this->getUrl('customer/account/');
    }
}
