<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_CatalogSearch
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Catalog Search change Search Type backend model
 *
 * @category   Mage
 * @package    Mage_CatalogSearch
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_CatalogSearch_Model_Config_Backend_Search_Type extends Magento_Core_Model_Config_Data
{
    /**
     * After change Catalog Search Type process
     *
     * @return Mage_CatalogSearch_Model_Config_Backend_Search_Type|Magento_Core_Model_Abstract
     */
    protected function _afterSave()
    {
        $newValue = $this->getValue();
        $oldValue = Mage::getConfig()->getNode(
            Mage_CatalogSearch_Model_Fulltext::XML_PATH_CATALOG_SEARCH_TYPE,
            $this->getScope(),
            $this->getScopeId()
        );
        if ($newValue != $oldValue) {
            Mage::getSingleton('Mage_CatalogSearch_Model_Fulltext')->resetSearchResults();
        }

        return $this;
    }
}
