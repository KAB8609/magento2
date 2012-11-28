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
 * Product attribute edit page
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */

class Mage_Adminhtml_Block_Catalog_Product_Attribute_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{

    protected function _construct()
    {
        $this->_objectId = 'attribute_id';
        $this->_controller = 'catalog_product_attribute';

        parent::_construct();

        if($this->getRequest()->getParam('popup')) {
            $this->_removeButton('back');
            $this->_addButton(
                'close',
                array(
                    'label'     => Mage::helper('Mage_Catalog_Helper_Data')->__('Close Window'),
                    'class'     => 'cancel',
                    'onclick'   => 'window.close()',
                    'level'     => -1
                )
            );
            $this->_addButton(
                'save_in_new_set',
                array(
                    'label'     => Mage::helper('Mage_Catalog_Helper_Data')->__('Save in New Attribute Set'),
                    'class'     => 'save',
                    'onclick'   => 'saveAttributeInNewSet(\''
                        . Mage::helper('Mage_Catalog_Helper_Data')->__('Enter Name for New Attribute Set')
                        . '\')',
                )
            );
        } else {
            $this->_addButton(
                'save_and_edit_button',
                array(
                    'label'     => Mage::helper('Mage_Catalog_Helper_Data')->__('Save and Continue Edit'),
                    'class'     => 'save',
                    'data_attr'  => array(
                        'widget-button' => array('event' => 'saveAndContinueEdit', 'related' => '#edit_form'),
                    ),
                ),
                100
            );
        }

        $this->_updateButton('save', 'label', Mage::helper('Mage_Catalog_Helper_Data')->__('Save Attribute'));
        $this->_updateButton('save', 'data_attr', array(
            'widget-button' => array('event' => 'save', 'related' => '#edit_form')
        ));

        if (!Mage::registry('entity_attribute') || !Mage::registry('entity_attribute')->getIsUserDefined()) {
            $this->_removeButton('delete');
        } else {
            $this->_updateButton('delete', 'label', Mage::helper('Mage_Catalog_Helper_Data')->__('Delete Attribute'));
        }
    }

    /**
     * Retrieve header text
     *
     * @return string
     */
    public function getHeaderText()
    {
        if (Mage::registry('entity_attribute')->getId()) {
            $frontendLabel = Mage::registry('entity_attribute')->getFrontendLabel();
            if (is_array($frontendLabel)) {
                $frontendLabel = $frontendLabel[0];
            }
            return Mage::helper('Mage_Catalog_Helper_Data')->__('Edit Product Attribute "%s"', $this->escapeHtml($frontendLabel));
        }
        return Mage::helper('Mage_Catalog_Helper_Data')->__('New Product Attribute');
    }

    /**
     * Retrieve URL for validation
     *
     * @return string
     */
    public function getValidationUrl()
    {
        return $this->getUrl('*/*/validate', array('_current'=>true));
    }

    /**
     * Retrieve URL for save
     *
     * @return string
     */
    public function getSaveUrl()
    {
        return $this->getUrl('*/'.$this->_controller.'/save', array('_current'=>true, 'back'=>null));
    }
}
