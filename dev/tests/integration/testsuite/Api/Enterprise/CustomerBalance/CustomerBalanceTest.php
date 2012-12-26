<?php
/**
 * Customer balance tests.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @magentoApiDataFixture Enterprise/CustomerBalance/_fixture/CustomerBalance.php
 */
class Enterprise_CustomerBalance_CustomerBalanceTest extends Magento_Test_TestCase_ApiAbstract
{
    /**
     * Customer fixture
     *
     * @var Mage_Customer_Model_Customer
     */
    public static $customer = null;

    /**
     * Customer without balance fixture
     *
     * @var Mage_Customer_Model_Customer
     */
    public static $customerWithoutBalance = null;

    /**
     * Test successful customer balance info
     *
     * @return void
     */
    public function testCustomerBalanceBalance()
    {
        $customerBalanceFixture = simplexml_load_file(dirname(__FILE__) . '/_fixture/CustomerBalance.xml');
        $data = self::simpleXmlToArray($customerBalanceFixture);

        $data['input']['customerId'] = self::$customer->getId();

        $result = $this->call('storecredit.balance', $data['input']);

        $this->assertEquals($data['expected']['balance'], $result, 'This balance value is not expected');
    }

    /**
     * Test customer balance info exception: balance not found
     *
     * @depends testCustomerBalanceBalance
     * @expectedException SoapFault
     * @return void
     */
    public function testCustomerBalanceBalanceExceptionBalanceNotFound()
    {
        $customerBalanceFixture = simplexml_load_file(
            dirname(__FILE__) . '/_fixture/CustomerBalanceExceptionBalanceNotFound.xml'
        );
        $data = self::simpleXmlToArray($customerBalanceFixture);

        $data['input']['customer_id'] = self::$customerWithoutBalance->getId();

        $this->call('storecredit.balance', $data['input']);
    }

    /**
     * Test successful customer balance history
     *
     * @depends testCustomerBalanceBalance
     * @return void
     */
    public function testCustomerBalanceHistory()
    {
        $customerBalanceHistoryFixture = simplexml_load_file(
            dirname(__FILE__) . '/_fixture/CustomerBalanceHistory.xml'
        );
        $data = self::simpleXmlToArray($customerBalanceHistoryFixture);

        $data['input']['customerId'] = self::$customer->getId();

        $result = $this->call('storecredit.history', $data['input']);

        $this->assertEquals(count($data['expected']['history_items']), count($result), 'History checksum fail');

        foreach ($data['expected']['history_items'] as $index => $expectedHistoryItem) {
            foreach ($expectedHistoryItem as $key => $value) {
                $this->assertEquals($value, $result[$index][$key], 'History item value fail');
            }
        }
    }

    /**
     * Test customer balance history exception: history not found
     *
     * @depends testCustomerBalanceHistory
     * @expectedException SoapFault
     * @return void
     */
    public function testCustomerBalanceHistoryExceptionHistoryNotFound()
    {
        $customerBalanceHistoryFixture = simplexml_load_file(
            dirname(__FILE__) . '/_fixture/CustomerBalanceExceptionHistoryNotFound.xml'
        );
        $data = self::simpleXmlToArray($customerBalanceHistoryFixture);

        $data['input']['customerId'] = self::$customerWithoutBalance->getId();

        $this->call('storecredit.history', $data['input']);
    }


    public static function tearDownAfterClass()
    {
        Mage::register('isSecureArea', true);

        self::$customer->delete();
        self::$customerWithoutBalance->delete();

        self::$customer = null;
        self::$customerWithoutBalance = null;

        Mage::unregister('isSecureArea');
        parent::tearDownAfterClass();
    }
}
