<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Flat sales order collection
 *
 * @category    Magento
 * @package     Magento_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Sales\Model\Resource\Order\Collection;

abstract class AbstractCollection extends \Magento\Sales\Model\Resource\Collection\AbstractCollection
{
    /**
     * Order object
     *
     * @var \Magento\Sales\Model\Order
     */
    protected $_salesOrder   = null;

    /**
     * Order field for setOrderFilter
     *
     * @var string
     */
    protected $_orderField   = 'parent_id';

    /**
     * Set sales order model as parent collection object
     *
     * @param \Magento\Sales\Model\Order $order
     * @return \Magento\Sales\Model\Resource\Order\Collection\AbstractCollection
     */
    public function setSalesOrder($order)
    {
        $this->_salesOrder = $order;
        if ($this->_eventPrefix && $this->_eventObject) {
            $this->_eventManager->dispatch($this->_eventPrefix . '_set_sales_order', array(
                'collection' => $this,
                $this->_eventObject => $this,
                'order' => $order
            ));
        }

        return $this;
    }

    /**
     * Retrieve sales order as parent collection object
     *
     * @return \Magento\Sales\Model\Order|null
     */
    public function getSalesOrder()
    {
        return $this->_salesOrder;
    }

    /**
     * Add order filter
     *
     * @param int|\Magento\Sales\Model\Order $order
     * @return \Magento\Sales\Model\Resource\Order\Collection\AbstractCollection
     */
    public function setOrderFilter($order)
    {
        if ($order instanceof \Magento\Sales\Model\Order) {
            $this->setSalesOrder($order);
            $orderId = $order->getId();
            if ($orderId) {
                $this->addFieldToFilter($this->_orderField, $orderId);
            } else {
                $this->_totalRecords = 0;
                $this->_setIsLoaded(true);
            }
        } else {
            $this->addFieldToFilter($this->_orderField, $order);
        }
        return $this;
    }
}