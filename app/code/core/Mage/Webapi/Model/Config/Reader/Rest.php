<?php
/**
 * REST specific API config reader.
 *
 * @copyright {}
 */
class Mage_Webapi_Model_Config_Reader_Rest extends Mage_Webapi_Model_Config_ReaderAbstract
{
    /**
     * Config type.
     */
    const CONFIG_TYPE = 'REST';

    /**
     * Construct config reader with REST class reflector.
     *
     * @param Mage_Webapi_Model_Config_Reader_Rest_ClassReflector $classReflector
     * @param Mage_Webapi_Helper_Data $helper
     * @param Mage_Core_Model_Config $appConfig
     * @param Mage_Core_Model_Cache $cache
     */
    public function __construct(
        Mage_Webapi_Model_Config_Reader_Rest_ClassReflector $classReflector,
        Mage_Webapi_Helper_Data $helper,
        Mage_Core_Model_Config $appConfig,
        Mage_Core_Model_Cache $cache
    ) {
        parent::__construct($classReflector, $helper, $appConfig, $cache);
    }

    /**
     * Retrieve cache ID.
     *
     * @return string
     */
    public function getCacheId()
    {
        return self::CONFIG_CACHE_ID . '-' . self::CONFIG_TYPE;
    }
}
