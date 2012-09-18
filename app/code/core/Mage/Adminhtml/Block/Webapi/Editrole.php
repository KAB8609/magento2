<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Adminhtml_Block_Webapi_Editrole extends Mage_Adminhtml_Block_Widget_Tabs
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('role_info_tabs');
        $this->setDestElementId('role_edit_form');
        $this->setTitle(Mage::helper('Mage_Adminhtml_Helper_Data')->__('Role Information'));
    }

    protected function _beforeToHtml()
    {
        $roleId = $this->getRequest()->getParam('rid', false);
        $role = Mage::getModel('Mage_Webapi_Model_Acl_Role')
           ->load($roleId);

        $this->addTab('info', array(
            'label'     => Mage::helper('Mage_Adminhtml_Helper_Data')->__('Role Info'),
            'title'     => Mage::helper('Mage_Adminhtml_Helper_Data')->__('Role Info'),
            'content'   => $this->getLayout()->createBlock(
                'Mage_Adminhtml_Block_Webapi_Tab_Roleinfo'
            )->setRole($role)->toHtml(),
            'active'    => true
        ));

        $this->addTab('account', array(
            'label'     => Mage::helper('Mage_Adminhtml_Helper_Data')->__('Role Resources'),
            'title'     => Mage::helper('Mage_Adminhtml_Helper_Data')->__('Role Resources'),
            'content'   => $this->getLayout()->createBlock('Mage_Adminhtml_Block_Webapi_Tab_Rolesedit')->toHtml(),
        ));

        if( intval($roleId) > 0 ) {
            $this->addTab('roles', array(
                'label'     => Mage::helper('Mage_Adminhtml_Helper_Data')->__('Role Users'),
                'title'     => Mage::helper('Mage_Adminhtml_Helper_Data')->__('Role Users'),
                'content'   => $this->getLayout()->createBlock(
                    'Mage_Adminhtml_Block_Webapi_Tab_Rolesusers',
                    'role.users.grid'
                )->toHtml(),
            ));
        }
        return parent::_beforeToHtml();
    }
}
