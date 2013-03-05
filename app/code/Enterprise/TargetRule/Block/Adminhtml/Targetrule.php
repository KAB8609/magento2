<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_TargetRule
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Enterprise_TargetRule_Block_Adminhtml_Targetrule extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    /**
     * Initialize invitation manage page
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml_targetrule';
        $this->_blockGroup = 'Enterprise_TargetRule';
        $this->_headerText = Mage::helper('Enterprise_TargetRule_Helper_Data')->__('Manage Product Rules');
        $this->_addButtonLabel = Mage::helper('Enterprise_TargetRule_Helper_Data')->__('Add Rule');
        parent::_construct();
    }

}
