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
 * @magentoDataFixture Mage/Sales/_files/order.php
 */
class Mage_Paypal_HostedproControllerTest extends Magento_Test_TestCase_ControllerAbstract
{
    public function testCancelActionIsContentGenerated()
    {
        $this->markTestIncomplete('MAGETWO-1587');
        $order = new Mage_Sales_Model_Order();
        $order->load('100000001', 'increment_id');
        $order->getPayment()->setMethod(Mage_Paypal_Model_Config::METHOD_HOSTEDPRO);
        $order->save();

        $session = Mage::getSingleton('Mage_Checkout_Model_Session');
        $session->setLastRealOrderId($order->getRealOrderId())
            ->setLastQuoteId($order->getQuoteId());

        $this->dispatch('paypal/hostedpro/cancel');
        $this->assertContains(
            'window.top.checkout.gotoSection("payment");',
            $this->getResponse()->getBody()
        );
        $this->assertContains(
            'window.top.document.getElementById(\'checkout-review-submit\').show();',
            $this->getResponse()->getBody()
        );
        $this->assertContains(
            'window.top.document.getElementById(\'iframe-warning\').hide();',
            $this->getResponse()->getBody()
        );
    }
}
