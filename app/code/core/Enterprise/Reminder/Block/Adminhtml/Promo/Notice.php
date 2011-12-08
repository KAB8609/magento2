<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Reminder
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Reminder adminhtml promo rules notice block
 */
class Enterprise_Reminder_Block_Adminhtml_Promo_Notice extends Mage_Adminhtml_Block_Template
{
    /**
     * Preparing block layout
     *
     * @return Enterprise_Reminder_Block_Adminhtml_Promo_Notice
     */
    protected function _prepareLayout()
    {
        if ($salesRule = Mage::registry('current_promo_quote_rule')) {
            $resource = Mage::getResourceModel('Enterprise_Reminder_Model_Resource_Rule');
            if ($count = $resource->getAssignedRulesCount($salesRule->getId())) {
                $confirm = Mage::helper('Enterprise_Reminder_Helper_Data')->__('This rule is assigned to %s automated reminder rule(s). Deleting this rule will automatically unassign it.', $count);
                $block = $this->getLayout()->getBlock('promo_quote_edit');
                if ($block instanceof Mage_Adminhtml_Block_Promo_Quote_Edit) {
                    $block->updateButton('delete', 'onclick', 'deleteConfirm(\'' . $confirm . '\', \'' . $block->getDeleteUrl() . '\')');
                }
            }
        }
        return $this;
    }
}
