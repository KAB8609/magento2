<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Usa
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Magento_Usa_Model_Shipping_Carrier_Usps_Source_Freemethod extends Magento_Usa_Model_Shipping_Carrier_Usps_Source_Method
{
    public function toOptionArray()
    {
        $arr = parent::toOptionArray();
        array_unshift($arr, array('value'=>'', 'label'=>Mage::helper('Mage_Shipping_Helper_Data')->__('None')));
        return $arr;
    }
}
