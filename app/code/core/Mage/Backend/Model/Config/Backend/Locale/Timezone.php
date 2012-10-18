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
 * System config email field backend model
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Backend_Model_Config_Backend_Locale_Timezone extends Mage_Core_Model_Config_Data
{
    /**
     * Const for PHP 5.3+ compatibility
     * This value copied from DateTimeZone::ALL_WITH_BC in PHP 5.3+
     *
     * @constant ALL_WITH_BC
     */
    const ALL_WITH_BC = 4095;

    protected function _beforeSave()
    {
        $allWithBc = self::ALL_WITH_BC;
        if (defined('DateTimeZone::ALL_WITH_BC')) {
            $allWithBc = DateTimeZone::ALL_WITH_BC;
        }

        if (!in_array($this->getValue(), DateTimeZone::listIdentifiers($allWithBc))) {
            Mage::throwException(Mage::helper('Mage_Adminhtml_Helper_Data')->__('Invalid timezone'));
        }

        return $this;
    }
}
