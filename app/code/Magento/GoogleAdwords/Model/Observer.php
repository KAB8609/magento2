<?php
/**
 * Google AdWords module observer
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GoogleAdwords\Model;

class Observer
{
    /**
     * @var \Magento\GoogleAdwords\Helper\Data
     */
    protected $_helper;

    /**
     * @var \Magento\Sales\Model\Resource\Order\Collection
     */
    protected $_collection;

    /**
     * Constructor
     *
     * @param \Magento\GoogleAdwords\Helper\Data $helper
     * @param \Magento\Core\Model\Registry $registry
     * @param \Magento\Sales\Model\Resource\Order\Collection $collection
     */
    public function __construct(
        \Magento\GoogleAdwords\Helper\Data $helper,
        \Magento\Core\Model\Registry $registry,
        \Magento\Sales\Model\Resource\Order\Collection $collection
    ) {
        $this->_helper = $helper;
        $this->_collection = $collection;
        $this->_registry = $registry;
    }

    /**
     * Set base grand total of order to registry
     *
     * @param \Magento\Event\Observer $observer
     * @return \Magento\GoogleAdwords\Model\Observer
     */
    public function setConversionValue(\Magento\Event\Observer $observer)
    {
        if (!($this->_helper->isGoogleAdwordsActive() && $this->_helper->isDynamicConversionValue())) {
            return $this;
        }
        $orderIds = $observer->getEvent()->getOrderIds();
        if (!$orderIds || !is_array($orderIds)) {
            return $this;
        }
        $this->_collection->addFieldToFilter('entity_id', array('in' => $orderIds));
        $conversionValue = 0;
        /** @var $order \Magento\Sales\Model\Order */
        foreach ($this->_collection as $order) {
            $conversionValue += $order->getBaseGrandTotal();
        }
        $this->_registry->register(\Magento\GoogleAdwords\Helper\Data::CONVERSION_VALUE_REGISTRY_NAME, $conversionValue);
        return $this;
    }
}
