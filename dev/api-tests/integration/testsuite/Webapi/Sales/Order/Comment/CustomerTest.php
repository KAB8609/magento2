<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Test
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test for sales order comments Webapi by customer api user
 *
 * @category    Magento
 * @package     Magento_Test
 * @author      Magento Api Team <api-team@magento.com>
 */
class Webapi_Sales_Order_Comment_CustomerTest extends Magento_Test_Webservice_Rest_Customer
{
    /**
     * Count of a history items
     */
    const HISTORY_COUNT = 3;

    /**
     * Delete fixtures
     */
    protected function tearDown()
    {
        Magento_Test_Webservice::deleteFixture('customer_order', true);
        Magento_Test_Webservice::deleteFixture('customer_quote', true);
        $fixtureProducts = $this->getFixture('customer_products');
        if ($fixtureProducts && count($fixtureProducts)) {
            foreach ($fixtureProducts as $fixtureProduct) {
                $this->callModelDelete($fixtureProduct, true);
            }
        }

        Magento_Test_Webservice::deleteFixture('order', true);
        Magento_Test_Webservice::deleteFixture('quote', true);
        $fixtureProducts = $this->getFixture('products');
        if ($fixtureProducts && count($fixtureProducts)) {
            foreach ($fixtureProducts as $fixtureProduct) {
                $this->callModelDelete($fixtureProduct, true);
            }
        }

        parent::tearDown();
    }

    /**
     * Generate sales order comments
     *
     * @param Mage_Sales_Model_Order $order
     * @param int $isVisibleOnFront
     * @return Webapi_Sales_Order_Comment_CustomerTest
     */
    protected function _generateCollection(Mage_Sales_Model_Order $order, $isVisibleOnFront = 1)
    {
        $counter = 0;
        while ($counter++ < self::HISTORY_COUNT) {
            /** @var $history Mage_Sales_Model_Order_Status_History */
            $history = require dirname(__FILE__) . '/../../../../../fixture/_block/Sales/Order/History.php';
            $history->setIsVisibleOnFront($isVisibleOnFront)
                ->setEntityName(Mage_Sales_Model_Order::HISTORY_ENTITY_NAME);
            $order->addStatusHistory($history);
        }
        $order->setDataChanges(true);
        return $this;
    }

    /**
     * Test create sales order comment
     *
     * @resourceOperation order_comment::create
     */
    public function testCreate()
    {
        $response = $this->callPost('orders/1/comments', array('qwerty'));
        $this->assertEquals(Mage_Webapi_Exception::HTTP_FORBIDDEN, $response->getStatus());
    }

    /**
     * Test retrieve sales order comments collection
     *
     * @magentoDataFixture fixture/Sales/Order/customer_order.php
     * @resourceOperation order_comment::multiget
     */
    public function testRetrieve()
    {
        /** @var $order Mage_Sales_Model_Order */
        $order = $this->getFixture('customer_order');

        $this->_generateCollection($order);
        $order->save();

        $response = $this->callGet("orders/{$order->getId()}/comments");

        $this->assertEquals(Mage_Webapi_Controller_Handler_Rest::HTTP_OK, $response->getStatus());
        $this->assertCount(self::HISTORY_COUNT, $response->getBody());
    }

    /**
     * Test retrieve sales order comments collection
     *
     * @magentoDataFixture fixture/Sales/Order/customer_order.php
     * @resourceOperation order_comment::multiget
     */
    public function testRetrieveVisibleOnFront()
    {
        /** @var $order Mage_Sales_Model_Order */
        $order = $this->getFixture('customer_order');

        $this->_generateCollection($order)
            ->_generateCollection($order, 0);
        $order->save();

        $response = $this->callGet("orders/{$order->getId()}/comments");

        $this->assertEquals(Mage_Webapi_Controller_Handler_Rest::HTTP_OK, $response->getStatus());
        $this->assertCount(self::HISTORY_COUNT, $response->getBody());
    }

    /**
     * Test retrieve another sales order comments collection
     *
     * @magentoDataFixture fixture/Sales/Order/order.php
     * @resourceOperation order_comment::multiget
     */
    public function testRetrieveForeignResource()
    {
        /* @var $fixtureOrder Mage_Sales_Model_Order */
        $fixtureOrder = $this->getFixture('order');
        $restResponse = $this->callGet("orders/{$fixtureOrder->getId()}/comments");
        $this->assertEquals(Mage_Webapi_Exception::HTTP_NOT_FOUND, $restResponse->getStatus());
    }

    /**
     * Test update action
     *
     * @resourceOperation order_comment::update
     */
    public function testUpdate()
    {
        $response = $this->callPut('orders/1/comments', array('qwerty'));
        $this->assertEquals(Mage_Webapi_Exception::HTTP_FORBIDDEN, $response->getStatus());
    }

    /**
     * Test delete action
     *
     * @resourceOperation order_comment::delete
     */
    public function testDelete()
    {
        $response = $this->callDelete('orders/1/comments', array('qwerty'));
        $this->assertEquals(Mage_Webapi_Exception::HTTP_FORBIDDEN, $response->getStatus());
    }
}
