<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_PHPUnit
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Memory cache model
 *
 * @category    Mage
 * @package     Mage_PHPUnit
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_PHPUnit_Cache_Memory extends Zend_Cache_Backend implements Zend_Cache_Backend_Interface
{
    /**
     * Cached data array
     *
     * @var array
     */
    static protected $_cache = array();

    /**
     * Array of entities which are allowed to cache
     *
     * @var array
     */
    protected $_allowedOptions = array(
        'config' => true
    );

    /**
     * Cache id prefix.
     * Should equals to string in <id_prefix> field in the config
     *
     * @var string
     */
    protected $_idPrefix = 'mem';

    /**
     * Constructor
     *
     * @param  array $options Associative array of options
     * @throws Zend_Cache_Exception
     */
    public function __construct(array $options = array())
    {
        parent::__construct($options);

        $this->save(serialize($this->_allowedOptions), $this->_id(Mage_Core_Model_Cache::OPTIONS_CACHE_ID));
    }

    /**
     * Returns cache full id
     *
     * @param string $id
     * @return string
     */
    protected function _id($id)
    {
        return $this->_idPrefix . strtoupper($id);
    }

    /**
     * Empty method. Does not need for memory cache
     *
     * @param array $directives
     */
    public function setDirectives($directives)
    {

    }

    /**
     * Loads data from cache
     *
     * @param string $id
     * @param bool $doNotTestCacheValidity isn't used
     * @return mixed
     */
    public function load($id,  $doNotTestCacheValidity = false)
    {
        return isset(self::$_cache[$id]) ? self::$_cache[$id] : null;
    }

    /**
     * Checks if data is in cache by id
     *
     * @param string $id
     * @return bool
     */
    public function test($id)
    {
        return array_key_exists($id, self::$_cache[$id]);
    }

    /**
     * Saves data to cache
     *
     * @param mixed $data
     * @param string $id
     * @param array $tags isn't used
     * @param bool $specificLifetime isn't used
     * @return bool
     */
    public function save($data, $id, $tags = array(), $specificLifetime = false)
    {
        self::$_cache[$id] = $data;
        return true;
    }

    /**
     * Removes data from cache
     *
     * @param string $id
     */
    public function remove($id)
    {
        unset(self::$_cache[$id]);
    }

    /**
     * Cleans all cache if $mode == Zend_Cache::CLEANING_MODE_ALL
     * Does not clean anything otherwise
     *
     * @param string $mode
     * @param array $tags
     * @return bool
     */
    public function clean($mode = Zend_Cache::CLEANING_MODE_ALL, $tags = array())
    {
        if ($mode == Zend_Cache::CLEANING_MODE_ALL) {
            self::$_cache = array();
        }
        return true;
    }

    /**
     * Statically cleans cache data by id.
     * Needed in some initializers.
     *
     * @param string|null $id If $id == null then cleans all cache
     */
    public static function staticClean($id = null)
    {
        if (!$id) {
            self::$_cache = array();
        } else {
            unset(self::$_cache[$id]);
        }
    }
}
