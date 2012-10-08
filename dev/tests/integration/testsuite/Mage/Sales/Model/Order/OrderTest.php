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

class Mage_Sales_Model_OrderTest extends PHPUnit_Framework_TestCase
{
    /**
     * @magentoConfigFixture current_store design/theme/full_name default/default/default
     * magentoDataFixture Mage/Sales/_files/order.php
     */
    public function testSendNewOrderEmail()
    {
        $this->markTestIncomplete('Need to fix DI dependencies + fixture');

        $order = new Mage_Sales_Model_Order();
        $order->loadByIncrementId('100000001');
        $order->setCustomerEmail('customer@example.com');

        $payment = $order->getPayment();
        $paymentInfoBlock = Mage::helper('Mage_Payment_Helper_Data')->getInfoBlock($payment);
        $paymentInfoBlock->setArea('invalid-area');
        $payment->setBlockMock($paymentInfoBlock);

        $this->assertEmpty($order->getEmailSent());
        $order->sendNewOrderEmail();
        $this->assertNotEmpty($order->getEmailSent());
        $this->assertEquals('frontend', $paymentInfoBlock->getArea());
    }
}
