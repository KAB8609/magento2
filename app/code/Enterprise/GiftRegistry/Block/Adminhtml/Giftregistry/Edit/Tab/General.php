<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_GiftRegistry
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Enterprise_GiftRegistry_Block_Adminhtml_Giftregistry_Edit_Tab_General
    extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * Return current gift registry type instance
     *
     * @return Enterprise_GiftRegistry_Model_Type
     */
    public function getType()
    {
        return Mage::registry('current_giftregistry_type');
    }

    /**
     * Prepares layout and set element renderer
     *
     * @return void
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        if ($this->getLayout()->hasElement($this->getNameInLayout() . '_element')) {
            $this->getLayout()->unsetElement($this->getNameInLayout() . '_element');
        }
        Varien_Data_Form::setFieldsetElementRenderer(
            $this->getLayout()->createBlock(
                'Enterprise_GiftRegistry_Block_Adminhtml_Giftregistry_Form_Renderer_Element',
                $this->getNameInLayout() . '_element'
            )
        );
    }

    /**
     * Prepare general properties form
     *
     * @return Enterprise_GiftRegistry_Block_Adminhtml_Giftregistry_Edit_Tab_General
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $form->setFieldNameSuffix('type');

        $fieldset = $form->addFieldset('base_fieldset', array(
            'legend'  => __('General Information')
        ));

        if ($this->getType()->getId()) {
            $fieldset->addField('type_id', 'hidden', array(
                'name' => 'type_id'
            ));
        }

        $fieldset->addField('code', 'text', array(
            'name'     => 'code',
            'label'    => __('Code'),
            'required' => true,
            'class'    => 'validate-code'
        ));

        $fieldset->addField('label', 'text', array(
            'name'     => 'label',
            'label'    => __('Label'),
            'required' => true,
            'scope'    => 'store'
        ));

        $fieldset->addField('sort_order', 'text', array(
            'name'     => 'sort_order',
            'label'    => __('Sort Order'),
            'scope'    => 'store'
        ));

        $fieldset->addField('is_listed', 'select', array(
            'label'    => __('Is Listed'),
            'name'     => 'is_listed',
            'values'   => Mage::getSingleton('Mage_Backend_Model_Config_Source_Yesno')->toOptionArray(),
            'scope'    => 'store'
        ));

        $form->setValues($this->getType()->getData());
        $this->setForm($form);
        $form->setDataObject($this->getType());

        return parent::_prepareForm();
    }
}
