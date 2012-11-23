<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Customer
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Create New Form Type Block
 *
 * @category   Enterprise
 * @package    Enterprise_Customer
 */
class Enterprise_Customer_Block_Adminhtml_Customer_Formtype_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    /**
     * Retrieve current form type instance
     *
     * @return Mage_Eav_Model_Form_Type
     */
    protected function _getFormType()
    {
        return Mage::registry('current_form_type');
    }

    /**
     * Initialize Form Container
     *
     */
    protected function _construct()
    {
        $this->_objectId   = 'type_id';
        $this->_blockGroup = 'Enterprise_Customer';
        $this->_controller = 'adminhtml_customer_formtype';

        parent::_construct();

        $editMode = Mage::registry('edit_mode');
        if ($editMode == 'edit') {
            $this->_updateButton('save', 'onclick', 'formType.save(false)');
            $this->_updateButton('save', 'data_attr', null);
            $this->_addButton('save_and_edit_button', array(
                'label'     => Mage::helper('Enterprise_Customer_Helper_Data')->__('Save and Continue Edit'),
                'onclick'   => 'formType.save(true)',
                'class'     => 'save'
            ));

            if ($this->_getFormType()->getIsSystem()) {
                $this->_removeButton('delete');
            }

            $this->_headerText = Mage::helper('Enterprise_Customer_Helper_Data')->__('Edit Form Type "%s"', $this->_getFormType()->getCode());
        } else {
            $this->_headerText = Mage::helper('Enterprise_Customer_Helper_Data')->__('New Form Type');
        }
    }
}
