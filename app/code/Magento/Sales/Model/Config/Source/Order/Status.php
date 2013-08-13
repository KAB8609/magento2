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
 * Order Statuses source model
 */
class Magento_Sales_Model_Config_Source_Order_Status implements Magento_Core_Model_Option_ArrayInterface
{
    // set null to enable all possible
    protected $_stateStatuses = array(
        Magento_Sales_Model_Order::STATE_NEW,
        Magento_Sales_Model_Order::STATE_PROCESSING,
        Magento_Sales_Model_Order::STATE_COMPLETE,
        Magento_Sales_Model_Order::STATE_CLOSED,
        Magento_Sales_Model_Order::STATE_CANCELED,
        Magento_Sales_Model_Order::STATE_HOLDED,
    );

    public function toOptionArray()
    {
        if ($this->_stateStatuses) {
            $statuses = Mage::getSingleton('Magento_Sales_Model_Order_Config')->getStateStatuses($this->_stateStatuses);
        }
        else {
            $statuses = Mage::getSingleton('Magento_Sales_Model_Order_Config')->getStatuses();
        }
        $options = array();
        $options[] = array(
               'value' => '',
               'label' => Mage::helper('Magento_Sales_Helper_Data')->__('-- Please Select --')
            );
        foreach ($statuses as $code=>$label) {
            $options[] = array(
               'value' => $code,
               'label' => $label
            );
        }
        return $options;
    }
}
