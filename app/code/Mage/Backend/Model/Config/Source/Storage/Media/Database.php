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
 * Generate options for media database selection
 */
class Mage_Backend_Model_Config_Source_Storage_Media_Database implements Magento_Core_Model_Option_ArrayInterface
{
    /**
     * Store all detected connections
     *
     * @var array
     */
    protected $_connections = array();

    /**
     * Recursively collect connection configuration
     *
     * @param  string $connectionName
     * @return array
     */
    protected function _collectConnectionConfig($connectionName)
    {
        $config = array();

        if (isset($this->_connections[$connectionName])) {
            $connection = $this->_connections[$connectionName];
            $connection = (array) $connection->descend('connection');

            if (isset($connection['use'])) {
                $config = $this->_collectConnectionConfig((string) $connection['use']);
            }

            $config = array_merge($config, $connection);
        }

        return $config;
    }

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $mediaStorages = array();

        $this->_connections = (array) Mage::app()->getConfig()->getNode('global/resources')->children();
        foreach (array_keys($this->_connections) as $connectionName) {
            $connection = $this->_collectConnectionConfig($connectionName);
            if (!isset($connection['active']) || $connection['active'] != 1) {
                continue;
            }

            $mediaStorages[] = array('value' => $connectionName, 'label' => $connectionName);
        }
        sort($mediaStorages);
        reset($mediaStorages);

        return $mediaStorages;
    }

}
