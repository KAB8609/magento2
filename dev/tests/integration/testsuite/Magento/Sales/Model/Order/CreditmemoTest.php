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

class Magento_Sales_Model_Order_CreditmemoTest extends PHPUnit_Framework_TestCase
{
    /**
     * @magentoDataFixture Magento/Sales/_files/order.php
     */
    public function testSendEmail()
    {
        Mage::app()->getArea(Magento_Core_Model_App_Area::AREA_FRONTEND)->load();
        $order = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Sales_Model_Order');
        $order->loadByIncrementId('100000001');
        $order->setCustomerEmail('customer@example.com');

        $creditmemo = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Sales_Model_Order_Creditmemo');
        $creditmemo->setOrder($order);

        $payment = $order->getPayment();
        $paymentInfoBlock = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->get('Magento_Payment_Helper_Data')
            ->getInfoBlock($payment);
        $paymentInfoBlock->setArea('invalid-area');
        $payment->setBlockMock($paymentInfoBlock);

        $this->assertEmpty($creditmemo->getEmailSent());
        $creditmemo->sendEmail(true);
        $this->assertNotEmpty($creditmemo->getEmailSent());
        $this->assertEquals('frontend', $paymentInfoBlock->getArea());
    }
}
