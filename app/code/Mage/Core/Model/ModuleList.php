<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Core_Model_ModuleList implements Mage_Core_Model_ModuleListInterface
{
    /**
     * Configuration data
     *
     * @var array
     */
    protected $_data;

    /**
     * Configuration scope
     *
     * @var string
     */
    protected $_scope = 'global';

    /**
     * @param Mage_Core_Model_Module_Declaration_Reader_Filesystem $reader
     * @param Magento_Config_CacheInterface $cache
     * @param string $cacheId
     */
    public function __construct(
        Mage_Core_Model_Module_Declaration_Reader_Filesystem $reader,
        Magento_Config_CacheInterface $cache,
        $cacheId = 'modules_declaration_cache'
    ) {
        $data = $cache->get($this->_scope, $cacheId);
        if (!$data) {
            $data = $reader->read($this->_scope);
            $cache->put($data, $this->_scope, $cacheId);
        }
        $this->_data = $data;
    }

    /**
     * Get configuration of all declared active modules
     *
     * @return array
     */
    public function getModules()
    {
        return $this->_data;
    }

    /**
     * Get module configuration
     *
     * @param string $moduleName
     * @return array|null
     */
    public function getModule($moduleName)
    {
        return isset($this->_data[$moduleName]) ? $this->_data[$moduleName] : null;
    }
}
