<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Checkout
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Checkout\Block;

class Success extends \Magento\View\Element\Template
{
    /**
     * @var \Magento\Sales\Model\OrderFactory
     */
    protected $_orderFactory;

    /**
     * @param \Magento\View\Element\Template\Context $context
     * @param \Magento\Sales\Model\OrderFactory $orderFactory
     * @param array $data
     */
    public function __construct(
        \Magento\View\Element\Template\Context $context,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        array $data = array()
    ) {
        $this->_orderFactory = $orderFactory;
        parent::__construct($context, $data);
    }

    /**
     * @return int
     */
    public function getRealOrderId()
    {
        /** @var \Magento\Sales\Model\Order $order */
        $order = $this->_orderFactory()->create()->load($this->getLastOrderId());
        return $order->getIncrementId();
    }
}
