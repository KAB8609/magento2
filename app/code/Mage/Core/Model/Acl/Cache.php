<?php
/**
 * ACL cache
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
class Mage_Core_Model_Acl_Cache implements Magento_Acl_CacheInterface
{
    /**
     * Cache
     *
     * @var Mage_Core_Model_Cache_Type_Config
     */
    protected $_cache;

    /**
     * Cache key
     *
     * @var string
     */
    protected $_cacheKey;

    /**
     * @var Magento_Acl
     */
    protected $_acl = null;

    /**
     * @param Mage_Core_Model_Cache_Type_Config $cache
     * @param string $cacheKey
     */
    function __construct(Mage_Core_Model_Cache_Type_Config $cache, $cacheKey)
    {
        $this->_cache = $cache;
        $this->_cacheKey = $cacheKey;
    }

    /**
     * Check whether ACL object is in cache
     *
     * @return bool
     */
    public function has()
    {
        return null !== $this->_acl || $this->_cache->test($this->_cacheKey);
    }

    /**
     * Retrieve ACL object from cache
     *
     * @return Magento_Acl
     */
    public function get()
    {
        if (null == $this->_acl) {
            $this->_acl = unserialize($this->_cache->load($this->_cacheKey));
        }
        return $this->_acl;
    }

    /**
     * Save ACL object to cache
     *
     * @param Magento_Acl $acl
     */
    public function save(Magento_Acl $acl)
    {
        $this->_acl = $acl;
        $this->_cache->save(serialize($acl), $this->_cacheKey);
    }

    /**
     * Clear ACL instance cache
     */
    public function clean()
    {
        $this->_acl = null;
        $this->_cache->remove($this->_cacheKey);
    }
}