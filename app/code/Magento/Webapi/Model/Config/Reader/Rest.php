<?php
/**
 * REST specific API config reader.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Webapi\Model\Config\Reader;

class Rest extends \Magento\Webapi\Model\Config\ReaderAbstract
{
    /**
     * Config type.
     */
    const CONFIG_TYPE = 'REST';

    /**
     * Construct config reader with REST class reflector.
     *
     * @param \Magento\Webapi\Model\Config\Reader\Rest\ClassReflector $classReflector
     * @param \Magento\Core\Model\Config $appConfig
     * @param \Magento\Core\Model\CacheInterface $cache
     * @param \Magento\Core\Model\ModuleListInterface $moduleList
     * @param \Magento\Core\Model\Cache\StateInterface $cacheState
     */
    public function __construct(
        \Magento\Webapi\Model\Config\Reader\Rest\ClassReflector $classReflector,
        \Magento\Core\Model\Config $appConfig,
        \Magento\Core\Model\CacheInterface $cache,
        \Magento\Core\Model\ModuleListInterface $moduleList,
        \Magento\Core\Model\Cache\StateInterface $cacheState
    ) {
        parent::__construct($classReflector, $appConfig, $cache, $moduleList, $cacheState);
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
