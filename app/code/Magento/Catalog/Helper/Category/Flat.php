<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Catalog flat helper
 *
 * @category   Mage
 * @package    Magento_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Catalog_Helper_Category_Flat extends Magento_Catalog_Helper_Flat_Abstract
{
    /**
     * Catalog Category Flat Is Enabled Config
     */
    const XML_PATH_IS_ENABLED_FLAT_CATALOG_CATEGORY = 'catalog/frontend/flat_catalog_category';

    /**
     * Catalog Flat Category index process code
     */
    const CATALOG_CATEGORY_FLAT_PROCESS_CODE = 'catalog_category_flat';

    /**
     * Catalog Category Flat index process code
     *
     * @var string
     */
    protected $_indexerCode = self::CATALOG_CATEGORY_FLAT_PROCESS_CODE;

    /**
     * Store catalog Category Flat index process instance
     *
     * @var Mage_Index_Model_Process|null
     */
    protected $_process = null;

    /**
     * Check if Catalog Category Flat Data is enabled
     *
     * @param bool $skipAdminCheck this parameter is deprecated and no longer in use
     *
     * @return bool
     */
    public function isEnabled($skipAdminCheck = false)
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_IS_ENABLED_FLAT_CATALOG_CATEGORY);
    }

    /**
     * Check if Catalog Category Flat Data has been initialized
     *
     * @return bool
     */
    public function isBuilt()
    {
        return Mage::getResourceSingleton('Magento_Catalog_Model_Resource_Category_Flat')->isBuilt();
    }

    /**
     * Check if Catalog Category Flat Data has been initialized
     *
     * @deprecated after 1.7.0.0 use Magento_Catalog_Helper_Category_Flat::isBuilt() instead
     *
     * @return bool
     */
    public function isRebuilt()
    {
        return $this->isBuilt();
    }
}
