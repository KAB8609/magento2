<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GoogleCheckout
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Magento_GoogleCheckout_Model_Source_Shipping_Virtual_Schedule
{
    public function toOptionArray()
    {
        return array(
            array('value' => 'OPTIMISTIC',  'label' => Mage::helper('Magento_GoogleCheckout_Helper_Data')->__('Optimistic')),
            array('value' => 'PESSIMISTIC', 'label' => Mage::helper('Magento_GoogleCheckout_Helper_Data')->__('Pessimistic')),
        );
    }
}
