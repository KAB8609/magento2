<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Sales orders grid massaction items updater
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Sales_Model_Order_Grid_Massaction_ItemsUpdater implements Mage_Core_Model_Layout_Argument_UpdaterInterface
{
    /**
     * Remove massaction items in case they disallowed for user
     * @param mixed $argument
     * @return mixed
     */
    public function update($argument)
    {
        if (false === Mage::getSingleton('Mage_Core_Model_Authorization')->isAllowed('Mage_Sales::cancel')) {
            unset($argument['cancel_order']);
        }

        if (false === Mage::getSingleton('Mage_Core_Model_Authorization')->isAllowed('Mage_Sales::hold')) {
            unset($argument['hold_order']);
        }

        if (false === Mage::getSingleton('Mage_Core_Model_Authorization')->isAllowed('Mage_Sales::unhold')) {
            unset($argument['unhold_order']);
        }

        return $argument;
    }
}
