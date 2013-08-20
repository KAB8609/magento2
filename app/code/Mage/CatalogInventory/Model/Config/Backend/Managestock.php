<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_CatalogInventory
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Catalog Inventory Manage Stock Config Backend Model
 *
 * @category   Mage
 * @package    Mage_CatalogInventory
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_CatalogInventory_Model_Config_Backend_Managestock
    extends Mage_Core_Model_Config_Value
{
/**
     * After change Catalog Inventory Manage value process
     *
     * @return Mage_CatalogInventory_Model_Config_Backend_Managestock
     */
    protected function _afterSave()
    {
        $newValue = $this->getValue();
        $oldValue = Mage::getConfig()->getValue(
            Mage_CatalogSearch_Model_Fulltext::XML_PATH_CATALOG_SEARCH_TYPE,
            $this->getScope(),
            $this->getScopeId()
        );
        if ($newValue != $oldValue) {
            Mage::getSingleton('Mage_CatalogInventory_Model_Stock_Status')->rebuild();
        }

        return $this;
    }
}
