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
 * Export edit form block
 *
 * @category    Mage
 * @package     Mage_ImportExport
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_ImportExport_Block_Adminhtml_Export_Edit_Form extends Mage_Backend_Block_Widget_Form
{
    /**
     * Prepare form before rendering HTML.
     *
     * @return Mage_ImportExport_Block_Adminhtml_Export_Edit_Form
     */
    protected function _prepareForm()
    {
        $helper = Mage::helper('Mage_ImportExport_Helper_Data');
        $form = new Magento_Data_Form(array(
            'id'     => 'edit_form',
            'action' => $this->getUrl('*/*/getFilter'),
            'method' => 'post'
        ));

        $fieldset = $form->addFieldset('base_fieldset', array('legend' => $helper->__('Export Settings')));
        /** @var $entitySourceModel Mage_ImportExport_Model_Source_Export_Entity */
        $entitySourceModel = Mage::getModel('Mage_ImportExport_Model_Source_Export_Entity');
        $fieldset->addField('entity', 'select', array(
            'name'     => 'entity',
            'title'    => $helper->__('Entity Type'),
            'label'    => $helper->__('Entity Type'),
            'required' => false,
            'onchange' => 'varienExport.getFilter();',
            'values'   => $entitySourceModel->toOptionArray()
        ));
        /** @var $formatSourceModel Mage_ImportExport_Model_Source_Export_Format */
        $formatSourceModel = Mage::getModel('Mage_ImportExport_Model_Source_Export_Format');
        $fieldset->addField('file_format', 'select', array(
            'name'     => 'file_format',
            'title'    => $helper->__('Export File Format'),
            'label'    => $helper->__('Export File Format'),
            'required' => false,
            'values'   => $formatSourceModel->toOptionArray()
        ));

        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
