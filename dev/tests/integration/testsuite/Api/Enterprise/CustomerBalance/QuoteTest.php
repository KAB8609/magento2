<?php
/**
 * Customer balance quote tests.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @magentoDataFixture Api/Enterprise/CustomerBalance/_fixture/Quote.php
 */
class Enterprise_CustomerBalance_QuoteTest extends PHPUnit_Framework_TestCase
{
    /**
     * Customer fixture
     *
     * @var Mage_Customer_Model_Customer
     */
    public static $customer = null;

    /**
     * Product fixture
     *
     * @var Mage_Catalog_Model_Product
     */
    public static $product = null;

    /**
     * Shopping cart fixture
     *
     * @var Mage_Sales_Model_Quote
     */
    public static $quote = null;

    /**
     * Shopping shoppingCartCreated by guest fixture
     *
     * @var Mage_Sales_Model_Quote
     */
    public static $guestQuote = null;

    /**
     * Test successful customer balance set amount to quote
     *
     * @return void
     */
    public function testCustomerBalanceForQuoteSetAmount()
    {
        $quoteFixture = simplexml_load_file(dirname(__FILE__) . '/_fixture/CustomerBalanceForQuote.xml');
        $data = Magento_Test_Helper_Api::simpleXmlToArray($quoteFixture);

        $data['input']['quoteId'] = self::$quote->getId();

        $result = Magento_Test_Helper_Api::call($this, 'shoppingCartCustomerbalanceSetAmount', $data['input']);
        $this->assertEquals($data['expected']['used_amount'], $result, 'Used amount is invalid');
    }

    /**
     * Test successful customer balance remove amount from quote
     *
     * @depends testCustomerBalanceForQuoteSetAmount
     * @return void
     */
    public function testCustomerBalanceForQuoteRemoveAmount()
    {
        $quoteFixture = simplexml_load_file(dirname(__FILE__) . '/_fixture/CustomerBalanceForQuote.xml');
        $data = Magento_Test_Helper_Api::simpleXmlToArray($quoteFixture);

        $data['input']['quoteId'] = self::$quote->getId();

        $this->assertTrue(Magento_Test_Helper_Api::call(
            $this,
            'shoppingCartCustomerbalanceRemoveAmount',
            $data['input']),
            'Remove used amount fail');

        $quote = Mage::getModel('Mage_Sales_Model_Quote');
        $quote->load(self::$quote->getId());
        $this->assertEquals(0, $quote->getCustomerBalanceAmountUsed(), 'Used amount must be deleted');
    }

    /**
     * Test successful customer balance set amount to quote using quote_id only
     *
     * @return void
     */
    public function testCustomerBalanceForQuoteSetAmountWithoutStoreId()
    {
        $quoteFixture = simplexml_load_file(dirname(__FILE__) . '/_fixture/CustomerBalanceForQuote.xml');
        $data = Magento_Test_Helper_Api::simpleXmlToArray($quoteFixture);

        $result = Magento_Test_Helper_Api::call($this, 'shoppingCartCustomerbalanceSetAmount',
            array('quoteId' => self::$quote->getId()));

        $this->assertEquals($data['expected']['used_amount'], $result, 'Used amount is invalid');
    }

    /**
     * Test successful customer balance remove amount from quote using quote_id only
     *
     * @depends testCustomerBalanceForQuoteSetAmountWithoutStoreId
     * @return void
     */
    public function testCustomerBalanceForQuoteRemoveAmountWithoutStoreId()
    {
        $input = array('quoteId' => self::$quote->getId());
        $this->assertTrue(Magento_Test_Helper_Api::call($this, 'shoppingCartCustomerbalanceRemoveAmount', $input),
            'Remove used amount fail');

        $quote = Mage::getModel('Mage_Sales_Model_Quote');
        $quote->load(self::$quote->getId());
        $this->assertEquals(0, $quote->getCustomerBalanceAmountUsed(), 'Used amount must be deleted');
    }

    /**
     * Test successful customer balance set amount to quote using store code
     *
     * @return void
     */
    public function testCustomerBalanceForQuoteSetAmountUsingStoreCode()
    {
        $quoteFixture = simplexml_load_file(dirname(__FILE__) . '/_fixture/CustomerBalanceForQuoteUsingStoreCode.xml');
        $data = Magento_Test_Helper_Api::simpleXmlToArray($quoteFixture);

        $data['input']['quoteId'] = self::$quote->getId();

        $result = Magento_Test_Helper_Api::call($this, 'shoppingCartCustomerbalanceSetAmount', (array)$data['input']);
        $this->assertEquals($data['expected']['used_amount'], $result, 'Used amount is invalid');
    }

