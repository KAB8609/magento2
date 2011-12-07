<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_GiftRegistry
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Gift registry type edit form block
 */
class Enterprise_GiftRegistry_Block_Adminhtml_Giftregistry_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * Intialize form
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('enterprise_giftregistry_form');
        $this->setTitle(Mage::helper('Enterprise_GiftRegistry_Helper_Data')->__('Gift Registry'));
    }

    /**
     * Prepare edit form
     *
     * @return Enterprise_GiftRegistry_Block_Adminhtml_Giftregistry_Edit_Form
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form(array('id' => 'edit_form', 'action' => $this->getData('action'), 'method' => 'post'));
        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();
    }
}
