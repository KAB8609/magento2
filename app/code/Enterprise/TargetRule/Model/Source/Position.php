<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_TargetRule
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Enterprise_TargetRule_Model_Source_Position
{

    /**
     * Get data for Position behavior selector
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            Enterprise_TargetRule_Model_Rule::BOTH_SELECTED_AND_RULE_BASED =>
                Mage::helper('Enterprise_TargetRule_Helper_Data')->__('Both Selected and Rule-Based'),
            Enterprise_TargetRule_Model_Rule::SELECTED_ONLY =>
                Mage::helper('Enterprise_TargetRule_Helper_Data')->__('Selected Only'),
            Enterprise_TargetRule_Model_Rule::RULE_BASED_ONLY =>
                Mage::helper('Enterprise_TargetRule_Helper_Data')->__('Rule-Based Only'),
        );
    }

}