    /**
     * Test successful customer balance remove amount from quote using store code
     *
     * @depends testCustomerBalanceForQuoteSetAmountUsingStoreCode
     * @return void
     */
    public function testCustomerBalanceForQuoteRemoveAmountUsingStoreCode()
    {
        $quoteFixture = simplexml_load_file(dirname(__FILE__) . '/_fixture/CustomerBalanceForQuoteUsingStoreCode.xml');
        $data = Magento_Test_Helper_Api::simpleXmlToArray($quoteFixture);

        $data['input']['quoteId'] = self::$quote->getId();

        $this->assertTrue(
            Magento_Test_Helper_Api::call($this, 'shoppingCartCustomerbalanceRemoveAmount', (array)$data['input']),
            'Remove used amount fail'
        );

        $quote = Mage::getModel('Mage_Sales_Model_Quote');
        $quote->load(self::$quote->getId());
        $this->assertEquals(0, $quote->getCustomerBalanceAmountUsed(), 'Used amount must be deleted');
    }

    /**
     * Test customer balance set amount to quote using store code exception: No store found with requested id or code.
     *
     * @expectedException SoapFault
     * @return void
     */
    public function testCustomerBalanceForQuoteSetAmountUsingInvalidStoreCodeException()
    {
        $quoteFixture = simplexml_load_file(
            dirname(__FILE__) . '/_fixture/CustomerBalanceForQuoteUsingInvalidStoreCode.xml'
        );
        $data = Magento_Test_Helper_Api::simpleXmlToArray($quoteFixture);

        $data['input']['quoteId'] = self::$quote->getId();

        Magento_Test_Helper_Api::call($this, 'shoppingCartCustomerbalanceSetAmount', (array)$data['input']);
    }

    /**
     * Test customer balance set amount to quote exception:  No quote found with requested id.
     *
     * @expectedException SoapFault
     * @return void
     */
    public function testCustomerBalanceForQuoteSetAmountExceptionQuoteNotExists()
    {
        $quoteFixture = simplexml_load_file(dirname(__FILE__) . '/_fixture/CustomerBalanceForQuote.xml');
        $data = Magento_Test_Helper_Api::simpleXmlToArray($quoteFixture);

        Magento_Test_Helper_Api::call($this, 'shoppingCartCustomerbalanceSetAmount', (array)$data['input']);
    }

    /**
     * Test customer balance remove amount from quote exception: No quote found with requested id.
     *
     * @expectedException SoapFault
     * @return void
     */
    public function testCustomerBalanceForQuoteRemoveAmountExceptionQuoteNotExists()
    {
        $quoteFixture = simplexml_load_file(dirname(__FILE__) . '/_fixture/CustomerBalanceForQuote.xml');
        $data = Magento_Test_Helper_Api::simpleXmlToArray($quoteFixture);

        Magento_Test_Helper_Api::call($this, 'shoppingCartCustomerbalanceRemoveAmount', (array)$data['input']);
    }

    /**
     * Test customer balance set amount to quote exception:
     * Store credit can not be used for quote created by guest.
     *
     * @expectedException SoapFault
     * @return void
     */
    public function testCustomerBalanceForQuoteSetAmountExceptionGuestQuote()
    {
        $quoteFixture = simplexml_load_file(dirname(__FILE__) . '/_fixture/CustomerBalanceForGuestQuote.xml');
        $data = Magento_Test_Helper_Api::simpleXmlToArray($quoteFixture);

        $data['input']['quoteId'] = self::$guestQuote->getId();

        Magento_Test_Helper_Api::call($this, 'shoppingCartCustomerbalanceSetAmount', (array)$data['input']);
    }

    /**
     * Test customer balance remove amount from quote exception:
     * Store credit can not be used for quote created by guest.
     *
     * @expectedException SoapFault
     * @return void
     */
    public function testCustomerBalanceForQuoteRemoveAmountExceptionGuestQuote()
    {
        $quoteFixture = simplexml_load_file(dirname(__FILE__) . '/_fixture/CustomerBalanceForGuestQuote.xml');
        $data = Magento_Test_Helper_Api::simpleXmlToArray($quoteFixture);

        $data['input']['quoteId'] = self::$guestQuote->getId();

        Magento_Test_Helper_Api::call($this, 'shoppingCartCustomerbalanceRemoveAmount', (array)$data['input']);
    }
}
