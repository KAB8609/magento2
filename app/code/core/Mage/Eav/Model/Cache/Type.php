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
 * System / Cache Management / Cache type "EAV types and attributes"
 *
 * @todo utilize the class for all manipulations with the cache type
 */
class Mage_Eav_Model_Cache_Type extends Magento_Cache_Frontend_Decorator_TagScope
{
    /**
     * Cache type code unique among all cache types
     */
    const TYPE_IDENTIFIER = 'eav';

    /**
     * Cache tag used to distinguish the cache type from all other cache
     */
    const CACHE_TAG = 'EAV';

    /**
     * @param Mage_Core_Model_Cache_Type_FrontendPool $cacheFrontendPool
     */
    public function __construct(Mage_Core_Model_Cache_Type_FrontendPool $cacheFrontendPool)
    {
        parent::__construct($cacheFrontendPool->get(self::TYPE_IDENTIFIER), self::CACHE_TAG);
    }
}
