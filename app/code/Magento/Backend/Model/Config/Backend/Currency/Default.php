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
 * Config Directory currency backend model
 *
 * Allows dispatching before and after events for each controller action
 *
 * @category   Mage
 * @package    Magento_Backend
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Backend_Model_Config_Backend_Currency_Default
    extends Magento_Backend_Model_Config_Backend_Currency_Abstract
{
    /**
     * Check default currency is available in installed currencies
     * Check default currency is available in allowed currencies
     *
     * @return Magento_Backend_Model_Config_Backend_Currency_Default
     */
    protected function _afterSave()
    {
        if (!in_array($this->getValue(), $this->_getInstalledCurrencies())) {
            Mage::throwException(
                Mage::helper('Magento_Backend_Helper_Data')
                    ->__('Sorry, we haven\'t installed the default display currency you selected.')
            );
        }

        if (!in_array($this->getValue(), $this->_getAllowedCurrencies())) {
            Mage::throwException(
                Mage::helper('Magento_Backend_Helper_Data')
                    ->__('Sorry, the default display currency you selected in not available in allowed currencies.')
            );
        }

        return $this;
    }
}
