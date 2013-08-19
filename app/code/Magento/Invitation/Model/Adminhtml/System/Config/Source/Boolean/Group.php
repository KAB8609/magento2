<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Invitation
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Invitation source for reffered customer group system configuration
 */
class Magento_Invitation_Model_Adminhtml_System_Config_Source_Boolean_Group
{
    public function toOptionArray()
    {
        return array(
            1 => Mage::helper('Magento_Invitation_Helper_Data')->__('Same as Inviter'),
            0 => Mage::helper('Magento_Invitation_Helper_Data')->__('Default Customer Group from System Configuration')
        );
    }
}
