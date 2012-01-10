<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_GoogleCheckout
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Mage_GoogleCheckout_Model_Source_Shipping_Category
{
    public function toOptionArray()
    {
        return array(
            array('value' => 'COMMERCIAL',  'label' => Mage::helper('googlecheckout')->__('Commercial')),
            array('value' => 'RESIDENTIAL', 'label' => Mage::helper('googlecheckout')->__('Residential')),
        );
    }
}