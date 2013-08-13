<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Pci
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Encryption key change edit page block
 *
 */
class Enterprise_Pci_Block_Adminhtml_Crypt_Key_Edit extends Magento_Adminhtml_Block_Widget_Form_Container
{
    protected $_blockGroup = null;
    protected $_controller = 'crypt_key';

    /**
     * Instantiate save button
     *
     */
    protected function _construct()
    {
        Magento_Object::__construct();
        $this->_addButton('save', array(
            'label'     => Mage::helper('Enterprise_Pci_Helper_Data')->__('Change Encryption Key'),
            'class'     => 'save',
            'data_attribute'  => array(
                'mage-init' => array(
                    'button' => array('event' => 'save', 'target' => '#edit_form'),
                ),
            ),
        ), 1);
    }

    /**
     * Header text getter
     *
     * @return string
     */
    public function getHeaderText()
    {
        return Mage::helper('Enterprise_Pci_Helper_Data')->__('Encryption Key');
    }
}
