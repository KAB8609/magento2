<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Payment
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Free payment method
 *
 * @category   Mage
 * @package    Mage_Payment
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Payment_Model_Method_Free extends Mage_Payment_Model_Method_Abstract
{
    /**
     * XML Pathes for configuration constants
     */
    const XML_PATH_PAYMENT_FREE_ACTIVE = 'payment/free/active';
    const XML_PATH_PAYMENT_FREE_ORDER_STATUS = 'payment/free/order_status';
    const XML_PATH_PAYMENT_FREE_PAYMENT_ACTION = 'payment/free/payment_action';

    /**
     * Payment Method features
     * @var bool
     */
    protected $_canAuthorize                = true;

    /**
     * Payment code name
     *
     * @var string
     */
    protected $_code = 'free';

    /**
     * Check whether method is available
     *
     * @param Mage_Sales_Model_Quote $quote
     * @return bool
     */
    public function isAvailable($quote = null)
    {
        return parent::isAvailable($quote) && (!empty($quote))
            && (Mage::app()->getStore()->roundPrice($quote->getGrandTotal()) == 0);
    }

    /**
     * Get config peyment action
     *
     * @return string
     */
    public function getConfigPaymentAction()
    {
        if ('pending' == $this->getConfigData('order_status')) {
            return null; // do nothing if status pending
        }
        return parent::getConfigPaymentAction();
    }
}
