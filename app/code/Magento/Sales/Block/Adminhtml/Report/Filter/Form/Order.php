<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Sales Adminhtml report filter form order
 *
 * @category   Magento
 * @package    Magento_Sales
 * @author     Magento Core Team <core@magentocommerce.com>
 */

class Magento_Sales_Block_Adminhtml_Report_Filter_Form_Order extends Magento_Sales_Block_Adminhtml_Report_Filter_Form
{
    protected function _prepareForm()
    {
        parent::_prepareForm();
        $form = $this->getForm();
        $htmlIdPrefix = $form->getHtmlIdPrefix();
        /** @var Magento_Data_Form_Element_Fieldset $fieldset */
        $fieldset = $this->getForm()->getElement('base_fieldset');

        if (is_object($fieldset) && $fieldset instanceof Magento_Data_Form_Element_Fieldset) {

            $fieldset->addField('show_actual_columns', 'select', array(
                'name'       => 'show_actual_columns',
                'options'    => array(
                    '1' => __('Yes'),
                    '0' => __('No')
                ),
                'label'      => __('Show Actual Values'),
            ));

        }

        return $this;
    }
}