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
 * Reminder rule edit form block
 */
class Enterprise_Reminder_Block_Adminhtml_Reminder_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    /**
     * Initialize form
     * Add standard buttons
     * Add "Run Now" button
     * Add "Save and Continue" button
     */
    public function __construct()
    {
        $this->_objectId = 'id';
        $this->_blockGroup = 'Enterprise_Reminder';
        $this->_controller = 'adminhtml_reminder';

        parent::__construct();

        /** @var $rule Enterprise_Reminder_Model_Rule */
        $rule = Mage::registry('current_reminder_rule');
        if ($rule && $rule->getId()) {
            $confirm = Mage::helper('Enterprise_Reminder_Helper_Data')->__('Are you sure you want to match this rule now?');
            if ($limit = Mage::helper('Enterprise_Reminder_Helper_Data')->getOneRunLimit()) {
                $confirm .= ' ' . Mage::helper('Enterprise_Reminder_Helper_Data')->__('Up to %s customers may receive reminder email after this action.', $limit);
            }
            $this->_addButton('run_now', array(
                'label'   => Mage::helper('Enterprise_Reminder_Helper_Data')->__('Run Now'),
                'onclick' => "confirmSetLocation('{$confirm}', '{$this->getRunUrl()}')"
            ), -1);
        }

        $this->_addButton('save_and_continue_edit', array(
            'class'   => 'save',
            'label'   => Mage::helper('Enterprise_Reminder_Helper_Data')->__('Save and Continue Edit'),
            'data_attr'  => array(
                'widget-button' => array('event' => 'saveAndContinueEdit', 'related' => '#edit_form'),
            ),
        ), 3);
    }

    /**
     * Getter for form header text
     *
     * @return string
     */
    public function getHeaderText()
    {
        $rule = Mage::registry('current_reminder_rule');
        if ($rule->getRuleId()) {
            return Mage::helper('Enterprise_Reminder_Helper_Data')->__("Edit Rule '%s'", $this->escapeHtml($rule->getName()));
        }
        else {
            return Mage::helper('Enterprise_Reminder_Helper_Data')->__('New Rule');
        }
    }

    /**
     * Get url for immediate run sending process
     *
     * @return string
     */
    public function getRunUrl()
    {
        $rule = Mage::registry('current_reminder_rule');
        return $this->getUrl('*/*/run', array('id' => $rule->getRuleId()));
    }
}
