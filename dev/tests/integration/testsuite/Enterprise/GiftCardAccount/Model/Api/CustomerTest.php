<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Enterprise_GiftCardAccount_Model_Api_CustomerTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test giftcard customer info by code
     *
     * @magentoDataFixture Enterprise/GiftCardAccount/_files/code_pool.php
     * @magentoDataFixture Enterprise/GiftCardAccount/_files/giftcardaccount.php
     *
     * @return void
     */
    public function testInfo()
    {
        $balance = 9.99;
        $dateExpires = date('Y-m-d');
        $code = 'giftcardaccount_fixture';

        $info = Magento_Test_Helper_Api::call($this, 'giftcardCustomerInfo', array('code' => $code));
        $this->assertEquals($balance, $info['balance']);
        $this->assertEquals($dateExpires, $info['expire_date']);
    }

    /**
     * Test redeem amount present on gift card to Store Credit.
     *
     * @magentoDataFixture Mage/Customer/_files/customer.php
     * @magentoDataFixture Enterprise/GiftCardAccount/_files/code_pool.php
     * @magentoDataFixture Enterprise/GiftCardAccount/_files/giftcardaccount.php
     *
     * @return void
     */
    public function testRedeem()
    {
        $code = 'giftcardaccount_fixture';

        //Fixture customer id
        $customerId = 1;
        $storeId = 1;

        $result = Magento_Test_Helper_Api::call($this,
            'giftcardCustomerRedeem',
            array('code' => $code, 'customerId' => $customerId, 'storeId' => $storeId)
        );
        $this->assertTrue($result);

        //Test giftcard redeemed to customer balance
        $customerBalance = Mage::getModel('Enterprise_CustomerBalance_Model_Balance');
        $customerBalance->setCustomerId($customerId);
        $customerBalance->loadByCustomer();
        $this->assertEquals(9.99, $customerBalance->getAmount());

        //Test giftcard already redeemed
        $this->setExpectedException('SoapFault');
        Magento_Test_Helper_Api::call($this,
            'giftcardCustomerRedeem',
            array('code' => $code, 'customerId' => $customerId, 'storeId' => $storeId)
        );
    }

    /**
     * Test info throw exception with incorrect data
     *
     * @expectedException SoapFault
     * @return void
     */
    public function testIncorrectDataInfoException()
    {
        $fixture = simplexml_load_file(dirname(__FILE__) . '/../../_files/fixture/giftcard_customer.xml');
        $invalidData = Magento_Test_Helper_Api::simpleXmlToArray($fixture->invalid_info);
        Magento_Test_Helper_Api::call($this, 'giftcardCustomerInfo', (array)$invalidData);
    }

    /**
     * Test redeem throw exception with incorrect data
     *
     * @expectedException SoapFault
     * @return void
     */
    public function testIncorrectDataRedeemException()
    {
        $fixture = simplexml_load_file(dirname(__FILE__) . '/../../_files/fixture/giftcard_customer.xml');
        $invalidData = Magento_Test_Helper_Api::simpleXmlToArray($fixture->invalid_redeem);
        Magento_Test_Helper_Api::call($this, 'giftcardCustomerRedeem', (array)$invalidData);
    }
}
