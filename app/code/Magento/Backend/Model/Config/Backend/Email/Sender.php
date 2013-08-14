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
 * System config email sender field backend model
 *
 * @category   Mage
 * @package    Magento_Backend
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Backend_Model_Config_Backend_Email_Sender extends Magento_Core_Model_Config_Data
{
    /**
     * Check sender name validity
     *
     * @return Magento_Backend_Model_Config_Backend_Email_Sender
     */
    protected function _beforeSave()
    {
        $value = $this->getValue();
        if (!preg_match("/^[\S ]+$/", $value)) {
            Mage::throwException(
                Mage::helper('Magento_Backend_Helper_Data')
                    ->__('The sender name "%s" is not valid. Please use only visible characters and spaces.', $value)
            );
        }

        if (strlen($value) > 255) {
            Mage::throwException(
                Mage::helper('Magento_Backend_Helper_Data')
                    ->__('Maximum sender name length is 255. Please correct your settings.')
            );
        }
        return $this;
    }
}
