<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Sales_Model_Order_ShipmentTest extends PHPUnit_Framework_TestCase
{
    /**
     * @magentoConfigFixture current_store design/theme/full_name default/default/default
     * @magentoDataFixture Mage/Sales/_files/order.php
     */
    public function testSendEmail()
    {
        $order = new Mage_Sales_Model_Order();
        $order->loadByIncrementId('100000001');
        $order->setCustomerEmail('customer@example.com');

        $shipment = new Mage_Sales_Model_Order_Shipment();
        $shipment->setOrder($order);

        $payment = $order->getPayment();
        $paymentInfoBlock = Mage::helper('Mage_Payment_Helper_Data')->getInfoBlock($payment);
        $paymentInfoBlock->setArea('invalid-area');
        $payment->setBlockMock($paymentInfoBlock);

        $this->assertEmpty($shipment->getEmailSent());
        $shipment->sendEmail(true);
        $this->assertNotEmpty($shipment->getEmailSent());
        $this->assertEquals('frontend', $paymentInfoBlock->getArea());
    }
}
