<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_ImportExport
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Import edit form block
 *
 * @category    Mage
 * @package     Mage_ImportExport
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_ImportExport_Block_Adminhtml_Import_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * Add fieldset
     *
     * @return Mage_ImportExport_Block_Adminhtml_Import_Edit_Form
     */
    protected function _prepareForm()
    {
        $helper = Mage::helper('Mage_ImportExport_Helper_Data');
        $form = new Varien_Data_Form(array(
            'id'      => 'edit_form',
            'action'  => $this->getUrl('*/*/validate'),
            'method'  => 'post',
            'enctype' => 'multipart/form-data'
        ));

        // base fieldset
        $fieldsets['base'] = $form->addFieldset('base_fieldset', array('legend' => $helper->__('Import Settings')));
        $fieldsets['base']->addField('entity', 'select', array(
            'name'     => 'entity',
            'title'    => $helper->__('Entity Type'),
            'label'    => $helper->__('Entity Type'),
            'required' => true,
            'onchange' => 'editForm.handleEntityTypeSelector();',
            'values'   => Mage::getModel('Mage_ImportExport_Model_Source_Import_Entity')->toOptionArray()
        ));
        $fieldsets['base']->addField('behavior', 'select', array(
            'name'     => 'behavior',
            'title'    => $helper->__('Import Behavior'),
            'label'    => $helper->__('Import Behavior'),
            'required' => true,
            'values'   => Mage::getModel('Mage_ImportExport_Model_Source_Import_Behavior')->toOptionArray()
        ));
        $fieldsets['base']->addField(Mage_ImportExport_Model_Import::FIELD_NAME_SOURCE_FILE, 'file', array(
            'name'     => Mage_ImportExport_Model_Import::FIELD_NAME_SOURCE_FILE,
            'label'    => $helper->__('Select File to Import'),
            'title'    => $helper->__('Select File to Import'),
            'required' => true
        ));

        // fieldset for format version
        $fieldsets['version'] = $form->addFieldset('import_format_version_fieldset',
            array(
                'legend' => $helper->__('Import Format Version'),
                'style'  => 'display:none'
            )
        );
        $fieldsets['version']->addField('file_format_version', 'select', array(
            'name'     => 'file_format_version',
            'title'    => $helper->__('Import Format Version'),
            'label'    => $helper->__('Import Format Version'),
            'required' => false,
            'onchange' => 'editForm.handleImportFormatVersionSelector();',
            'values'   => Mage::getModel('Mage_ImportExport_Model_Source_Import_Format_Version')->toOptionArray()
        ));

        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
