<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CustomerCustomAttributes
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Customer Address Attribute Edit Container Block
 *
 * @category    Magento
 * @package     Magento_CustomerCustomAttributes
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_CustomerCustomAttributes_Block_Adminhtml_Customer_Address_Attribute_Edit
    extends Magento_Adminhtml_Block_Widget_Form_Container
{
    /**
     * Return current customer address attribute instance
     *
     * @return Magento_Customer_Model_Attribute
     */
    protected function _getAttribute()
    {
        return Mage::registry('entity_attribute');
    }

    /**
     * Initialize Customer Address Attribute Edit Container
     *
     */
    protected function _construct()
    {
        $this->_objectId   = 'attribute_id';
        $this->_blockGroup = 'Magento_CustomerCustomAttributes';
        $this->_controller = 'adminhtml_customer_address_attribute';

        parent::_construct();

        $this->_addButton(
            'save_and_edit_button',
            array(
                'label'     => Mage::helper('Magento_CustomerCustomAttributes_Helper_Data')->__('Save and Continue Edit'),
                'class'     => 'save',
                'data_attribute'  => array(
                    'mage-init' => array(
                        'button' => array('event' => 'saveAndContinueEdit', 'target' => '#edit_form'),
                    ),
                ),
            ),
            100
        );

        $this->_updateButton('save', 'label', Mage::helper('Magento_CustomerCustomAttributes_Helper_Data')->__('Save Attribute'));
        if (!$this->_getAttribute() || !$this->_getAttribute()->getIsUserDefined()) {
            $this->_removeButton('delete');
        } else {
            $this->_updateButton('delete', 'label', Mage::helper('Magento_CustomerCustomAttributes_Helper_Data')->__('Delete Attribute'));
        }
    }

    /**
     * Return header text for edit block
     *
     * @return string
     */
    public function getHeaderText()
    {
        if ($this->_getAttribute()->getId()) {
            $label = $this->_getAttribute()->getFrontendLabel();
            if (is_array($label)) {
                // restored label
                $label = $label[0];
            }
            return Mage::helper('Magento_CustomerCustomAttributes_Helper_Data')->__('Edit Customer Address Attribute "%s"', $label);
        } else {
            return Mage::helper('Magento_CustomerCustomAttributes_Helper_Data')->__('New Customer Address Attribute');
        }
    }

    /**
     * Return validation URL for edit form
     *
     * @return string
     */
    public function getValidationUrl()
    {
        return $this->getUrl('*/*/validate', array('_current' => true));
    }

    /**
     * Return save url for edit form
     *
     * @return string
     */
    public function getSaveUrl()
    {
        return $this->getUrl('*/*/save', array('_current' => true, 'back' => null));
    }
}
