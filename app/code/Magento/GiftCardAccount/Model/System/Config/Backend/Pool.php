<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftCardAccount
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_GiftCardAccount_Model_System_Config_Backend_Pool extends Magento_Core_Model_Config_Value
{
    protected function _beforeSave()
    {
        if ($this->isValueChanged()) {
            if (!Mage::registry('giftcardaccount_code_length_check')) {
                Mage::register('giftcardaccount_code_length_check', 1);
                $this->_checkMaxLength();
            }
        }
        parent::_beforeSave();
    }

    protected function _afterSave()
    {
        if ($this->isValueChanged()) {
            Mage::getModel('Magento_GiftCardAccount_Model_Pool')->cleanupFree();
        }
        parent::_afterSave();
    }

    protected function _checkMaxLength()
    {
        $groups = $this->getGroups();
        if (isset($groups['general']['fields'])) {
            $fields = $groups['general']['fields'];
        }

        $len = 0;
        $codeLen = 0;
        if (isset($fields['code_length']['value'])) {
            $codeLen = (int) $fields['code_length']['value'];
            $len += $codeLen;
        }
        if (isset($fields['code_suffix']['value'])) {
            $len += strlen($fields['code_suffix']['value']);
        }
        if (isset($fields['code_prefix']['value'])) {
            $len += strlen($fields['code_prefix']['value']);
        }
        if (isset($fields['code_split']['value'])) {
            $v = (int) $fields['code_split']['value'];
            if ($v > 0 && $v < $codeLen) {
                $sep = Mage::getModel('Magento_GiftCardAccount_Model_Pool')->getCodeSeparator();
                $len += (ceil($codeLen/$v) * strlen($sep))-1;
            }
        }

        if ($len > 255) {
            Mage::throwException(__('Maximum generated code length is 255. Please correct your settings.'));
        }
    }
}