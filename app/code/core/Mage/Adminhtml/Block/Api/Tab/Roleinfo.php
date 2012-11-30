<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * implementing now
 *
 */
class Mage_Adminhtml_Block_Api_Tab_Roleinfo extends Mage_Adminhtml_Block_Widget_Form
{
    public function _beforeToHtml() {
        $this->_initForm();

        return parent::_beforeToHtml();
    }

    protected function _initForm()
    {
        $roleId = $this->getRequest()->getParam('rid');

        $form = new Varien_Data_Form();

        $fieldset = $form->addFieldset('base_fieldset', array('legend'=>Mage::helper('Mage_Adminhtml_Helper_Data')->__('Role Information')));

        $fieldset->addField('role_name', 'text',
            array(
                'name'  => 'rolename',
                'label' => Mage::helper('Mage_Adminhtml_Helper_Data')->__('Role Name'),
                'id'    => 'role_name',
                'class' => 'required-entry',
                'required' => true,
            )
        );

        $fieldset->addField('role_id', 'hidden',
            array(
                'name'  => 'role_id',
                'id'    => 'role_id',
            )
        );

        $fieldset->addField('in_role_user', 'hidden',
            array(
                'name'  => 'in_role_user',
                'id'    => 'in_role_userz',
            )
        );

        $fieldset->addField('in_role_user_old', 'hidden', array('name' => 'in_role_user_old'));

        $form->setValues($this->getRole()->getData());
        $this->setForm($form);
    }
}
