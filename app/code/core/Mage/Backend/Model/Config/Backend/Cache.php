<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Cache cleaner backend model
 *
 */
class Mage_Backend_Model_Config_Backend_Cache extends Mage_Core_Model_Config_Data
{
    /**
     * Cache tags to clean
     *
     * @var array
     */
    protected $_cacheTags = array();

    /**
     * Clean cache, value was changed
     *
     */
    protected function _afterSave()
    {
        if ($this->isValueChanged()) {
            Mage::app()->cleanCache($this->_cacheTags);
        }
    }
}
