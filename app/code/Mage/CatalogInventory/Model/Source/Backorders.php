<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_CatalogInventory
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_CatalogInventory_Model_Source_Backorders
{
    public function toOptionArray()
    {
        return array(
            array('value' => Mage_CatalogInventory_Model_Stock::BACKORDERS_NO, 'label'=>Mage::helper('Mage_CatalogInventory_Helper_Data')->__('No Backorders')),
            array('value' => Mage_CatalogInventory_Model_Stock::BACKORDERS_YES_NONOTIFY, 'label'=>Mage::helper('Mage_CatalogInventory_Helper_Data')->__('Allow Qty Below 0')),
            array('value' => Mage_CatalogInventory_Model_Stock::BACKORDERS_YES_NOTIFY , 'label'=>Mage::helper('Mage_CatalogInventory_Helper_Data')->__('Allow Qty Below 0 and Notify Customer')),
        );
    }
}
