<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * System config translate inline fields backend model
 *
 * @category   Magento
 * @package    Magento_Backend
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Backend_Model_Config_Backend_Translate extends Magento_Core_Model_Config_Value
{
    /**
     * Path to config node with list of caches
     *
     * @var string
     */
    const XML_PATH_INVALID_CACHES = 'dev/translate_inline/invalid_caches';

    /**
     * Set status 'invalidate' for blocks and other output caches
     *
     * @return Magento_Backend_Model_Config_Backend_Translate
     */
    protected function _afterSave()
    {
        $types = array_keys(Mage::getStoreConfig(self::XML_PATH_INVALID_CACHES));
        if ($this->isValueChanged()) {
            /** @var Magento_Core_Model_Cache_TypeListInterface $cacheTypeList */
            $cacheTypeList = Mage::getObjectManager()->get('Magento_Core_Model_Cache_TypeListInterface');
            $cacheTypeList->invalidate($types);
        }

        return $this;
    }
}