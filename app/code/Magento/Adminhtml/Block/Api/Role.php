<?php
/**
 * Adminhtml permissioms role block
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Adminhtml_Block_Api_Role extends Magento_Adminhtml_Block_Widget_Grid_Container
{

    protected function _construct()
    {
        $this->_controller = 'api_role';
        $this->_headerText = Mage::helper('Magento_Adminhtml_Helper_Data')->__('Roles');
        $this->_addButtonLabel = Mage::helper('Magento_Adminhtml_Helper_Data')->__('Add New Role');
        parent::_construct();
    }

}
