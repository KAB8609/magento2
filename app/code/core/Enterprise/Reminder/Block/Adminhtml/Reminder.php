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
 * Reminder Adminhtml Block
 */
class Enterprise_Reminder_Block_Adminhtml_Reminder extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    /**
     * Initialize invitation manage page
     *
     * @return void
     */
    public function __construct()
    {
        $this->_blockGroup = 'Enterprise_Reminder';
        $this->_controller = 'adminhtml_reminder';
        $this->_headerText = Mage::helper('Enterprise_Reminder_Helper_Data')->__('Automated Email Marketing Reminder Rules');
        $this->_addButtonLabel = Mage::helper('Enterprise_Reminder_Helper_Data')->__('Add New Rule');
        parent::__construct();
    }
}
