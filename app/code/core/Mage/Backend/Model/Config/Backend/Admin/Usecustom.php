<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Config backend model for "Use Custom Admin URL" option
 *
 * @category   Mage
 * @package    Mage_Backend
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Backend_Model_Config_Backend_Admin_Usecustom extends Mage_Core_Model_Config_Data
{
    /**
     * Validate custom url
     *
     * @return Mage_Backend_Model_Config_Backend_Admin_Usecustom
     */
    protected function _beforeSave()
    {
        $value = $this->getValue();
        if ($value == 1) {
            $customUrl = $this->getData('groups/url/fields/custom/value');
            if (empty($customUrl)) {
                Mage::throwException(Mage::helper('Mage_Backend_Helper_Data')->__('Please specify the admin custom URL.'));
            }
        }

        return $this;
    }

    /**
     * Delete custom admin url from configuration if "Use Custom Admin Url" option disabled
     *
     * @return Mage_Backend_Model_Config_Backend_Admin_Usecustom
     */
    protected function _afterSave()
    {
        $value = $this->getValue();

        if (!$value) {
            Mage::getConfig()->deleteConfig(
                Mage_Backend_Model_Config_Backend_Admin_Custom::XML_PATH_SECURE_BASE_URL,
                Mage_Backend_Model_Config_Backend_Admin_Custom::CONFIG_SCOPE,
                Mage_Backend_Model_Config_Backend_Admin_Custom::CONFIG_SCOPE_ID
            );
            Mage::getConfig()->deleteConfig(
                Mage_Backend_Model_Config_Backend_Admin_Custom::XML_PATH_UNSECURE_BASE_URL,
                Mage_Backend_Model_Config_Backend_Admin_Custom::CONFIG_SCOPE,
                Mage_Backend_Model_Config_Backend_Admin_Custom::CONFIG_SCOPE_ID
            );
        }

        return $this;
    }
}
