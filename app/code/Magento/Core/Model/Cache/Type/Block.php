<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * System / Cache Management / Cache type "Blocks HTML output"
 *
 * @todo utilize the class for all manipulations with the cache type
 */
class Magento_Core_Model_Cache_Type_Block extends Magento_Cache_Frontend_Decorator_TagScope
{
    /**
     * Cache type code unique among all cache types
     */
    const TYPE_IDENTIFIER = 'block_html';

    /**
     * Cache tag used to distinguish the cache type from all other cache
     */
    const CACHE_TAG = 'BLOCK_HTML';

    /**
     * @param Magento_Core_Model_Cache_Type_FrontendPool $cacheFrontendPool
     */
    public function __construct(Magento_Core_Model_Cache_Type_FrontendPool $cacheFrontendPool)
    {
        parent::__construct($cacheFrontendPool->get(self::TYPE_IDENTIFIER), self::CACHE_TAG);
    }
}