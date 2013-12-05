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
 * @category   Magento
 * @package    Magento_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Catalog\Helper\Category;

class Flat extends \Magento\Catalog\Helper\Flat\AbstractFlat
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
     * @var \Magento\Index\Model\Process|null
     */
    protected $_process = null;

    /**
     * Core store config
     *
     * @var \Magento\Core\Model\Store\Config
     */
    protected $_coreStoreConfig;

    /**
     * Catalog category flat
     *
     * @var \Magento\Catalog\Model\Resource\Category\Flat
     */
    protected $_catalogCategoryFlat;

    /**
     * @param \Magento\App\Helper\Context $context
     * @param \Magento\Index\Model\ProcessFactory $processFactory
     * @param \Magento\Catalog\Model\Resource\Category\Flat $catalogCategoryFlat
     * @param \Magento\Core\Model\Store\Config $coreStoreConfig
     * @param bool $isAvailable
     */
    public function __construct(
        \Magento\App\Helper\Context $context,
        \Magento\Index\Model\ProcessFactory $processFactory,
        \Magento\Catalog\Model\Resource\Category\Flat $catalogCategoryFlat,
        \Magento\Core\Model\Store\Config $coreStoreConfig,
        $isAvailable = true
    ) {
        $this->_catalogCategoryFlat = $catalogCategoryFlat;
        $this->_coreStoreConfig = $coreStoreConfig;
        parent::__construct($context, $processFactory, $isAvailable);
    }

    /**
     * Check if Catalog Category Flat Data is enabled
     *
     * @param bool $skipAdminCheck this parameter is deprecated and no longer in use
     *
     * @return bool
     */
    public function isEnabled($skipAdminCheck = false)
    {
        return $this->_coreStoreConfig->getConfigFlag(self::XML_PATH_IS_ENABLED_FLAT_CATALOG_CATEGORY);
    }

    /**
     * Check if Catalog Category Flat Data has been initialized
     *
     * @return bool
     */
    public function isBuilt()
    {
        return $this->_catalogCategoryFlat->isBuilt();
    }

    /**
     * Check if Catalog Category Flat Data has been initialized
     *
     * @deprecated after 1.7.0.0 use \Magento\Catalog\Helper\Category\Flat::isBuilt() instead
     *
     * @return bool
     */
    public function isRebuilt()
    {
        return $this->isBuilt();
    }
}
