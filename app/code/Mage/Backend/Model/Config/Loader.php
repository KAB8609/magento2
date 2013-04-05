<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * System configuration loader
 */
class Mage_Backend_Model_Config_Loader
{
    /**
     * Config data factory
     *
     * @var Mage_Core_Model_Config_DataFactory
     */
    protected $_configDataFactory;

    /**
     * @param Mage_Core_Model_Config_DataFactory $configDataFactory
     */
    public function __construct(Mage_Core_Model_Config_DataFactory $configDataFactory)
    {
        $this->_configDataFactory = $configDataFactory;
    }

    /**
     * Get configuration value by path
     *
     * @param string $path
     * @param string $scope
     * @param string $scopeId
     * @param bool $full
     * @return array
     */
    public function getConfigByPath($path, $scope, $scopeId, $full = true)
    {
        $configDataCollection = $this->_configDataFactory->create();
        $configDataCollection = $configDataCollection
            ->getCollection()
            ->addScopeFilter($scope, $scopeId, $path);

        $config = array();
        $configDataCollection->load();
        foreach ($configDataCollection->getItems() as $data) {
            if ($full) {
                $config[$data->getPath()] = array(
                    'path'      => $data->getPath(),
                    'value'     => $data->getValue(),
                    'config_id' => $data->getConfigId()
                );
            } else {
                $config[$data->getPath()] = $data->getValue();
            }
        }
        return $config;
    }
}
