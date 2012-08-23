<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Reminder
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Reminder rules edit form general fields
 */
class Enterprise_Reminder_Block_Adminhtml_Reminder_Edit_Tab_General
    extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * Prepare general properties form
     *
     * @return Enterprise_Reminder_Block_Adminhtml_Reminder_Edit_Tab_General
     */
    protected function _prepareForm()
    {
        $isEditable = ($this->getCanEditReminderRule() !== false) ? true : false;
        $form = new Varien_Data_Form();
        $model = Mage::registry('current_reminder_rule');

        $fieldset = $form->addFieldset('base_fieldset', array(
            'legend'  => Mage::helper('Enterprise_Reminder_Helper_Data')->__('General Information'),
            'comment' => Mage::helper('Enterprise_Reminder_Helper_Data')->__('Reminder emails may promote a shopping cart price rule with or without coupon. If a shopping cart price rule defines an auto-generated coupon, this reminder rule will generate a random coupon code for each customer.'),
        ));

        if ($model->getId()) {
            $fieldset->addField('rule_id', 'hidden', array(
                'name' => 'rule_id',
            ));
        }

        $fieldset->addField('name', 'text', array(
            'name'     => 'name',
            'label'    => Mage::helper('Enterprise_Reminder_Helper_Data')->__('Rule Name'),
            'required' => true,
        ));

        $fieldset->addField('description', 'textarea', array(
            'name'  => 'description',
            'label' => Mage::helper('Enterprise_Reminder_Helper_Data')->__('Description'),
            'style' => 'height: 100px;',
        ));

        $field = $fieldset->addField('salesrule_id', 'note', array(
            'name'  => 'salesrule_id',
            'label' => Mage::helper('Enterprise_Reminder_Helper_Data')->__('Shopping Cart Price Rule'),
            'class' => 'widget-option',
            'value' => $model->getSalesruleId(),
            'note'  => Mage::helper('Enterprise_Reminder_Helper_Data')->__('Promotion rule this reminder will advertise.'),
            'readonly' => !$isEditable
        ));

        $model->unsSalesruleId();
        $helperBlock = $this->getLayout()->createBlock('Mage_Adminhtml_Block_Promo_Widget_Chooser');

        if ($helperBlock instanceof Varien_Object) {
            $helperBlock->setConfig($this->getChooserConfig())
                ->setFieldsetId($fieldset->getId())
                ->setTranslationHelper(Mage::helper('Mage_SalesRule_Helper_Data'))
                ->prepareElementHtml($field);
        }

        if (Mage::app()->isSingleStoreMode()) {
            $websiteId = Mage::app()->getStore(true)->getWebsiteId();
            $fieldset->addField('website_ids', 'hidden', array(
                'name'     => 'website_ids[]',
                'value'    => $websiteId
            ));
            $model->setWebsiteIds($websiteId);
        } else {
            $fieldset->addField('website_ids', 'multiselect', array(
                'name'     => 'website_ids[]',
                'label'    => Mage::helper('Enterprise_Reminder_Helper_Data')->__('Assigned to Website'),
                'title'    => Mage::helper('Enterprise_Reminder_Helper_Data')->__('Assigned to Website'),
                'required' => true,
                'values'   => Mage::getSingleton('Mage_Core_Model_System_Store')->getWebsiteValuesForForm(),
                'value'    => $model->getWebsiteIds()
            ));
        }

        $fieldset->addField('is_active', 'select', array(
            'label'    => Mage::helper('Enterprise_Reminder_Helper_Data')->__('Status'),
            'name'     => 'is_active',
            'required' => true,
            'options'  => array(
                '1' => Mage::helper('Enterprise_Reminder_Helper_Data')->__('Active'),
                '0' => Mage::helper('Enterprise_Reminder_Helper_Data')->__('Inactive'),
            ),
        ));

        if (!$model->getId()) {
            $model->setData('is_active', '1');
        }

        $dateFormat = Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);

        $fieldset->addField('from_date', 'date', array(
            'name'   => 'from_date',
            'label'  => Mage::helper('Enterprise_Reminder_Helper_Data')->__('From Date'),
            'title'  => Mage::helper('Enterprise_Reminder_Helper_Data')->__('From Date'),
            'image'  => $this->getSkinUrl('images/grid-cal.gif'),
            'input_format' => Varien_Date::DATE_INTERNAL_FORMAT,
            'date_format'  => $dateFormat
        ));
        $fieldset->addField('to_date', 'date', array(
            'name'   => 'to_date',
            'label'  => Mage::helper('Enterprise_Reminder_Helper_Data')->__('To Date'),
            'title'  => Mage::helper('Enterprise_Reminder_Helper_Data')->__('To Date'),
            'image'  => $this->getSkinUrl('images/grid-cal.gif'),
            'input_format' => Varien_Date::DATE_INTERNAL_FORMAT,
            'date_format' => $dateFormat
        ));

        $fieldset->addField('schedule', 'text', array(
            'name' => 'schedule',
            'label' => Mage::helper('Enterprise_Reminder_Helper_Data')->__('Repeat Schedule'),
            'note' => Mage::helper('Enterprise_Reminder_Helper_Data')->__('In what number of days to repeat reminder email, if the rule condition still matches. Enter days, comma-separated.'),
        ));

        $form->setValues($model->getData());
        $this->setForm($form);

        if (!$isEditable) {
            $this->getForm()->setReadonly(true, true);
        }

        return parent::_prepareForm();
    }

    /**
     * Get chooser config data
     *
     * @return array
     */
    public function getChooserConfig()
    {
        return array(
            'button' => array('open'=>'Select Rule...')
        );
    }
}
