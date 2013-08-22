<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Core_Model_Config_Cache
{
    /**
     * Config cache id
     *
     * @var string
     */
    protected $_cacheId = 'config_global';

    /**
     * Container factory model
     *
     * @var Mage_Core_Model_Config_BaseFactory
     */
    protected $_containerFactory;

    /**
     * @var Mage_Core_Model_Cache_Type_Config
     */
    protected $_configCacheType;

    /**
     * Cache lifetime in seconds
     *
     * @var int
     */
    protected $_cacheLifetime;

    /**
     * Config container
     *
     * @var Mage_Core_Model_Config_Base
     */
    protected $_loadedConfig = null;

    /**
     * @param Mage_Core_Model_Cache_Type_Config $configCacheType
     * @param Mage_Core_Model_Config_BaseFactory $containerFactory
     */
    public function __construct(
        Mage_Core_Model_Cache_Type_Config $configCacheType,
        Mage_Core_Model_Config_BaseFactory $containerFactory
    ) {
        $this->_containerFactory = $containerFactory;
        $this->_configCacheType = $configCacheType;
    }

    /**
     * Set cache lifetime
     *
     * @param int $lifetime
     */
    public function setCacheLifetime($lifetime)
    {
        $this->_cacheLifetime = $lifetime;
    }

    /**
     * Retrieve cache lifetime
     *
     * @return int
     */
    public function getCacheLifeTime()
    {
        return $this->_cacheLifetime;
    }

    /**
     * @return Mage_Core_Model_ConfigInterface|bool
     */
    public function load()
    {
        if (!$this->_loadedConfig) {
            $config = $this->_configCacheType->load($this->_cacheId);
            if ($config) {
                $this->_loadedConfig = $this->_containerFactory->create($config);
            }
        }
        return $this->_loadedConfig ? : false;
    }

    /**
     * Save config cache
     *
     * @param Mage_Core_Model_Config_Base $config
     */
    public function save(Mage_Core_Model_Config_Base $config)
    {
        $this->_configCacheType->save(
            $config->getNode()->asNiceXml('', false), $this->_cacheId, array(), $this->_cacheLifetime
        );
    }

    /**
     * Clean cached data
     *
     * @return bool
     */
    public function clean()
    {
        $this->_loadedConfig = null;
        return $this->_configCacheType->clean();
    }
}
