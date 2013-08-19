<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Reward
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Source model for list of Expiry Calculation algorythms
 */
class Magento_Reward_Model_Source_Points_ExpiryCalculation
{
    public function toOptionArray()
    {
        return array(
            array('value' => 'static', 'label' => Mage::helper('Magento_Reward_Helper_Data')->__('Static')),
            array('value' => 'dynamic', 'label' => Mage::helper('Magento_Reward_Helper_Data')->__('Dynamic')),
        );
    }
}
