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
 * Adminhtml tax rule Edit Container
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Magento_Adminhtml_Block_Checkout_Agreement_Edit extends Magento_Adminhtml_Block_Widget_Form_Container
{
    /**
     * Init class
     *
     */
    protected function _construct()
    {
        $this->_objectId = 'id';
        $this->_controller = 'checkout_agreement';

        parent::_construct();

        $this->_updateButton('save', 'label', Mage::helper('Mage_Checkout_Helper_Data')->__('Save Condition'));
        $this->_updateButton('delete', 'label', Mage::helper('Mage_Checkout_Helper_Data')->__('Delete Condition'));
    }

    /**
     * Get Header text
     *
     * @return string
     */
    public function getHeaderText()
    {
        if (Mage::registry('checkout_agreement')->getId()) {
            return Mage::helper('Mage_Checkout_Helper_Data')->__('Edit Terms and Conditions');
        }
        else {
            return Mage::helper('Mage_Checkout_Helper_Data')->__('New Terms and Conditions');
        }
    }
}
