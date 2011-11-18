<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_GiftCardAccount
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Enterprise_GiftCardAccount_Block_Checkout_Cart_Total extends Mage_Checkout_Block_Total_Default
{
    protected $_template = 'Enterprise_GiftCardAccount::cart/total.phtml';

    protected function _getQuote()
    {
        return Mage::getSingleton('Mage_Checkout_Model_Session')->getQuote();
    }

    public function getQuoteGiftCards()
    {
        return Mage::helper('Enterprise_GiftCardAccount_Helper_Data')->getCards($this->_getQuote());
    }
}
