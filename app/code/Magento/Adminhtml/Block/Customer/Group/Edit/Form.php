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
 * Adminhtml customer groups edit form
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Adminhtml_Block_Customer_Group_Edit_Form extends Magento_Adminhtml_Block_Widget_Form
{
    /**
     * Prepare form for render
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $form = new Magento_Data_Form();
        $customerGroup = Mage::registry('current_group');

        $fieldset = $form->addFieldset('base_fieldset', array('legend'=>Mage::helper('Magento_Customer_Helper_Data')->__('Group Information')));

        $validateClass = sprintf('required-entry validate-length maximum-length-%d',
            Magento_Customer_Model_Group::GROUP_CODE_MAX_LENGTH);
        $name = $fieldset->addField('customer_group_code', 'text',
            array(
                'name'  => 'code',
                'label' => Mage::helper('Magento_Customer_Helper_Data')->__('Group Name'),
                'title' => Mage::helper('Magento_Customer_Helper_Data')->__('Group Name'),
                'note'  => Mage::helper('Magento_Customer_Helper_Data')->__('Maximum length must be less then %s symbols', Magento_Customer_Model_Group::GROUP_CODE_MAX_LENGTH),
                'class' => $validateClass,
                'required' => true,
            )
        );

        if ($customerGroup->getId()==0 && $customerGroup->getCustomerGroupCode() ) {
            $name->setDisabled(true);
        }

        $fieldset->addField('tax_class_id', 'select',
            array(
                'name'  => 'tax_class',
                'label' => Mage::helper('Magento_Customer_Helper_Data')->__('Tax Class'),
                'title' => Mage::helper('Magento_Customer_Helper_Data')->__('Tax Class'),
                'class' => 'required-entry',
                'required' => true,
                'values' => Mage::getSingleton('Mage_Tax_Model_Class_Source_Customer')->toOptionArray()
            )
        );

        if (!is_null($customerGroup->getId())) {
            // If edit add id
            $form->addField('id', 'hidden',
                array(
                    'name'  => 'id',
                    'value' => $customerGroup->getId(),
                )
            );
        }

        if( Mage::getSingleton('Magento_Adminhtml_Model_Session')->getCustomerGroupData() ) {
            $form->addValues(Mage::getSingleton('Magento_Adminhtml_Model_Session')->getCustomerGroupData());
            Mage::getSingleton('Magento_Adminhtml_Model_Session')->setCustomerGroupData(null);
        } else {
            $form->addValues($customerGroup->getData());
        }

        $form->setUseContainer(true);
        $form->setId('edit_form');
        $form->setAction($this->getUrl('*/*/save'));
        $this->setForm($form);
    }
}
