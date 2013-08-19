<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_FullPageCache
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Abstract advanced placeholder container
 */
abstract class Magento_FullPageCache_Model_Container_Advanced_Abstract
    extends Magento_FullPageCache_Model_Container_Abstract
{

    /**
     * Get container individual additional cache id
     *
     * @return string | false
     */
    abstract protected function _getAdditionalCacheId();

    /**
     * Load cached data by cache id
     *
     * @param string $id
     * @return string | false
     */
    protected function _loadCache($id)
    {
        $cacheRecord = parent::_loadCache($id);
        if (!$cacheRecord) {
            return false;
        }

        $cacheRecord = json_decode($cacheRecord, true);
        if (!$cacheRecord) {
            return false;
        }

        return isset($cacheRecord[$this->_getAdditionalCacheId()])
            ? $cacheRecord[$this->_getAdditionalCacheId()] : false;
    }

    /**
     * Save data to cache storage. Store many block instances in one cache record depending on additional cache ids.
     *
     * @param string $data
     * @param string $id
     * @param array $tags
     * @param null|int $lifetime
     * @return Magento_FullPageCache_Model_Container_Advanced_Abstract
     */
    protected function _saveCache($data, $id, $tags = array(), $lifetime = null)
    {
        $additionalCacheId = $this->_getAdditionalCacheId();
        if (!$additionalCacheId) {
            Mage::throwException(Mage::helper('Magento_FullPageCache_Helper_Data')->__('Please enter an additional ID.'));
        }

        $tags[] = Magento_FullPageCache_Model_Processor::CACHE_TAG;
        if (is_null($lifetime)) {
            $lifetime = $this->_placeholder->getAttribute('cache_lifetime') ?
                $this->_placeholder->getAttribute('cache_lifetime') : false;
        }

        /**
         * Replace all occurrences of session_id with unique marker
         */
        Magento_FullPageCache_Helper_Url::replaceSid($data);

        $result = array();

        $cacheRecord = parent::_loadCache($id);
        if ($cacheRecord) {
            $cacheRecord = json_decode($cacheRecord, true);
            if ($cacheRecord) {
                $result = $cacheRecord;
            }
        }

        $result[$additionalCacheId] = $data;

        $this->_fpcCache->save(json_encode($result), $id, $tags, $lifetime);
        return $this;
    }
}
