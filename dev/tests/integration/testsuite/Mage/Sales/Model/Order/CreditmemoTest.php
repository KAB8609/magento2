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

class Mage_Sales_Model_Order_CreditmemoTest extends PHPUnit_Framework_TestCase
{
    /**
     * @magentoConfigFixture frontend/design/theme/full_name default/demo
     * @magentoDataFixture Mage/Sales/_files/order.php
     */
    public function testSendEmail()
    {
        $order = Mage::getModel('Mage_Sales_Model_Order');
        $order->loadByIncrementId('100000001');
        $order->setCustomerEmail('customer@example.com');

        $creditmemo = Mage::getModel('Mage_Sales_Model_Order_Creditmemo');
        $creditmemo->setOrder($order);

        $payment = $order->getPayment();
        $paymentInfoBlock = Mage::helper('Mage_Payment_Helper_Data')->getInfoBlock($payment);
        $paymentInfoBlock->setArea('invalid-area');
        $payment->setBlockMock($paymentInfoBlock);

        $this->assertEmpty($creditmemo->getEmailSent());
        $creditmemo->sendEmail(true);
        $this->assertNotEmpty($creditmemo->getEmailSent());
        $this->assertEquals('frontend', $paymentInfoBlock->getArea());
    }
}
