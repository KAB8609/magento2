<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Customer
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Customer sharing config model
 *
 * @category   Mage
 * @package    Magento_Customer
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Customer_Model_Config_Share extends Magento_Core_Model_Config_Data
{
    /**
     * Xml config path to customers sharing scope value
     *
     */
    const XML_PATH_CUSTOMER_ACCOUNT_SHARE = 'customer/account_share/scope';
    
    /**
     * Possible customer sharing scopes
     *
     */
    const SHARE_GLOBAL  = 0;
    const SHARE_WEBSITE = 1;

    /**
     * Check whether current customers sharing scope is global
     *
     * @return bool
     */
    public function isGlobalScope()
    {
        return !$this->isWebsiteScope();
    }

    /**
     * Check whether current customers sharing scope is website
     *
     * @return bool
     */
    public function isWebsiteScope()
    {
        return Mage::getStoreConfig(self::XML_PATH_CUSTOMER_ACCOUNT_SHARE) == self::SHARE_WEBSITE;
    }

    /**
     * Get possible sharing configuration options
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            self::SHARE_GLOBAL  => Mage::helper('Magento_Customer_Helper_Data')->__('Global'),
            self::SHARE_WEBSITE => Mage::helper('Magento_Customer_Helper_Data')->__('Per Website'),
        );
    }

    /**
     * Check for email dublicates before saving customers sharing options
     *
     * @return Magento_Customer_Model_Config_Share
     * @throws Magento_Core_Exception
     */
    public function _beforeSave()
    {
        $value = $this->getValue();
        if ($value == self::SHARE_GLOBAL) {
            if (Mage::getResourceSingleton('Magento_Customer_Model_Resource_Customer')->findEmailDuplicates()) {
                Mage::throwException(
                    Mage::helper('Magento_Customer_Helper_Data')->__('Cannot share customer accounts globally because some customer accounts with the same emails exist on multiple websites and cannot be merged.')
                );
            }
        }
        return $this;
    }
}
