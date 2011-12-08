<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_CustomerBalance
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Customer balance block for order creation page
 *
 */
class Enterprise_CustomerBalance_Block_Adminhtml_Sales_Order_Create_Payment
extends Mage_Core_Block_Template
{
    /**
     * @var Enterprise_CustomerBalance_Model_Balance
     */
    protected $_balanceInstance;

    /**
     * Retrieve order create model
     *
     * @return Mage_Adminhtml_Model_Sales_Order_Create
     */
    protected function _getOrderCreateModel()
    {
        return Mage::getSingleton('Mage_Adminhtml_Model_Sales_Order_Create');
    }

    /**
     * Format value as price
     *
     * @param numeric $value
     * @return string
     */
    public function formatPrice($value)
    {
        return Mage::getSingleton('Mage_Adminhtml_Model_Session_Quote')->getStore()->formatPrice($value);
    }

    /**
     * Balance getter
     *
     * @return float
     */
    public function getBalance()
    {
        if (!Mage::helper('Enterprise_CustomerBalance_Helper_Data')->isEnabled() || !$this->_getBalanceInstance()) {
            return 0.0;
        }
        return $this->_getBalanceInstance()->getAmount();
    }

    /**
     * Check whether quote uses customer balance
     *
     * @return bool
     */
    public function getUseCustomerBalance()
    {
        return $this->_getOrderCreateModel()->getQuote()->getUseCustomerBalance();
    }

    /**
     * Check whether customer balance fully covers quote
     *
     * @return bool
     */
    public function isFullyPaid()
    {
        if (!$this->_getBalanceInstance()) {
            return false;
        }
        return $this->_getBalanceInstance()->isFullAmountCovered($this->_getOrderCreateModel()->getQuote());
    }

    /**
     * Check whether quote uses customer balance
     *
     * @return bool
     */
    public function isUsed()
    {
        return $this->getUseCustomerBalance();
    }

    /**
     * Instantiate/load balance and return it
     *
     * @return Enterprise_CustomerBalance_Model_Balance|false
     */
    protected function _getBalanceInstance()
    {
        if (!$this->_balanceInstance) {
            $quote = $this->_getOrderCreateModel()->getQuote();
            if (!$quote || !$quote->getCustomerId() || !$quote->getStoreId()) {
                return false;
            }

            $store = Mage::app()->getStore($quote->getStoreId());
            $this->_balanceInstance = Mage::getModel('Enterprise_CustomerBalance_Model_Balance')
                ->setCustomerId($quote->getCustomerId())
                ->setWebsiteId($store->getWebsiteId())
                ->loadByCustomer();
        }
        return $this->_balanceInstance;
    }
}
