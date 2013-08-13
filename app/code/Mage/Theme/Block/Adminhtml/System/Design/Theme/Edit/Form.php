<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Theme
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Theme Edit Form
 */
class Mage_Theme_Block_Adminhtml_System_Design_Theme_Edit_Form extends Magento_Backend_Block_Widget_Form
{
    /**
     * Initialize theme form
     *
     * @return Mage_Theme_Block_Adminhtml_System_Design_Theme_Edit_Form|Magento_Backend_Block_Widget_Form
     */
    protected function _prepareForm()
    {
        $form = new Magento_Data_Form(array(
              'id'      => 'edit_form',
              'action'  => $this->getUrl('*/*/save'),
              'enctype' => 'multipart/form-data',
              'method'  => 'post'
         ));

        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();
    }
}
