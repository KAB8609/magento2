<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_TargetRule
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_TargetRule_Model_Source_Rotation
{

    /**
     * Get data for Rotation mode selector
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            Magento_TargetRule_Model_Rule::ROTATION_NONE =>
                Mage::helper('Magento_TargetRule_Helper_Data')->__('Do not rotate'),
            Magento_TargetRule_Model_Rule::ROTATION_SHUFFLE =>
                Mage::helper('Magento_TargetRule_Helper_Data')->__('Shuffle'),
        );
    }

}
