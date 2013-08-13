<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Customer group edit block
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Adminhtml_Block_Customer_Group_Edit extends Magento_Adminhtml_Block_Widget_Form_Container
{

    protected function _construct()
    {
        parent::_construct();

        $this->_objectId = 'id';
        $this->_controller = 'customer_group';

        $this->_updateButton('save', 'label', Mage::helper('Mage_Customer_Helper_Data')->__('Save Customer Group'));
        $this->_updateButton('delete', 'label', Mage::helper('Mage_Customer_Helper_Data')->__('Delete Customer Group'));

        $group = Mage::registry('current_group');
        if(!$group || !$group->getId() || $group->usesAsDefault()) {
            $this->_removeButton('delete');
        }
    }

    public function getHeaderText()
    {
        if(!is_null(Mage::registry('current_group')->getId())) {
            return Mage::helper('Mage_Customer_Helper_Data')->__('Edit Customer Group "%s"', $this->escapeHtml(Mage::registry('current_group')->getCustomerGroupCode()));
        } else {
            return Mage::helper('Mage_Customer_Helper_Data')->__('New Customer Group');
        }
    }

    public function getHeaderCssClass() {
        return 'icon-head head-customer-groups';
    }
}
