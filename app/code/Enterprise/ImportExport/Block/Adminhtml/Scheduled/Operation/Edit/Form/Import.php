<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_ImportExport
 * @copyright   {copyright}
 * @license     {license_link}
 */

// @codingStandardsIgnoreStart
/**
 * Scheduled import create/edit form
 *
 * @category    Enterprise
 * @package     Enterprise_ImportExport
 * @author      Magento Core Team <core@magentocommerce.com>
 *
 * @method Enterprise_ImportExport_Block_Adminhtml_Scheduled_Operation_Edit_Form_Import setGeneralSettingsLabel() setGeneralSettingsLabel(string $value)
 * @method Enterprise_ImportExport_Block_Adminhtml_Scheduled_Operation_Edit_Form_Import setFileSettingsLabel() setFileSettingsLabel(string $value)
 * @method Enterprise_ImportExport_Block_Adminhtml_Scheduled_Operation_Edit_Form_Import setEmailSettingsLabel() setEmailSettingsLabel(string $value)
 */
// @codingStandardsIgnoreEnd
class Enterprise_ImportExport_Block_Adminhtml_Scheduled_Operation_Edit_Form_Import
    extends Enterprise_ImportExport_Block_Adminhtml_Scheduled_Operation_Edit_Form
{
    /**
     * Prepare form for import operation
     *
     * @return Enterprise_ImportExport_Block_Adminhtml_Scheduled_Operation_Edit_Form_Import
     */
    protected function _prepareForm()
    {
        $this->setGeneralSettingsLabel(__('Import Settings'));
        $this->setFileSettingsLabel(__('Import File Information'));
        $this->setEmailSettingsLabel(__('Import Failed Emails'));

        parent::_prepareForm();
        $form = $this->getForm();

        /** @var $fieldset Varien_Data_Form_Element_Abstract */
        $fieldset = $form->getElement('operation_settings');

        // add behaviour fields
        $uniqueBehaviors = Mage_ImportExport_Model_Import::getUniqueEntityBehaviors();
        foreach ($uniqueBehaviors as $behaviorCode => $behaviorClass) {
            /** @var $behaviorSource Mage_ImportExport_Model_Source_Import_BehaviorAbstract */
            $behaviorSource = Mage::getModel($behaviorClass);
            $fieldset->addField($behaviorCode, 'select', array(
                'name'     => 'behavior',
                'title'    => __('Import Behavior'),
                'label'    => __('Import Behavior'),
                'required' => true,
                'disabled' => true,
                'values'   => $behaviorSource->toOptionArray()
            ), 'entity');
        }

        /** @var $operationData Enterprise_ImportExport_Model_Scheduled_Operation_Data */
        $operationData = Mage::getSingleton('Enterprise_ImportExport_Model_Scheduled_Operation_Data');
        $fieldset->addField('force_import', 'select', array(
            'name'     => 'force_import',
            'title'    => __('On Error'),
            'label'    => __('On Error'),
            'required' => true,
            'values'   => $operationData->getForcedImportOptionArray()
        ), 'freq');

        $form->getElement('email_template')
            ->setValues(Mage::getModel('Mage_Backend_Model_Config_Source_Email_Template')
                ->setPath('enterprise_importexport_import_failed')
                ->toOptionArray()
            );

        $fieldset = $form->getElement('file_settings');
        $fieldset->addField('file_name', 'text', array(
            'name'     => 'file_info[file_name]',
            'title'    => __('File Name'),
            'label'    => __('File Name'),
            'required' => true
        ), 'file_path');

        /** @var $element Varien_Data_Form_Element_Abstract */
        $element = $form->getElement('entity');
        $element->setData('onchange', 'varienImportExportScheduled.handleEntityTypeSelector();');

        /** @var $operation Enterprise_ImportExport_Model_Scheduled_Operation */
        $operation = Mage::registry('current_operation');
        $this->_setFormValues($operation->getData());

        return $this;
    }
}
