<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_User
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * User edit page
 *
 * @category   Magento
 * @package    Magento_User
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_User_Block_User_Edit extends Magento_Backend_Block_Widget_Form_Container
{
    protected function _construct()
    {
        $this->_objectId = 'user_id';
        $this->_controller = 'user';
        $this->_blockGroup = 'Magento_User';

        parent::_construct();

        $this->_updateButton('save', 'label', __('Save User'));
        $this->_updateButton('delete', 'label', __('Delete User'));
    }

    public function getHeaderText()
    {
        if (Mage::registry('permissions_user')->getId()) {
            $username = $this->escapeHtml(Mage::registry('permissions_user')->getUsername());
            return __("Edit User '%1'", $username);
        } else {
            return __('New User');
        }
    }

}