<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Paypal
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Paypal Express Onepage checkout block for Shipping Address
 *
 * @category   Mage
 * @package    Mage_Paypal
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Paypal_Block_Express_Review_Shipping extends Mage_Checkout_Block_Onepage_Shipping
{
    /**
     * Return Sales Quote Address model (shipping address)
     *
     * @return Mage_Sales_Model_Quote_Address
     */
    public function getAddress()
    {
        if (is_null($this->_address)) {
            if ($this->isCustomerLoggedIn() || $this->getQuote()->getShippingAddress()) {
                $this->_address = $this->getQuote()->getShippingAddress();
            } else {
                $this->_address = Mage::getModel('Mage_Sales_Model_Quote_Address');
            }
        }

        return $this->_address;
    }
}
