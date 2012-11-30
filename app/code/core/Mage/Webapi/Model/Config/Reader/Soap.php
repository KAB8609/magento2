<?php
/**
 * SOAP specific API config reader.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Webapi_Model_Config_Reader_Soap extends Mage_Webapi_Model_Config_ReaderAbstract
{
    /**
     * Config type.
     */
    const CONFIG_TYPE = 'SOAP';

    /**
     * Construct config reader with SOAP class reflector.
     *
     * @param Mage_Webapi_Model_Config_Reader_Soap_ClassReflector $classReflector
     * @param Mage_Core_Model_Config $appConfig
     * @param Mage_Core_Model_Cache $cache
     */
    public function __construct(
        Mage_Webapi_Model_Config_Reader_Soap_ClassReflector $classReflector,
        Mage_Core_Model_Config $appConfig,
        Mage_Core_Model_Cache $cache
    ) {
        parent::__construct($classReflector, $appConfig, $cache);
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
