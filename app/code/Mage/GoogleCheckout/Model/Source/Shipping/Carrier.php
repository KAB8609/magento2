<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_GoogleCheckout
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Mage_GoogleCheckout_Model_Source_Shipping_Carrier
{
    public function toOptionArray()
    {
        return array(
            array('label' => Mage::helper('Mage_GoogleCheckout_Helper_Data')->__('FedEx'), 'value' => array(
                array('label' => Mage::helper('Mage_GoogleCheckout_Helper_Data')->__('Ground'), 'value' => 'FedEx/Ground'),
                array('label' => Mage::helper('Mage_GoogleCheckout_Helper_Data')->__('Home Delivery'), 'value' => 'FedEx/Home Delivery'),
                array('label' => Mage::helper('Mage_GoogleCheckout_Helper_Data')->__('Express Saver'), 'value' => 'FedEx/Express Saver'),
                array('label' => Mage::helper('Mage_GoogleCheckout_Helper_Data')->__('First Overnight'), 'value' => 'FedEx/First Overnight'),
                array('label' => Mage::helper('Mage_GoogleCheckout_Helper_Data')->__('Priority Overnight'), 'value' => 'FedEx/Priority Overnight'),
                array('label' => Mage::helper('Mage_GoogleCheckout_Helper_Data')->__('Standard Overnight'), 'value' => 'FedEx/Standard Overnight'),
                array('label' => Mage::helper('Mage_GoogleCheckout_Helper_Data')->__('2Day'), 'value' => 'FedEx/2Day'),
            )),
            array('label' => Mage::helper('Mage_GoogleCheckout_Helper_Data')->__('UPS'), 'value' => array(
                array('label' => Mage::helper('Mage_GoogleCheckout_Helper_Data')->__('Next Day Air'), 'value' => 'UPS/Next Day Air'),
                array('label' => Mage::helper('Mage_GoogleCheckout_Helper_Data')->__('Next Day Air Early AM'), 'value' => 'UPS/Next Day Air Early AM'),
                array('label' => Mage::helper('Mage_GoogleCheckout_Helper_Data')->__('Next Day Air Saver'), 'value' => 'UPS/Next Day Air Saver'),
                array('label' => Mage::helper('Mage_GoogleCheckout_Helper_Data')->__('2nd Day Air'), 'value' => 'UPS/2nd Day Air'),
                array('label' => Mage::helper('Mage_GoogleCheckout_Helper_Data')->__('2nd Day Air AM'), 'value' => 'UPS/2nd Day Air AM'),
                array('label' => Mage::helper('Mage_GoogleCheckout_Helper_Data')->__('3 Day Select'), 'value' => 'UPS/3 Day Select'),
                array('label' => Mage::helper('Mage_GoogleCheckout_Helper_Data')->__('Ground'), 'value' => 'UPS/Ground'),
            )),
            array('label' => Mage::helper('Mage_GoogleCheckout_Helper_Data')->__('USPS'), 'value' => array(
                array('label' => Mage::helper('Mage_GoogleCheckout_Helper_Data')->__('Express Mail'), 'value' => 'USPS/Express Mail'),
                array('label' => Mage::helper('Mage_GoogleCheckout_Helper_Data')->__('Priority Mail'), 'value' => 'USPS/Priority Mail'),
                array('label' => Mage::helper('Mage_GoogleCheckout_Helper_Data')->__('Parcel Post'), 'value' => 'USPS/Parcel Post'),
                array('label' => Mage::helper('Mage_GoogleCheckout_Helper_Data')->__('Media Mail'), 'value' => 'USPS/Media Mail'),
            )),
        );
    }
}
