<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Centinel
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml sales order create validation card block
 */
class Magento_Centinel_Block_Adminhtml_Validation extends Magento_Adminhtml_Block_Sales_Order_Create_Abstract
{
    /**
     * construct
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('sales_order_create_validation_card');
    }

    /**
     * Return text for block`s header
     *
     * @return string
     */
    public function getHeaderText()
    {
        return __('3D Secure Card Validation');
    }

    /**
     * Return css class name for header block
     *
     * @return string
     */
    public function getHeaderCssClass()
    {
        return 'head-payment-method';
    }

    /**
     * Prepare html output
     *
     * @return string
     */
    protected function _toHtml()
    {
        $payment = $this->getQuote()->getPayment();
        if (!$payment->getMethod()
            || !$payment->getMethodInstance()
            || $payment->getMethodInstance()->getIsDummy()
            || !$payment->getMethodInstance()->getIsCentinelValidationEnabled())
        {
            return '';
        }
        return parent::_toHtml();
    }
}
