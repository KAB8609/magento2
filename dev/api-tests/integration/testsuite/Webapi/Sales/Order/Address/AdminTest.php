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
 * Test for order addresses (admin) Webapi
 *
 * @category    Magento
 * @package     Magento_Test
 * @author      Magento Api Team <api-team@magento.com>
 */
class Webapi_Sales_Order_Address_AdminTest extends Magento_Test_Webservice_Rest_Admin
{
    /**
     * Delete fixtures
     */
    protected function tearDown()
    {
        Magento_Test_Webservice::deleteFixture('order', true);
        Magento_Test_Webservice::deleteFixture('quote', true);
        Magento_Test_Webservice::deleteFixture('product1', true);
        Magento_Test_Webservice::deleteFixture('product2', true);

        parent::tearDown();
    }

    /**
     * Test get order address for admin
     *
     * @magentoDataFixture fixture/Sales/Order/order_address.php
     * @resourceOperation order_address::get
     */
    public function testGetOrderAddress()
    {
        /* @var $order Mage_Sales_Model_Order */
        $order = $this->getFixture('order');

        //test billing
        $restResponse = $this->callGet('orders/' . $order->getId() . '/addresses/billing');
        $this->assertEquals(Mage_Webapi_Controller_Front_Rest::HTTP_OK, $restResponse->getStatus());

        $responseData = $restResponse->getBody();
        $this->assertNotEmpty($responseData);

        $this->assertEquals(
            $order->getBillingAddress()->getCity(),
            $responseData['city']
        );

        //test shipping
        $restResponse = $this->callGet('orders/' . $order->getId() . '/addresses/shipping');
        $this->assertEquals(Mage_Webapi_Controller_Front_Rest::HTTP_OK, $restResponse->getStatus());

        $responseData = $restResponse->getBody();
        $this->assertNotEmpty($responseData);

        $this->assertEquals(
            $order->getShippingAddress()->getCity(),
            $responseData['city']
        );
    }

    /**
     * Test retrieving address for not existing order
     *
     * @resourceOperation order_address::get
     */
    public function testGetAddressForUnavailableOrder()
    {
        $restResponse = $this->callGet('orders/invalid_id/addresses/billing');
        $this->assertEquals(Mage_Webapi_Exception::HTTP_NOT_FOUND, $restResponse->getStatus());

        $restResponse = $this->callGet('orders/invalid_id/addresses/shipping');
        $this->assertEquals(Mage_Webapi_Exception::HTTP_NOT_FOUND, $restResponse->getStatus());
    }

    /**
     * Test get order address for admin
     *
     * @magentoDataFixture fixture/Sales/Order/order_address.php
     * @resourceOperation order_address::multiget
     */
    public function testGetOrderAddresses()
    {
        /* @var $order Mage_Sales_Model_Order */
        $order = $this->getFixture('order');

        $restResponse = $this->callGet('orders/' . $order->getId() . '/addresses');
        $this->assertEquals(Mage_Webapi_Controller_Front_Rest::HTTP_OK, $restResponse->getStatus());

        $responseData = $restResponse->getBody();
        $this->assertNotEmpty($responseData);
        $this->assertCount(2, $responseData);

        $addressByType = array();
        foreach ($responseData as $address) {
            $type = $address['address_type'];
            $addressByType[$type] = $address;
        }

        $this->assertEquals(
            $order->getShippingAddress()->getCity(),
            $addressByType[Mage_Customer_Model_Address_Abstract::TYPE_SHIPPING]['city']
        );

        $this->assertEquals(
            $order->getBillingAddress()->getCity(),
            $addressByType[Mage_Customer_Model_Address_Abstract::TYPE_BILLING]['city']
        );



    }

    /**
     * Test retrieving address for not existing order
     *
     * @resourceOperation order_address::multiget
     */
    public function testGetAddressesForUnavailableOrder()
    {
        $restResponse = $this->callGet('orders/invalid_id/addresses');

        $this->assertEquals(Mage_Webapi_Exception::HTTP_NOT_FOUND, $restResponse->getStatus());
    }
}
