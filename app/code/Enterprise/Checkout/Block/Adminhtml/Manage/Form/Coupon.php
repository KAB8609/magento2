<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Checkout
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Checkout coupon code form
 *
 * @category   Enterprise
 * @package    Enterprise_Checkout
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Checkout_Block_Adminhtml_Manage_Form_Coupon extends Mage_Adminhtml_Block_Template
{
    /**
     * Return applied coupon code for current quote
     *
     * @return string
     */
    public function getCouponCode()
    {
        return $this->_getQuote()->getCouponCode();
    }

    /**
     * Return current quote from regisrty
     *
     * @return Mage_Sales_Model_Quote
     */
    protected function _getQuote()
    {
        return Mage::registry('checkout_current_quote');
    }

    /**
     * Button html
     *
     * @return string
     */
    public function getApplyButtonHtml()
    {
        return $this->getLayout()
            ->createBlock('Mage_Adminhtml_Block_Widget_Button')
                ->setData(array(
                    'id'        => 'apply_coupon',
                    'label'     => Mage::helper('Enterprise_Checkout_Helper_Data')->__('Apply'),
                    'onclick'   => "checkoutObj.applyCoupon($('coupon_code').value)",
                ))
            ->toHtml();
    }

    /**
     * Apply admin acl
     */
    protected function _toHtml()
    {
        if (!Mage::getSingleton('Mage_Core_Model_Authorization')->isAllowed('Enterprise_Checkout::update')) {
            return '';
        }
        return parent::_toHtml();
    }
}