<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * System cache model interface
 */
interface Mage_Core_Model_CacheInterface
{
    /**
     * Get cache frontend API object
     *
     * @return Zend_Cache_Core
     */
    public function getFrontend();

    /**
     * Load data from cache by id
     *
     * @param   string $id
     * @return  string
     */
    public function load($id);

    /**
     * Save data
     *
     * @param string $data
     * @param string $id
     * @param array $tags
     * @param int $lifeTime
     * @return bool
     */
    public function save($data, $id, $tags = array(), $lifeTime = null);

    /**
     * Remove cached data by identifier
     *
     * @param string $id
     * @return bool
     */
    public function remove($id);

    /**
     * Clean cached data by specific tag
     *
     * @param array $tags
     * @return bool
     */
    public function clean($tags = array());

    /**
     * Clean cached data by specific tag
     *
     * @return bool
     */
    public function flush();

    /**
     * Get adapter for database cache backend model
     *
     * @return Zend_Db_Adapter_Abstract
     */
    public function getDbAdapter();

    /**
     * Check if cache can be used for specific data type
     *
     * @param string $typeCode
     * @return bool
     */
    public function canUse($typeCode);

    /**
     * Disable cache usage for specific data type
     *
     * @param string $typeCode
     * @return Mage_Core_Model_CacheInterface
     */
    public function banUse($typeCode);

    /**
     * Enable cache usage for specific data type
     *
     * @param string $typeCode
     * @return Mage_Core_Model_CacheInterface
     */
    public function allowUse($typeCode);

    /**
     * Get cache tags by cache type from configuration
     *
     * @param string $type
     * @return array
     */
    public function getTagsByType($type);

    /**
     * Get information about all declared cache types
     *
     * @return array
     */
    public function getTypes();

    /**
     * Get array of all invalidated cache types
     *
     * @return array
     */
    public function getInvalidatedTypes();

    /**
     * Mark specific cache type(s) as invalidated
     *
     * @param string|array $typeCode
     * @return Mage_Core_Model_CacheInterface
     */
    public function invalidateType($typeCode);

    /**
     * Clean cached data for specific cache type
     *
     * @param string $typeCode
     * @return Mage_Core_Model_CacheInterface
     */
    public function cleanType($typeCode);
}
