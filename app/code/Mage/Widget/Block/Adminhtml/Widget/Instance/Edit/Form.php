<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Widget
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Widget Instance edit form
 *
 * @category    Mage
 * @package     Mage_Widget
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Widget_Block_Adminhtml_Widget_Instance_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * Prepare form before rendering HTML
     *
     * @return Mage_Widget_Block_Adminhtml_Widget_Instance_Edit_Form
     */
    protected function _prepareForm()
    {
        $form = new Magento_Data_Form(array('id' => 'edit_form', 'action' => $this->getData('action'), 'method' => 'post'));
        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();
    }

}
