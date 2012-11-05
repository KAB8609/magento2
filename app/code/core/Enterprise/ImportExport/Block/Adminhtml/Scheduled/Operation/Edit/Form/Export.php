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
 * Scheduled export create/edit form
 *
 * @category    Enterprise
 * @package     Enterprise_ImportExport
 * @author      Magento Core Team <core@magentocommerce.com>
 *
 * @method Enterprise_ImportExport_Block_Adminhtml_Scheduled_Operation_Edit_Form_Export setGeneralSettingsLabel() setGeneralSettingsLabel(string $value)
 * @method Enterprise_ImportExport_Block_Adminhtml_Scheduled_Operation_Edit_Form_Export setFileSettingsLabel() setFileSettingsLabel(string $value)
 * @method Enterprise_ImportExport_Block_Adminhtml_Scheduled_Operation_Edit_Form_Export setEmailSettingsLabel() setEmailSettingsLabel(string $value)
 */
// @codingStandardsIgnoreEnd
class Enterprise_ImportExport_Block_Adminhtml_Scheduled_Operation_Edit_Form_Export
    extends Enterprise_ImportExport_Block_Adminhtml_Scheduled_Operation_Edit_Form
{
    /**
     * Prepare form for export operation
     *
     * @return Enterprise_ImportExport_Block_Adminhtml_Scheduled_Operation_Edit_Form_Export
     */
    protected function _prepareForm()
    {
        /** @var $helper Enterprise_ImportExport_Helper_Data */
        $helper = Mage::helper('Enterprise_ImportExport_Helper_Data');

        $this->setGeneralSettingsLabel($helper->__('Export Settings'));
        $this->setFileSettingsLabel($helper->__('Export File Information'));
        $this->setEmailSettingsLabel($helper->__('Export Failed Emails'));

        parent::_prepareForm();
        $form = $this->getForm();
        /** @var $operation Enterprise_ImportExport_Model_Scheduled_Operation */
        $operation = Mage::registry('current_operation');

        /** @var $fileFormatModel Mage_ImportExport_Model_Source_Export_Format */
        $fileFormatModel = Mage::getModel('Mage_ImportExport_Model_Source_Export_Format');

        $fieldset = $form->getElement('operation_settings');
        $fieldset->addField('file_format', 'select', array(
            'name'      => 'file_info[file_format]',
            'title'     => $helper->__('File Format'),
            'label'     => $helper->__('File Format'),
            'required'  => true,
            'values'    => $fileFormatModel->toOptionArray()
        ));

        $form->getElement('email_template')
            ->setValues(Mage::getModel('Mage_Backend_Model_Config_Source_Email_Template')
                ->setPath('enterprise_importexport_export_failed')
                ->toOptionArray()
            );

        /** @var $element Varien_Data_Form_Element_Abstract */
        $element = $form->getElement('entity');
        $element->setData('onchange', 'editForm.getFilter();');

        $fieldset = $form->addFieldset('export_filter_grid_container', array(
            'legend' => $helper->__('Entity Attributes'),
            'fieldset_container_id' => 'export_filter_container'
        ));

        // prepare filter grid data
        if ($operation->getId()) {
            // $operation object is stored in registry and used in other places.
            // that's why we will not change its data to ensure that existing logic will not be affected.
            // instead we will clone existing operation object.
            $filterOperation = clone $operation;
            if ($filterOperation->getEntityType() == 'customer_address'
                || $filterOperation->getEntityType() == 'customer_finance'
            ) {
                $filterOperation->setEntityType('customer');
            }
            $fieldset->setData('html_content', $this->_getFilterBlock($filterOperation)->toHtml());
        }

        $this->_setFormValues($operation->getData());

        return $this;
    }

    /**
     * Return block instance with specific attribute fields
     *
     * @param Enterprise_ImportExport_Model_Scheduled_Operation $operation
     * @return Enterprise_ImportExport_Block_Adminhtml_Export_Filter
     */
    protected function _getFilterBlock($operation)
    {
        $exportOperation = $operation->getInstance();
        /** @var $block Enterprise_ImportExport_Block_Adminhtml_Export_Filter */
        $block = $this->getLayout()
            ->createBlock('Enterprise_ImportExport_Block_Adminhtml_Export_Filter')
            ->setOperation($exportOperation);

        $exportOperation->filterAttributeCollection(
            $block->prepareCollection($exportOperation->getEntityAttributeCollection())
        );
        return $block;
    }
}
