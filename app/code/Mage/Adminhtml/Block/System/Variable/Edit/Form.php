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
 * Custom Variable Edit Form
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_System_Variable_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * Getter
     *
     * @return Mage_Core_Model_Variable
     */
    public function getVariable()
    {
        return Mage::registry('current_variable');
    }

    /**
     * Prepare form before rendering HTML
     *
     * @return Mage_Adminhtml_Block_System_Variable_Edit_Form
     */
    protected function _prepareForm()
    {
        $form = new Magento_Data_Form(array(
            'id' => 'edit_form',
            'action' => $this->getData('action'),
            'method' => 'post'
        ));

        $fieldset = $form->addFieldset('base', array(
            'legend'=>Mage::helper('Mage_Adminhtml_Helper_Data')->__('Variable'),
            'class'=>'fieldset-wide'
        ));

        $fieldset->addField('code', 'text', array(
            'name'     => 'code',
            'label'    => Mage::helper('Mage_Adminhtml_Helper_Data')->__('Variable Code'),
            'title'    => Mage::helper('Mage_Adminhtml_Helper_Data')->__('Variable Code'),
            'required' => true,
            'class'    => 'validate-xml-identifier'
        ));

        $fieldset->addField('name', 'text', array(
            'name'     => 'name',
            'label'    => Mage::helper('Mage_Adminhtml_Helper_Data')->__('Variable Name'),
            'title'    => Mage::helper('Mage_Adminhtml_Helper_Data')->__('Variable Name'),
            'required' => true
        ));

        $useDefault = false;
        if ($this->getVariable()->getId() && $this->getVariable()->getStoreId()) {
            $useDefault = !((bool)$this->getVariable()->getStoreHtmlValue());
            $this->getVariable()->setUseDefaultValue((int)$useDefault);
            $fieldset->addField('use_default_value', 'select', array(
                'name'   => 'use_default_value',
                'label'  => Mage::helper('Mage_Adminhtml_Helper_Data')->__('Use Default Variable Values'),
                'title'  => Mage::helper('Mage_Adminhtml_Helper_Data')->__('Use Default Variable Values'),
                'onchange' => 'toggleValueElement(this);',
                'values' => array(
                    0 => Mage::helper('Mage_Adminhtml_Helper_Data')->__('No'),
                    1 => Mage::helper('Mage_Adminhtml_Helper_Data')->__('Yes')
                )
            ));
        }

        $fieldset->addField('html_value', 'textarea', array(
            'name'     => 'html_value',
            'label'    => Mage::helper('Mage_Adminhtml_Helper_Data')->__('Variable HTML Value'),
            'title'    => Mage::helper('Mage_Adminhtml_Helper_Data')->__('Variable HTML Value'),
            'disabled' => $useDefault
        ));

        $fieldset->addField('plain_value', 'textarea', array(
            'name'     => 'plain_value',
            'label'    => Mage::helper('Mage_Adminhtml_Helper_Data')->__('Variable Plain Value'),
            'title'    => Mage::helper('Mage_Adminhtml_Helper_Data')->__('Variable Plain Value'),
            'disabled' => $useDefault
        ));

        $form->setValues($this->getVariable()->getData())
            ->addFieldNameSuffix('variable')
            ->setUseContainer(true);

        $this->setForm($form);
        return parent::_prepareForm();
    }

}
