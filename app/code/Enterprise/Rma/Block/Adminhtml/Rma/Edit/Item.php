<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Rma
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * User-attributes block for RMA Item  in Admin RMA edit
 *
 * @category    Enterprise
 * @package     Enterprise_Rma
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Rma_Block_Adminhtml_Rma_Edit_Item extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * Preparing form - container, which contains all attributes
     *
     * @return Enterprise_Rma_Block_Adminhtml_Rma_Edit_Item
     */
    public function initForm()
    {
        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('_rma');
        $form->setFieldNameSuffix();

        $item = Mage::registry('current_rma_item');

        if (!$item->getId()) {
            // for creating RMA process when we have no item loaded, $item is just empty model
            $this->_populateItemWithProductData($item);
        }

        /* @var $customerForm Mage_Customer_Model_Form */
        $customerForm = Mage::getModel('Enterprise_Rma_Model_Item_Form');
        $customerForm->setEntity($item)
            ->setFormCode('default')
            ->initDefaultValues();

        $fieldset = $form->addFieldset('base_fieldset',
            array('legend'=>Mage::helper('Enterprise_Rma_Helper_Data')->__('RMA Item Details'))
        );

        $fieldset->setProductName($this->escapeHtml($item->getProductAdminName()));
        $okButton = $this->getLayout()->createBlock('Mage_Adminhtml_Block_Widget_Button')
            ->setData(array(
                'label'   => Mage::helper('Enterprise_Rma_Helper_Data')->__('OK'),
                'class'   => 'ok_button',
            ));
        $fieldset->setOkButton($okButton->toHtml());

        $cancelButton = $this->getLayout()->createBlock('Mage_Adminhtml_Block_Widget_Button')
            ->setData(array(
                'label'   => Mage::helper('Enterprise_Rma_Helper_Data')->__('Cancel'),
                'class'   => 'cancel_button',
            ));
        $fieldset->setCancelButton($cancelButton->toHtml());


        $attributes = $customerForm->getUserAttributes();

        foreach ($attributes as $attribute) {
            $attribute->unsIsVisible();
        }
        $this->_setFieldset($attributes, $fieldset);

        $form->setValues($item->getData());
        $this->setForm($form);
        return $this;
    }

    /**
     * Preparing global layout
     *
     * You can redefine this method in child classes for changin layout
     *
     * @return Mage_Core_Block_Abstract
     */
    protected function _prepareLayout()
    {
        Varien_Data_Form::setElementRenderer(
            $this->getLayout()->createBlock(
                'Mage_Adminhtml_Block_Widget_Form_Renderer_Element',
                $this->getNameInLayout() . '_element'
            )
        );
        Varien_Data_Form::setFieldsetRenderer(
            $this->getLayout()->createBlock(
                'Enterprise_Rma_Block_Adminhtml_Rma_Edit_Item_Renderer_Fieldset',
                $this->getNameInLayout() . '_fieldset'
            )
        );
        Varien_Data_Form::setFieldsetElementRenderer(
            $this->getLayout()->createBlock(
                'Mage_Adminhtml_Block_Widget_Form_Renderer_Fieldset_Element',
                $this->getNameInLayout() . '_fieldset_element'
            )
        );

        return $this;
    }

    /**
     * Return predefined additional element types
     *
     * @return array
     */
    protected function _getAdditionalElementTypes()
    {
        return array(
            'text' => 'Enterprise_Rma_Block_Adminhtml_Rma_Edit_Item_Form_Element_Text',
            'textarea' => 'Enterprise_Rma_Block_Adminhtml_Rma_Edit_Item_Form_Element_Textarea',
            'image' => 'Enterprise_Rma_Block_Adminhtml_Rma_Edit_Item_Form_Element_Image',
        );
    }

    /**
     * Add needed data (Product name) to RMA item during create process
     *
     * @param Enterprise_Rma_Model_Item $item
     */
    protected function _populateItemWithProductData($item)
    {
        if ($this->getProductId()) {
            $orderItem = Mage::getModel('Mage_Sales_Model_Order_Item')->load($this->getProductId());
            if ($orderItem && $orderItem->getId()) {
                $item->setProductAdminName(Mage::helper('Enterprise_Rma_Helper_Data')->getAdminProductName($orderItem));
            }
        }
    }
}
