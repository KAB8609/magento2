<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Adminhtml_Sales_Order_CreateControllerTest extends Mage_Adminhtml_Utility_Controller
{
    public function testLoadBlockAction()
    {
        $this->getRequest()->setParam('block', ',');
        $this->getRequest()->setParam('json', 1);
        $this->dispatch('backend/admin/sales_order_create/loadBlock');
        $this->assertEquals('{"message":""}', $this->getResponse()->getBody());
    }

    /**
     * @magentoDataFixture Mage/Catalog/_files/product_simple.php
     */
    public function testLoadBlockActionData()
    {
        Mage::getSingleton('Mage_Adminhtml_Model_Sales_Order_Create')->addProducts(array(1 => array('qty' => 1)));
        $this->getRequest()->setParam('block', 'data');
        $this->getRequest()->setParam('json', 1);
        $this->dispatch('backend/admin/sales_order_create/loadBlock');
        $html = $this->getResponse()->getBody();
        $this->assertContains('<div id=\"sales_order_create_search_grid\">', $html);
        $this->assertContains('<div id=\"order-billing_method_form\">', $html);
        $this->assertContains('id=\"shipping-method-overlay\"', $html);
        $this->assertContains('id=\"coupons:code\"', $html);
    }

    /**
     * @dataProvider loadBlockActionsDataProvider
     */
    public function testLoadBlockActions($block, $expected)
    {
        $this->getRequest()->setParam('block', $block);
        $this->getRequest()->setParam('json', 1);
        $this->dispatch('backend/admin/sales_order_create/loadBlock');
        $html = $this->getResponse()->getBody();
        $this->assertContains($expected, $html);
    }

    public function loadBlockActionsDataProvider()
    {
        return array(
            'shipping_method' => array('shipping_method', 'id=\"shipping-method-overlay\"'),
            'billing_method' => array('billing_method', '<div id=\"order-billing_method_form\">'),
            'newsletter' => array('newsletter', 'name=\"newsletter:subscribe\"'),
            'search' => array('search', '<div id=\"sales_order_create_search_grid\">'),
            'search_grid' => array('search', '<div id=\"sales_order_create_search_grid\">'),
        );
    }

    /**
     * @magentoDataFixture Mage/Catalog/_files/product_simple.php
     */
    public function testLoadBlockActionItems()
    {
        Mage::getSingleton('Mage_Adminhtml_Model_Sales_Order_Create')->addProducts(array(1 => array('qty' => 1)));
        $this->getRequest()->setParam('block', 'items');
        $this->getRequest()->setParam('json', 1);
        $this->dispatch('backend/admin/sales_order_create/loadBlock');
        $html = $this->getResponse()->getBody();
        $this->assertContains('id=\"coupons:code\"', $html);
    }

    /**
     * @magentoDataFixture Mage/Catalog/_files/product_simple.php
     */
    public function testIndexAction()
    {
        /** @var $order Mage_Adminhtml_Model_Sales_Order_Create */
        $order = Mage::getSingleton('Mage_Adminhtml_Model_Sales_Order_Create');
        $order->addProducts(array(1 => array('qty' => 1)));
        $this->dispatch('backend/admin/sales_order_create/index');
        $html = $this->getResponse()->getBody();
        $this->assertContains('<div id="order-customer-selector"', $html);
        $this->assertContains('<div id="sales_order_create_customer_grid">', $html);
        $this->assertContains('<div id="order-billing_method_form">', $html);
        $this->assertContains('id="shipping-method-overlay"', $html);
        $this->assertContains('<div id="sales_order_create_search_grid">', $html);
        $this->assertContains('id="coupons:code"', $html);
    }
}
