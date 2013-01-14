<?php
/**
 * Resource configuration. Uses application configuration to retrieve resource information.
 * Uses latest loaded configuration object to make resource connection available on early stages of bootstrapping.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */ 
class Mage_Core_Model_Config_Resource
{
    /**
     * @var Mage_Core_Model_Config_Base
     */
    protected $_config;

    /**
     * @param Mage_Core_Model_Config_Primary $config
     */
    public function __construct(Mage_Core_Model_Config_Primary $config)
    {
        $this->_config = $config;
    }

    /**
     * Set application config
     *
     * @param Mage_Core_Model_Config_Base $config
     */
    public function setConfig(Mage_Core_Model_Config_Base $config)
    {
        $this->_config = $config;
    }

    /**
     * Get resource configuration for resource name
     *
     * @param string $name
     * @return Varien_Simplexml_Object
     */
    public function getResourceConfig($name)
    {
        return $this->_config->getNode('global/resources/' . $name);
    }

    /**
     * Retrieve resource connection configuration by name
     *
     * @param $name
     * @return Varien_Simplexml_Element
     */
    public function getResourceConnectionConfig($name)
    {
        $config = $this->getResourceConfig($name);
        if ($config) {
            $conn = $config->connection;
            if ($conn) {
                if (!empty($conn->use)) {
                    return $this->getResourceConnectionConfig((string)$conn->use);
                } else {
                    return $conn;
                }
            }
        }
        return false;
    }

    /**
     * Retrieve reosurce type configuration
     *
     * @param $type
     * @return Varien_Simplexml_Element
     */
    public function getResourceTypeConfig($type)
    {
        return (string) $this->_config->getNode('global/resource/connection/types/' . $type);
    }

    /**
     * Retrieve database table prefix
     *
     * @return string
     */
    public function getTablePrefix()
    {
        return (string) $this->_config->getNode('global/resources/db/table_prefix');
    }

    /**
     * Retrieve resource connection model name
     *
     * @param string $moduleName
     * @return string
     */
    public function getResourceConnectionModel($moduleName = null)
    {
        $config = null;
        if (!is_null($moduleName)) {
            $setupResource = $moduleName . '_setup';
            $config        = $this->getResourceConnectionConfig($setupResource);
        }
        if (!$config) {
            $config = $this->getResourceConnectionConfig(Mage_Core_Model_Resource::DEFAULT_SETUP_RESOURCE);
        }

        return (string) $config->model;
    }
}
