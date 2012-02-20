<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Magento
 * @package     Magento_Test
 * @subpackage  integration_tests
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magento.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Test for order item API2
 *
 * @category    Magento
 * @package     Magento_Test
 * @author      Magento Api Team <api-team@magento.com>
 */
class Api2_Sales_Order_AdminTest extends Magento_Test_Webservice_Rest_Admin
{
    /**
     * Delete fixtures
     */
    protected function tearDown()
    {
        Magento_TestCase::deleteFixture('order', true);

        parent::tearDown();
    }

    /**
     * Restore original options
     */
    public static function tearDownAfterClass()
    {
        Magento_TestCase::deleteFixture('role', true);
        Magento_TestCase::deleteFixture('rule', true);
        Magento_TestCase::deleteFixture('attribute', true);

        parent::tearDownAfterClass();
    }

    /**
     * Test get order item for admin
     *
     * @magentoDataFixture Api2/Sales/_fixtures/prepare_admin_acl.php
     * @magentoDataFixture Api2/Sales/_fixtures/order.php
     */
    public function testGetOrder()
    {
        /* @var $fixtureOrder Mage_Sales_Model_Order */
        $fixtureOrder = $this->getFixture('order');
        $restResponse = $this->callGet('orders/' . $fixtureOrder->getId());
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_OK, $restResponse->getStatus());

        $responseData = $restResponse->getBody();
        $this->assertNotEmpty($responseData);

        $orderOriginalData = $fixtureOrder->getData();
        foreach ($responseData as $field => $value) {
            if (isset($orderOriginalData[$field])) {
                $this->assertEquals($orderOriginalData[$field], $value);
            }
        }
    }

    /**
     * Test retrieving not existing order item
     *
     * @magentoDataFixture Api2/Sales/_fixtures/prepare_admin_acl.php
     */
    public function testGetUnavailableOrder()
    {
        $restResponse = $this->callGet('orders/' . 'invalid_id');
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_NOT_FOUND, $restResponse->getStatus());
    }
}
