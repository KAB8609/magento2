<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Paypal
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @group module:Mage_Paypal
 * @magentoDataFixture Mage/Sales/_files/order.php
 */
class Mage_Paypal_StandardControllerTest extends Magento_Test_TestCase_ControllerAbstract
{
    public function testRedirectActionIsContentGenerated()
    {
        $order = new Mage_Sales_Model_Order();
        $order->load('100000001', 'increment_id');
        $order->getPayment()->setMethod(Mage_Paypal_Model_Config::METHOD_WPS);
        $order->save();

        $session = Mage::getSingleton('Mage_Checkout_Model_Session');
        $session->setLastRealOrderId($order->getRealOrderId())
            ->setLastQuoteId($order->getQuoteId());

        $this->dispatch('paypal/standard/redirect');
        $this->assertContains(
            '<form action="https://www.paypal.com/webscr" id="paypal_standard_checkout"'
                . ' name="paypal_standard_checkout" method="POST">',
            $this->getResponse()->getBody()
        );
    }
}