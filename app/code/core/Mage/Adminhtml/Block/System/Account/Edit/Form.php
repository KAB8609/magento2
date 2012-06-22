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
 * Adminhtml edit admin user account form
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Adminhtml_Block_System_Account_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $userId = Mage::getSingleton('Mage_Backend_Model_Auth_Session')->getUser()->getId();
        $user = Mage::getModel('Mage_User_Model_User')
            ->load($userId);
        $user->unsetData('password');

        $form = new Varien_Data_Form();

        $fieldset = $form->addFieldset('base_fieldset', array('legend'=>Mage::helper('Mage_Adminhtml_Helper_Data')->__('Account Information')));

        $fieldset->addField('username', 'text', array(
                'name'  => 'username',
                'label' => Mage::helper('Mage_Adminhtml_Helper_Data')->__('User Name'),
                'title' => Mage::helper('Mage_Adminhtml_Helper_Data')->__('User Name'),
                'required' => true,
            )
        );

        $fieldset->addField('firstname', 'text', array(
                'name'  => 'firstname',
                'label' => Mage::helper('Mage_Adminhtml_Helper_Data')->__('First Name'),
                'title' => Mage::helper('Mage_Adminhtml_Helper_Data')->__('First Name'),
                'required' => true,
            )
        );

        $fieldset->addField('lastname', 'text', array(
                'name'  => 'lastname',
                'label' => Mage::helper('Mage_Adminhtml_Helper_Data')->__('Last Name'),
                'title' => Mage::helper('Mage_Adminhtml_Helper_Data')->__('Last Name'),
                'required' => true,
            )
        );

        $fieldset->addField('user_id', 'hidden', array(
                'name'  => 'user_id',
            )
        );

        $fieldset->addField('email', 'text', array(
                'name'  => 'email',
                'label' => Mage::helper('Mage_Adminhtml_Helper_Data')->__('Email'),
                'title' => Mage::helper('Mage_Adminhtml_Helper_Data')->__('User Email'),
                'required' => true,
            )
        );

        $fieldset->addField('password', 'password', array(
                'name'  => 'new_password',
                'label' => Mage::helper('Mage_Adminhtml_Helper_Data')->__('New Password'),
                'title' => Mage::helper('Mage_Adminhtml_Helper_Data')->__('New Password'),
                'class' => 'input-text validate-admin-password',
            )
        );

        $fieldset->addField('confirmation', 'password', array(
                'name'  => 'password_confirmation',
                'label' => Mage::helper('Mage_Adminhtml_Helper_Data')->__('Password Confirmation'),
                'class' => 'input-text validate-cpassword',
            )
        );

        $form->setValues($user->getData());
        $form->setAction($this->getUrl('*/system_account/save'));
        $form->setMethod('post');
        $form->setUseContainer(true);
        $form->setId('edit_form');

        $this->setForm($form);

        return parent::_prepareForm();
    }
}
