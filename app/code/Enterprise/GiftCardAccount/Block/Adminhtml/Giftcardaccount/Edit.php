<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_GiftCardAccount
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Enterprise_GiftCardAccount_Block_Adminhtml_Giftcardaccount_Edit extends Magento_Adminhtml_Block_Widget_Form_Container
{

    protected function _construct()
    {
        $this->_objectId = 'id';
        $this->_controller = 'adminhtml_giftcardaccount';
        $this->_blockGroup = 'Enterprise_GiftCardAccount';

        parent::_construct();

        $clickSave = "\$('_sendaction').value = 0;";
        $clickSave .= "\$('_sendrecipient_email').removeClassName('required-entry');";
        $clickSave .= "\$('_sendrecipient_name').removeClassName('required-entry');";

        $this->_updateButton('save', 'label', Mage::helper('Enterprise_GiftCardAccount_Helper_Data')->__('Save'));
        $this->_updateButton('save', 'onclick', $clickSave);
        $this->_updateButton('save', 'data_attribute', array(
            'mage-init' => array(
                'button' => array('event' => 'save', 'target' => '#edit_form'),
            ),
        ));
        $this->_updateButton('delete', 'label', Mage::helper('Enterprise_GiftCardAccount_Helper_Data')->__('Delete'));

        $clickSend = "\$('_sendrecipient_email').addClassName('required-entry');";
        $clickSend .= "\$('_sendrecipient_name').addClassName('required-entry');";
        $clickSend .= "\$('_sendaction').value = 1;";

        $this->_addButton('send', array(
            'label'     => Mage::helper('Enterprise_GiftCardAccount_Helper_Data')->__('Save & Send Email'),
            'onclick'   => $clickSend,
            'class'     => 'save',
            'data_attribute' => array(
                'mage-init' => array(
                    'button' => array('event' => 'save', 'target' => '#edit_form'),
                ),
            )
        ));
    }

    public function getGiftcardaccountId()
    {
        return Mage::registry('current_giftcardaccount')->getId();
    }

    public function getHeaderText()
    {
        if (Mage::registry('current_giftcardaccount')->getId()) {
            return Mage::helper('Enterprise_GiftCardAccount_Helper_Data')->__('Edit Gift Card Account: %s', $this->escapeHtml(Mage::registry('current_giftcardaccount')->getCode()));
        }
        else {
            return Mage::helper('Enterprise_GiftCardAccount_Helper_Data')->__('New Gift Card Account');
        }
    }

}
