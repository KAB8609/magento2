<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CatalogInventory
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * CatalogInventory Stock source model
 *
 * @category   Magento
 * @package    Magento_CatalogInventory
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_CatalogInventory_Model_Source_Stock
{
    /**
     * Retrieve option array
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array(
                'value' => Magento_CatalogInventory_Model_Stock::STOCK_IN_STOCK,
                'label' => Mage::helper('Magento_CatalogInventory_Helper_Data')->__('In Stock')
            ),
            array(
                'value' => Magento_CatalogInventory_Model_Stock::STOCK_OUT_OF_STOCK,
                'label' => Mage::helper('Magento_CatalogInventory_Helper_Data')->__('Out of Stock')
            ),
        );
    }
}
