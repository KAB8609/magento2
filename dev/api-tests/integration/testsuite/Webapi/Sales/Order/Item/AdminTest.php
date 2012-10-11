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
 * Test for order items (admin) Webapi
 *
 * @category    Magento
 * @package     Magento_Test
 * @author      Magento Api Team <api-team@magento.com>
 */
class Webapi_Sales_Order_Item_AdminTest extends Magento_Test_Webservice_Rest_Admin
{
    /**
     * Delete fixtures
     */
    protected function tearDown()
    {
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
     * Test get order items for admin
     *
     * @magentoDataFixture fixture/Sales/Order/order.php
     * @resourceOperation order_item::multiget
     */
    public function testGetOrderItems()
    {
        /* @var $fixtureOrder Mage_Sales_Model_Order */
        $fixtureOrder = $this->getFixture('order');

        $restResponse = $this->callGet('orders/' . $fixtureOrder->getId() . '/items');
        $this->assertEquals(Mage_Webapi_Controller_Front_Rest::HTTP_OK, $restResponse->getStatus());

        $responseData = $restResponse->getBody();
        $this->assertNotEmpty($responseData);

        $orderItemsIds = array();
        foreach ($responseData as $orderItem) {
            $orderItemsIds[] = $orderItem['item_id'];
        }
        /* @var $fixtureItems Mage_Sales_Model_Resource_Order_Item_Collection */
        $fixtureItems = $fixtureOrder->getItemsCollection();
        foreach ($fixtureItems as $fixtureItem) {
            $this->assertContains($fixtureItem->getId(), $orderItemsIds, 'Order item should be in response');
        }
    }

    /**
     * Test retrieving items for not existing order
     *
     * @resourceOperation order_item::multiget
     */
    public function testGetItemsForUnavailableOrder()
    {
        $restResponse = $this->callGet('orders/invalid_id/items');
        $this->assertEquals(Mage_Webapi_Exception::HTTP_NOT_FOUND, $restResponse->getStatus());
    }
}
