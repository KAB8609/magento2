<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * XmlConnect checkout controller
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_XmlConnect_Paypal_MepController extends Mage_XmlConnect_Controller_Action
{
    /**
     * Store MEP checkout model instance
     *
     * @var Mage_XmlConnect_Model_Paypal_Mep_Checkout
     */
    protected $_checkout = null;

    /**
     * Store Quote mdoel instance
     *
     * @var Mage_Sales_Model_Quote
     */
    protected $_quote = false;

    /**
     * Make sure customer is logged in
     */
    public function preDispatch()
    {
        parent::preDispatch();
        if (!Mage::getSingleton('customer/session')->isLoggedIn()) {
            $this->setFlag('', self::FLAG_NO_DISPATCH, true);
            $this->_message(Mage::helper('xmlconnect')->__('Customer not loggined.'), self::MESSAGE_STATUS_ERROR);
            return ;
        }
    }

    /**
     * Start MEP Checkout
     */
    public function indexAction()
    {
        try {
            $this->_initCheckout();
            $reservedOrderId = $this->_checkout->initCheckout();
            $this->_message(Mage::helper('xmlconnect')->__('Checkout was successfully initialized.'), self::MESSAGE_STATUS_SUCCESS);
            return;
        }
        catch (Mage_Core_Exception $e) {
            $this->_message($e->getMessage(), self::MESSAGE_STATUS_ERROR);
        }
        catch (Exception $e) {
            $this->_message(Mage::helper('xmlconnect')->__('Unable to start MEP Checkout.'), self::MESSAGE_STATUS_ERROR);
            Mage::logException($e);
        }
    }

    /**
     * Save shipping address to current quote using onepage model
     */
    public function saveShippingAddressAction()
    {
        if (!$this->getRequest()->isPost()) {
            $this->_message(Mage::helper('xmlconnect')->__('Specified invalid data.'), self::MESSAGE_STATUS_ERROR);
            return;
        }
        $this->_initCheckout();
        $data = $this->getRequest()->getPost('shipping', array());
        $result = $this->_checkout->saveShipping($data);
        if (!isset($result['error'])) {
            $this->_message(Mage::helper('xmlconnect')->__('Shipping address was successfully set.'), self::MESSAGE_STATUS_SUCCESS);
        }
        else {
            if (!is_array($result['message'])) {
                $result['message'] = array($result['message']);
            }
            $this->_message(implode('. ', $result['message']), self::MESSAGE_STATUS_ERROR);
        }
    }

    /**
     * Get shipping methods for current quote
     */
    public function shippingMethodsAction()
    {
        $this->_initCheckout();
        $this->loadLayout(false);
        $this->renderLayout();
    }

    /**
     * Shipping method save action
     */
    public function saveShippingMethodAction()
    {
        if (!$this->getRequest()->isPost()) {
            $this->_message(Mage::helper('xmlconnect')->__('Specified invalid data.'), self::MESSAGE_STATUS_ERROR);
            return;
        }
        $this->_initCheckout();
        $data = $this->getRequest()->getPost('shipping_method', '');
        $result = $this->_checkout->saveShippingMethod($data);
        if (!isset($result['error'])) {
            $message = new Mage_XmlConnect_Model_Simplexml_Element('<message></message>');
            $message->addChild('status', self::MESSAGE_STATUS_SUCCESS);
            $message->addChild('text', Mage::helper('xmlconnect')->__('Shipping method was successfully set.'));
            if ($this->_getQuote()->isVirtual()) {
                $quoteAddress = $this->_getQuote()->getBillingAddress();
            }
            else {
                $quoteAddress = $this->_getQuote()->getShippingAddress();
            }
            $taxAmount = Mage::helper('core')->currency($quoteAddress->getBaseTaxAmount(), false, false);
            $message->addChild('tax_amount', sprintf('%01.2F', $taxAmount));
            $this->getResponse()->setBody($message->asNiceXml());
        }
        else {
            if (!is_array($result['message'])) {
                $result['message'] = array($result['message']);
            }
            $this->_message(implode('. ', $result['message']), self::MESSAGE_STATUS_ERROR);
        }
    }

    /**
     * Shopping cart totals
     */
    public function cartTotalsAction()
    {
        try {
            $this->_initCheckout();
            $this->loadLayout(false);
            $this->renderLayout();
            return;
        }
        catch (Mage_Core_Exception $e) {
            $this->_message($e->getMessage(), self::MESSAGE_STATUS_ERROR);
        }
        catch (Exception $e) {
            $this->_message(Mage::helper('xmlconnect')->__('Unable to collect cart totals.'), self::MESSAGE_STATUS_ERROR);
            Mage::logException($e);
        }
    }

    /**
     * Submit the order
     */
    public function saveOrderAction()
    {
        if (!$this->getRequest()->isPost()) {
            $this->_message(Mage::helper('xmlconnect')->__('Specified invalid data.'), self::MESSAGE_STATUS_ERROR);
            return;
        }
        try {
            /**
             * Init checkout
             */
            $this->_initCheckout();

            /**
             * Set payment data
             */
            $data = $this->getRequest()->getPost('payment', array());
            $this->_checkout->savePayment($data);

            /**
             * Place order
             */
            $this->_checkout->saveOrder();

            /**
             * Format success report
             */
            $message = new Mage_XmlConnect_Model_Simplexml_Element('<message></message>');
            $message->addChild('status', self::MESSAGE_STATUS_SUCCESS);

            $orderId = $this->_checkout->getLastOrderId();

            $text = Mage::helper('xmlconnect')->__('Thank you for your purchase! ');
            $text .= Mage::helper('xmlconnect')->__('Your order # is: %s. ', $orderId);
            $text .= Mage::helper('xmlconnect')->__('You will receive an order confirmation email with details of your order and a link to track its progress.');
            $message->addChild('text', $text);

            $message->addChild('order_id', $orderId);
            $this->getResponse()->setBody($message->asNiceXml());
            return;
        }
        catch (Mage_Core_Exception $e) {
            $this->_message($e->getMessage(), self::MESSAGE_STATUS_ERROR);
        }
        catch (Exception $e) {
            $this->_message(Mage::helper('xmlconnect')->__('Unable to place the order.'), self::MESSAGE_STATUS_ERROR);
            Mage::logException($e);
        }
    }

    /**
     * Instantiate quote and checkout
     *
     * @throws Mage_Core_Exception
     */
    protected function _initCheckout()
    {

        $quote = $this->_getQuote();
        if (!$quote->hasItems() || $quote->getHasError()) {
            Mage::throwException(Mage::helper('xmlconnect')->__('Unable to initialize MEP Checkout.'));
        }
        if (!$quote->validateMinimumAmount()) {
            $error = Mage::getStoreConfig('sales/minimum_order/error_message');
            Mage::throwException($error);
        }
        $this->_getCheckoutSession()->setCartWasUpdated(false);

        $this->_checkout = Mage::getSingleton('xmlconnect/paypal_mep_checkout', array('quote'  => $quote));
    }

    /**
     * Return checkout session object
     *
     * @return Mage_Checkout_Model_Session
     */
    protected function _getCheckoutSession()
    {
        return Mage::getSingleton('checkout/session');
    }

    /**
     * Return checkout quote object
     *
     * @return Mage_Sale_Model_Quote
     */
    protected function _getQuote()
    {
        if (!$this->_quote) {
            $this->_quote = $this->_getCheckoutSession()->getQuote();
        }
        return $this->_quote;
    }
}
