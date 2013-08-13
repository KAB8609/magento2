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

class Magento_Adminhtml_Block_Catalog_Product_Attribute_Set_Main_Formgroup extends Magento_Adminhtml_Block_Widget_Form
{

    protected function _prepareForm()
    {
        $form = new Magento_Data_Form();

        $fieldset = $form->addFieldset('set_fieldset', array('legend'=>Mage::helper('Magento_Catalog_Helper_Data')->__('Add New Group')));

        $fieldset->addField('attribute_group_name', 'text',
                            array(
                                'label' => Mage::helper('Magento_Catalog_Helper_Data')->__('Name'),
                                'name' => 'attribute_group_name',
                                'required' => true,
                            )
        );

        $fieldset->addField('submit', 'note',
                            array(
                                'text' => $this->getLayout()->createBlock('Magento_Adminhtml_Block_Widget_Button')
                                            ->setData(array(
                                                'label'     => Mage::helper('Magento_Catalog_Helper_Data')->__('Add Group'),
                                                'onclick'   => 'this.form.submit();',
                                                                                                'class' => 'add'
                                            ))
                                            ->toHtml(),
                            )
        );

        $fieldset->addField('attribute_set_id', 'hidden',
                            array(
                                'name' => 'attribute_set_id',
                                'value' => $this->_getSetId(),
                            )

        );

        $form->setUseContainer(true);
        $form->setMethod('post');
        $form->setAction($this->getUrl('*/catalog_product_group/save'));
        $this->setForm($form);
    }

    protected function _getSetId()
    {
        return ( intval($this->getRequest()->getParam('id')) > 0 )
                    ? intval($this->getRequest()->getParam('id'))
                    : Mage::getModel('Mage_Eav_Model_Entity_Type')
                        ->load(Mage::registry('entityType'))
                        ->getDefaultAttributeSetId();
    }
}
