<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Usa
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * UPS (UPS XML) Modesource model
 *
 * @category Mage
 * @package Mage_Usa
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Usa_Model_Shipping_Carrier_Ups_Source_Mode
{
    public function toOptionArray()
    {
        return array(
            array('value' => '1', 'label' => Mage::helper('Mage_Usa_Helper_Data')->__('Live')),
            array('value' => '0', 'label' => Mage::helper('Mage_Usa_Helper_Data')->__('Development')),
        );
    }
}
