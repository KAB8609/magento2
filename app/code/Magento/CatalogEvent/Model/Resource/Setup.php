<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Catalog Event resource setup
 */
class Magento_CatalogEvent_Model_Resource_Setup extends Magento_Sales_Model_Resource_Setup
{
    /**
     * Block model factory
     *
     * @var Magento_Cms_Model_BlockFactory
     */
    protected $_modelBlockFactory;

    /**
     * Construct
     *
     * @param Magento_Core_Model_Config_Resource $resourcesConfig
     * @param Magento_Core_Model_Config $config
     * @param Magento_Core_Model_ModuleListInterface $moduleList
     * @param Magento_Core_Model_Resource $resource
     * @param Magento_Core_Model_Config_Modules_Reader $modulesReader
     * @param Magento_Core_Model_CacheInterface $cache
     * @param $resourceName
     * @param Magento_Cms_Model_BlockFactory $modelBlockFactory
     */
    public function __construct(
        Magento_Core_Model_Config_Resource $resourcesConfig,
        Magento_Core_Model_Config $config,
        Magento_Core_Model_ModuleListInterface $moduleList,
        Magento_Core_Model_Resource $resource,
        Magento_Core_Model_Config_Modules_Reader $modulesReader,
        Magento_Core_Model_CacheInterface $cache,
        $resourceName,
        Magento_Cms_Model_BlockFactory $modelBlockFactory
    ) {
        parent::__construct($resourcesConfig, $config, $moduleList, $resource, $modulesReader, $cache, $resourceName);

        $this->_modelBlockFactory = $modelBlockFactory;
    }

    /**
     * Get model block factory
     *
     * @return Magento_Cms_Model_BlockFactory
     */
    public function getModelBlockFactory()
    {
        return $this->_modelBlockFactory;
    }
}
