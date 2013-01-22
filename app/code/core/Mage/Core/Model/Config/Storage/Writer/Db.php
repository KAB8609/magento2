<?php
/**
 * Application config db storage writer
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Core_Model_Config_Storage_Writer_Db implements Mage_Core_Model_Config_Storage_WriterInterface
{
    /**
     * Resource model of config data
     *
     * @var Mage_Core_Model_Resource_Config
     */
    protected $_resource;

    /**
     * @param Mage_Core_Model_Resource_Config $resource
     */
    public function __construct(Mage_Core_Model_Resource_Config $resource)
    {
        $this->_resource = $resource;
    }

    /**
     * Delete config value from storage
     *
     * @param   string $path
     * @param   string $scope
     * @param   int $scopeId
     */
    public function delete($path, $scope = Mage_Core_Model_Store::DEFAULT_CODE, $scopeId = 0)
    {
        $this->_resource->deleteConfig(rtrim($path, '/'), $scope, $scopeId);
    }

    /**
     * Save config value to storage
     *
     * @param string $path
     * @param string $value
     * @param string $scope
     * @param int $scopeId
     */
    public function save($path, $value, $scope = Mage_Core_Model_Store::DEFAULT_CODE, $scopeId = 0)
    {
        $this->_resource->saveConfig(rtrim($path, '/'), $value, $scope, $scopeId);
    }
}
