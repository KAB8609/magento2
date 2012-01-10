<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_CustomerSegment
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Conditions tab of customer segment configuration
 *
 * @category    Enterprise
 * @package     Enterprise_CustomerSegment
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_CustomerSegment_Block_Adminhtml_Customersegment_Edit_Tab_Conditions
    extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * Prepare conditions form
     *
     * @return Enterprise_CustomerSegment_Block_Adminhtml_Customersegment_Edit_Tab_Conditions
     */
    protected function _prepareForm()
    {
        $model = Mage::registry('current_customer_segment');

        $form = new Varien_Data_Form();

        $form->setHtmlIdPrefix('segment_');
        $params = array('apply_to' => $model->getApplyTo());
        $url = $this->getUrl('*/customersegment/newConditionHtml/form/segment_conditions_fieldset', $params);

        $renderer = Mage::getBlockSingleton('Mage_Adminhtml_Block_Widget_Form_Renderer_Fieldset')
            ->setTemplate('promo/fieldset.phtml')
            ->setNewChildUrl($url);
        $fieldset = $form->addFieldset('conditions_fieldset', array(
            'legend' => Mage::helper('enterprise_customersegment')->__('Conditions'),
            'class' => 'form-list',
        ))->setRenderer($renderer);

        $fieldset->addField('conditions', 'text', array(
            'name' => 'conditions',
            'label' => Mage::helper('Enterprise_CustomerSegment_Helper_Data')->__('Conditions'),
            'title' => Mage::helper('Enterprise_CustomerSegment_Helper_Data')->__('Conditions'),
            'required' => true,
        ))->setRule($model)->setRenderer(Mage::getBlockSingleton('Mage_Rule_Block_Conditions'));

        if (Enterprise_CustomerSegment_Model_Segment::APPLY_TO_VISITORS_AND_REGISTERED == $model->getApplyTo()) {
            $fieldset->addField('conditions-label', 'label', array(
                'note' => Mage::helper('enterprise_customersegment')->__('* Could be applied both for visitors and registered customers'),
            ));
        }

        $form->setValues($model->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }
}