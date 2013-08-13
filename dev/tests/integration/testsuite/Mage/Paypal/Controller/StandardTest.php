<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Paypal_Controller_StandardTest extends Magento_Test_TestCase_ControllerAbstract
{
    /**
     * @var Magento_Checkout_Model_Session
     */
    protected $_session;

    /**
     * @var Mage_Sales_Model_Order
     */
    protected $_order;

    public function setUp()
    {
        parent::setUp();
        $this->_order = $this->_objectManager->create('Mage_Sales_Model_Order');
        $this->_session = $this->_objectManager->get('Magento_Checkout_Model_Session');
    }

    /**
     * @magentoDataFixture Mage/Sales/_files/order.php
     */
    public function testRedirectActionIsContentGenerated()
    {
        $this->_order->load('100000001', 'increment_id');
        $this->_order->getPayment()->setMethod(Mage_Paypal_Model_Config::METHOD_WPS);
        $this->_order->save();
        $this->_order->load('100000001', 'increment_id');

        $this->_session->setLastRealOrderId($this->_order->getRealOrderId())
            ->setLastQuoteId($this->_order->getQuoteId());

        $this->dispatch('paypal/standard/redirect');
        $this->assertContains(
            '<form action="https://www.paypal.com/cgi-bin/webscr" id="paypal_standard_checkout"'
                . ' name="paypal_standard_checkout" method="POST">',
            $this->getResponse()->getBody()
        );
    }

    /**
     * @magentoDataFixture Mage/Paypal/_files/quote_payment_standard.php
     * @magentoConfigFixture current_store payment/paypal_standard/active 1
     * @magentoConfigFixture current_store paypal/general/business_account merchant_2012050718_biz@example.com
     */
    public function testCancelAction()
    {
        $quote = $this->_objectManager->create('Mage_Sales_Model_Quote');
        $quote->load('test01', 'reserved_order_id');
        $this->_session->setQuoteId($quote->getId());
        $this->_session->setPaypalStandardQuoteId($quote->getId())
            ->setLastRealOrderId('100000002');
        $this->dispatch('paypal/standard/cancel');

        $this->_order->load('100000002', 'increment_id');
        $this->assertEquals('canceled', $this->_order->getState());
        $this->assertEquals($this->_session->getQuote()->getGrandTotal(), $quote->getGrandTotal());
        $this->assertEquals($this->_session->getQuote()->getItemsCount(), $quote->getItemsCount());
    }
}
