<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Centinel
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Centinel_CreateOrderTest extends Mage_Adminhtml_Utility_Controller
{
    /**
     * @magentoConfigFixture admin_store payment/ccsave/centinel 1
     * @magentoDataFixture Mage/Catalog/_files/product_simple.php
     */
    public function testIndexAction()
    {
        /** @var $order Mage_Adminhtml_Model_Sales_Order_Create */
        $order = Mage::getSingleton('Mage_Adminhtml_Model_Sales_Order_Create');
        $paymentData = array(
            'cc_owner' => 'Test User',
            'cc_type' => 'visa',
            'cc_number' => '4111111111111111',
            'cc_exp_month' => '12',
            'cc_exp_year' => '2013',
            'cc_cid' => '123',
            'method' => 'ccsave',
        );
        $order->addProducts(array(1 => array('qty' => 1)))->getQuote()->getPayment()->addData($paymentData);
        $this->dispatch('admin/sales_order_create/index');
        $this->assertContains('<div class="centinel">', $this->getResponse()->getBody());
    }
}