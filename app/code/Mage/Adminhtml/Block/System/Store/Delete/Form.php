<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Adminhtml cms block edit form
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_System_Store_Delete_Form extends Mage_Adminhtml_Block_Widget_Form
{

    /**
     * Init form
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('store_delete_form');
        $this->setTitle(__('Block Information'));
    }

    protected function _prepareForm()
    {
        $dataObject = $this->getDataObject();

        $form = new Magento_Data_Form(array('id' => 'edit_form', 'action' => $this->getData('action'), 'method' => 'post'));

        $form->setHtmlIdPrefix('store_');

        $fieldset = $form->addFieldset('base_fieldset', array('legend' => __('Backup Options'), 'class' => 'fieldset-wide'));

        $fieldset->addField('item_id', 'hidden', array(
            'name'  => 'item_id',
            'value' => $dataObject->getId(),
        ));

        $fieldset->addField('create_backup', 'select', array(
            'label'     => __('Create DB Backup'),
            'title'     => __('Create DB Backup'),
            'name'      => 'create_backup',
            'options'   => array(
                '1' => __('Yes'),
                '0' => __('No'),
            ),
            'value'     => '1',
        ));

        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }

}
