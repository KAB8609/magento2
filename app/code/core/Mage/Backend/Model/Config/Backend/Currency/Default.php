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
 * Config Directory currency backend model
 *
 * Allows dispatching before and after events for each controller action
 *
 * @category   Mage
 * @package    Mage_Backend
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Backend_Model_Config_Backend_Currency_Default extends Mage_Backend_Model_Config_Backend_Currency_Abstract
{
    /**
     * Check default currency is available in installed currencies
     * Check default currency is available in allowed currencies
     *
     * @return Mage_Backend_Model_Config_Backend_Currency_Default
     */
    protected function _afterSave()
    {
        if (!in_array($this->getValue(), $this->_getInstalledCurrencies())) {
            Mage::throwException(
                Mage::helper('Mage_Backend_Helper_Data')
                    ->__('Selected default display currency is not available in installed currencies.')
            );
        }

        if (!in_array($this->getValue(), $this->_getAllowedCurrencies())) {
            Mage::throwException(
                Mage::helper('Mage_Backend_Helper_Data')
                    ->__('Selected default display currency is not available in allowed currencies.')
            );
        }

        return $this;
    }
}