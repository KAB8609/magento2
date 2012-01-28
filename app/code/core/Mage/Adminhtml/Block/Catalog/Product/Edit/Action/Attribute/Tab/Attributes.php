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
 * Adminhtml catalog product edit action attributes update tab block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Catalog_Product_Edit_Action_Attribute_Tab_Attributes
    extends Mage_Adminhtml_Block_Catalog_Form
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    protected function _construct()
    {
        parent::_construct();
        $this->setShowGlobalIcon(true);
    }

    protected function _prepareForm()
    {
        $this->setFormExcludedFieldList(array(
            'tier_price','gallery', 'media_gallery', 'recurring_profile', 'group_price'
        ));
        Mage::dispatchEvent('adminhtml_catalog_product_form_prepare_excluded_field_list', array('object'=>$this));

        $form = new Varien_Data_Form();
        $fieldset = $form->addFieldset('fields', array('legend'=>Mage::helper('Mage_Catalog_Helper_Data')->__('Attributes')));
        $attributes = $this->getAttributes();
        /**
         * Initialize product object as form property
         * for using it in elements generation
         */
        $form->setDataObject(Mage::getModel('Mage_Catalog_Model_Product'));
        $this->_setFieldset($attributes, $fieldset, $this->getFormExcludedFieldList());
        $form->setFieldNameSuffix('attributes');
        $this->setForm($form);
    }

    /**
     * Retrive attributes for product massupdate
     *
     * @return array
     */
    public function getAttributes()
    {
        return $this->helper('Mage_Adminhtml_Helper_Catalog_Product_Edit_Action_Attribute')->getAttributes()->getItems();
    }

    /**
     * Additional element types for product attributes
     *
     * @return array
     */
    protected function _getAdditionalElementTypes()
    {
        return array(
            'price' => Mage::getConfig()->getBlockClassName('Mage_Adminhtml_Block_Catalog_Product_Helper_Form_Price'),
            'weight' => Mage::getConfig()->getBlockClassName('Mage_Adminhtml_Block_Catalog_Product_Helper_Form_Weight'),
            'image' => Mage::getConfig()->getBlockClassName('Mage_Adminhtml_Block_Catalog_Product_Helper_Form_Image'),
            'boolean' => Mage::getConfig()->getBlockClassName('Mage_Adminhtml_Block_Catalog_Product_Helper_Form_Boolean')
        );
    }

    /**
     * Custom additional elemnt html
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    protected function _getAdditionalElementHtml($element)
    {
        return '<span class="attribute-change-checkbox"><input type="checkbox" id="' . $element->getId()
             . '-checkbox" onclick="toogleFieldEditMode(this, \'' . $element->getId()
             . '\')" /><label for="' . $element->getId() . '-checkbox">' . Mage::helper('Mage_Catalog_Helper_Data')->__('Change')
             . '</label></span>
                <script type="text/javascript">initDisableFields(\''.$element->getId().'\')</script>';
    }

    /**
     * ######################## TAB settings #################################
     */
    public function getTabLabel()
    {
        return Mage::helper('Mage_Catalog_Helper_Data')->__('Attributes');
    }

    public function getTabTitle()
    {
        return Mage::helper('Mage_Catalog_Helper_Data')->__('Attributes');
    }

    public function canShowTab()
    {
        return true;
    }

    public function isHidden()
    {
        return false;
    }
}
