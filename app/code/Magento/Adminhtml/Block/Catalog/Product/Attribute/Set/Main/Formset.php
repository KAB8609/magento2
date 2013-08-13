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
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Magento_Adminhtml_Block_Catalog_Product_Attribute_Set_Main_Formset extends Magento_Adminhtml_Block_Widget_Form
{

    /**
     * Prepares attribute set form
     *
     */
    protected function _prepareForm()
    {
        $data = Mage::getModel('Mage_Eav_Model_Entity_Attribute_Set')
            ->load($this->getRequest()->getParam('id'));

        $form = new Magento_Data_Form();
        $fieldset = $form->addFieldset('set_name', array('legend'=> Mage::helper('Mage_Catalog_Helper_Data')->__('Edit Set Name')));
        $fieldset->addField('attribute_set_name', 'text', array(
            'label' => Mage::helper('Mage_Catalog_Helper_Data')->__('Name'),
            'note' => Mage::helper('Mage_Catalog_Helper_Data')->__('For internal use'),
            'name' => 'attribute_set_name',
            'required' => true,
            'class' => 'required-entry validate-no-html-tags',
            'value' => $data->getAttributeSetName()
        ));

        if( !$this->getRequest()->getParam('id', false) ) {
            $fieldset->addField('gotoEdit', 'hidden', array(
                'name' => 'gotoEdit',
                'value' => '1'
            ));

            $sets = Mage::getModel('Mage_Eav_Model_Entity_Attribute_Set')
                ->getResourceCollection()
                ->setEntityTypeFilter(Mage::registry('entityType'))
                ->load()
                ->toOptionArray();

            $fieldset->addField('skeleton_set', 'select', array(
                'label' => Mage::helper('Mage_Catalog_Helper_Data')->__('Based On'),
                'name' => 'skeleton_set',
                'required' => true,
                'class' => 'required-entry',
                'values' => $sets,
            ));
        }

        $form->setMethod('post');
        $form->setUseContainer(true);
        $form->setId('set-prop-form');
        $form->setAction($this->getUrl('*/*/save'));
        $form->setOnsubmit('return false;');
        $this->setForm($form);
    }
}
