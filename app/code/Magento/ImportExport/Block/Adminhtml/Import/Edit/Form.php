<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_ImportExport
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Import edit form block
 *
 * @category    Magento
 * @package     Magento_ImportExport
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_ImportExport_Block_Adminhtml_Import_Edit_Form extends Magento_Adminhtml_Block_Widget_Form
{
    /**
     * Add fieldsets
     *
     * @return Magento_ImportExport_Block_Adminhtml_Import_Edit_Form
     */
    protected function _prepareForm()
    {
        $form = new Magento_Data_Form(array(
            'id'      => 'edit_form',
            'action'  => $this->getUrl('*/*/validate'),
            'method'  => 'post',
            'enctype' => 'multipart/form-data',
        ));

        // base fieldset
        /** @var $importEntity Magento_ImportExport_Model_Source_Import_Entity */
        $importEntity = Mage::getModel('Magento_ImportExport_Model_Source_Import_Entity');
        $fieldsets['base'] = $form->addFieldset('base_fieldset', array('legend' => $this->__('Import Settings')));
        $fieldsets['base']->addField('entity', 'select', array(
            'name'     => 'entity',
            'title'    => $this->__('Entity Type'),
            'label'    => $this->__('Entity Type'),
            'required' => true,
            'onchange' => 'varienImport.handleEntityTypeSelector();',
            'values'   => $importEntity->toOptionArray(),
        ));

        // add behaviour fieldsets
        $uniqueBehaviors = Magento_ImportExport_Model_Import::getUniqueEntityBehaviors();
        foreach ($uniqueBehaviors as $behaviorCode => $behaviorClass) {
            $fieldsets[$behaviorCode] = $form->addFieldset(
                $behaviorCode . '_fieldset',
                array(
                    'legend' => $this->__('Import Behavior'),
                    'class'  => 'no-display',
                )
            );
            /** @var $behaviorSource Magento_ImportExport_Model_Source_Import_BehaviorAbstract */
            $behaviorSource = Mage::getModel($behaviorClass);
            $fieldsets[$behaviorCode]->addField($behaviorCode, 'select', array(
                'name'     => 'behavior',
                'title'    => $this->__('Import Behavior'),
                'label'    => $this->__('Import Behavior'),
                'required' => true,
                'disabled' => true,
                'values'   => $behaviorSource->toOptionArray(),
            ));
        }

        // fieldset for file uploading
        $fieldsets['upload'] = $form->addFieldset('upload_file_fieldset',
            array(
                'legend' => $this->__('File to Import'),
                'class'  => 'no-display',
            )
        );
        $fieldsets['upload']->addField(Magento_ImportExport_Model_Import::FIELD_NAME_SOURCE_FILE, 'file', array(
            'name'     => Magento_ImportExport_Model_Import::FIELD_NAME_SOURCE_FILE,
            'label'    => $this->__('Select File to Import'),
            'title'    => $this->__('Select File to Import'),
            'required' => true,
        ));

        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
