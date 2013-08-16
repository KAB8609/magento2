<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Module statuses manager
 */
class Mage_Core_Model_ModuleManager
{
    /**
     * XPath in the configuration where module statuses are stored
     */
    const XML_PATH_MODULE_OUTPUT_STATUS = 'advanced/modules_disable_output/%s';

    /**
     * @var Mage_Core_Model_Store_ConfigInterface
     */
    private $_storeConfig;

    /**
     * @var Mage_Core_Model_ModuleListInterface
     */
    private $_moduleList;

    /**
     * @var array
     */
    private $_outputConfigPaths;

    /**
     * @param Mage_Core_Model_Store_ConfigInterface $storeConfig
     * @param Mage_Core_Model_ModuleListInterface $moduleList
     * @param array $outputConfigPaths
     */
    public function __construct(
        Mage_Core_Model_Store_ConfigInterface $storeConfig,
        Mage_Core_Model_ModuleListInterface $moduleList,
        array $outputConfigPaths = array()
    ) {
        $this->_storeConfig = $storeConfig;
        $this->_moduleList = $moduleList;
        $this->_outputConfigPaths = $outputConfigPaths;
    }

    /**
     * Whether a module is enabled in the configuration or not
     *
     * @param string $moduleName Fully-qualified module name
     * @return boolean
     */
    public function isEnabled($moduleName)
    {
        return !!$this->_moduleList->getModule($moduleName);
    }

    /**
     * Whether a module output is permitted by the configuration or not
     *
     * @param string $moduleName Fully-qualified module name
     * @return boolean
     */
    public function isOutputEnabled($moduleName)
    {
        if (!$this->isEnabled($moduleName)) {
            return false;
        }
        if (!$this->_isCustomOutputConfigEnabled($moduleName)) {
            return false;
        }
        if ($this->_storeConfig->getConfigFlag(sprintf(self::XML_PATH_MODULE_OUTPUT_STATUS, $moduleName))) {
            return false;
        }
        return true;
    }

    /**
     * Whether a configuration switch for a module output permits output or not
     *
     * @param string $moduleName Fully-qualified module name
     * @return boolean
     */
    protected function _isCustomOutputConfigEnabled($moduleName)
    {
        if (isset($this->_outputConfigPaths[$moduleName])) {
            $configPath = $this->_outputConfigPaths[$moduleName];
            if (defined($configPath)) {
                $configPath = constant($configPath);
            }
            return $this->_storeConfig->getConfigFlag($configPath);
        }
        return true;
    }
}
