<?php
/**
 * Modules configuration. Contains primary configuration and configuration from modules /etc/*.xml files
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Core_Model_Config_Modules implements Mage_Core_Model_ConfigInterface
{
    /**
     * Configuration data container
     *
     * @var Mage_Core_Model_ConfigInterface
     */
    protected $_data;

    /**
     * Configuration storage
     *
     * @var Mage_Core_Model_Config_StorageInterface
     */
    protected $_storage;

    /**
     * @param Mage_Core_Model_Config_StorageInterface $storage
     */
    public function __construct(Mage_Core_Model_Config_StorageInterface $storage)
    {
        Magento_Profiler::start('config_modules_load');
        $this->_storage = $storage;
        $this->_data = $this->_storage->getConfiguration();
        Magento_Profiler::stop('config_modules_load');
    }

    /**
     * Get configuration node
     *
     * @param string $path
     * @return Varien_Simplexml_Element
     */
    public function getNode($path = null)
    {
        return $this->_data->getNode($path);
    }

    /**
     * Create node by $path and set its value
     *
     * @param string $path separated by slashes
     * @param string $value
     * @param boolean $overwrite
     */
    public function setNode($path, $value, $overwrite = true)
    {
        $this->_data->setNode($path, $value, $overwrite);
    }

    /**
     * Returns nodes found by xpath expression
     *
     * @param string $xpath
     * @return array
     */
    public function getXpath($xpath)
    {
        return $this->_data->getXpath($xpath);
    }

    /**
     * Get module config node
     *
     * @param string $moduleName
     * @return Varien_Simplexml_Element
     */
    public function getModuleConfig($moduleName = '')
    {
        $modules = $this->getNode('modules');
        if ('' === $moduleName) {
            return $modules;
        } else {
            return $modules->$moduleName;
        }
    }

    /**
     * Reinitialize primary configuration
     */
    public function reinit()
    {
        $this->_data = $this->_storage->getConfiguration();
    }
}
