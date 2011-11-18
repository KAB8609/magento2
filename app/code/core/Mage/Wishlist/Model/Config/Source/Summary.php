<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Wishlist
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Wishlist_Model_Config_Source_Summary
{
    public function toOptionArray()
    {
        return array(
            array('value'=>0, 'label'=>Mage::helper('Mage_Checkout_Helper_Data')->__('Display number of items in wishlist')),
            array('value'=>1, 'label'=>Mage::helper('Mage_Checkout_Helper_Data')->__('Display item quantities')),
        );
    }
}
