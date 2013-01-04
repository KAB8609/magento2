<?php
/**
 * Gift card account API model tests.
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 * @magentoDbIsolation enabled
 */
class Enterprise_GiftCardAccount_Model_ApiTest extends PHPUnit_Framework_TestCase
{
    public static $code;

    /**
     * Test create, list, info, update, remove
     *
     * @magentoDataFixture Enterprise/GiftCardAccount/_files/code_pool.php
     */
    public function testCRUD()
    {
        $testModel = Mage::getModel('Enterprise_GiftCardAccount_Model_Giftcardaccount');
        $accountFixture = simplexml_load_file(
            dirname(__FILE__) . '/../_files/fixture/giftcard_account.xml'
        );

        //Test create
        $createData = Magento_Test_Helper_Api::simpleXmlToArray($accountFixture->create);
        $accountId = Magento_Test_Helper_Api::call($this, 'giftcardAccountCreate', array((object)$createData));
        $this->assertGreaterThan(0, $accountId);

        $testModel->load($accountId);
        $this->_testDataCorrect($createData, $testModel);

        //Test list
        $list = Magento_Test_Helper_Api::call($this, 'giftcardAccountList', array('filters' => array()));
        $this->assertInternalType('array', $list);
        $this->assertGreaterThan(0, count($list));

        //Test info
        $info = Magento_Test_Helper_Api::call($this, 'giftcardAccountInfo', array('giftcardAccountId' => $accountId));

        unset($createData['status']);
        unset($createData['website_id']);
        $info['date_expires'] = $info['expire_date'];
        $this->_testDataCorrect($createData, new Varien_Object($info));

        //Test update
        $updateData = Magento_Test_Helper_Api::simpleXmlToArray($accountFixture->update);
        $updateResult = Magento_Test_Helper_Api::call($this,
            'giftcardAccountUpdate',
            array('giftcardAccountId' => $accountId, 'giftcardData' => $updateData)
        );
        $this->assertTrue($updateResult);

        $testModel->load($accountId);
        $this->_testDataCorrect($updateData, $testModel);

        //Test remove
        $removeResult = Magento_Test_Helper_Api::call(
            $this,
            'giftcardAccountRemove',
            array('giftcardAccountId' => $accountId)
        );
        $this->assertTrue($removeResult);

        /** @var $pool Enterprise_GiftCardAccount_Model_Pool */
        $pool = Mage::getModel('Enterprise_GiftCardAccount_Model_Pool');
        $pool->setCode(self::$code);
        $pool->delete();

        //Test item was really removed and fault was Exception thrown
        $this->setExpectedException('SoapFault');
        Magento_Test_Helper_Api::call($this, 'giftcardAccountRemove', array('giftcardAccountId' => $accountId));
    }

    /**
     * Test Exception on invalid data
     *
     * @expectedException SoapFault
     */
    public function testCreateExceptionInvalidData()
    {
        $fixture = simplexml_load_file(dirname(__FILE__) . '/../_files/fixture/giftcard_account.xml');

        $invalidCreateData = Magento_Test_Helper_Api::simpleXmlToArray($fixture->invalidCreate);
        Magento_Test_Helper_Api::call($this, 'giftcardAccountCreate', array($invalidCreateData));
    }

    /**
     * Test giftcard account not found exception
     *
     * @expectedException SoapFault
     */
    public function testExceptionNotFound()
    {
        $fixture = simplexml_load_file(dirname(__FILE__) . '/../_files/fixture/giftcard_account.xml');

        $invalidData = Magento_Test_Helper_Api::simpleXmlToArray($fixture->invalidInfo);
        Magento_Test_Helper_Api::call($this, 'giftcardAccountInfo', array($invalidData->giftcardId));
    }

    /**
     * Test that data in db and webservice are equals
     *
     * @param array $data
     * @param Varien_Object $testModel
     */
    protected function _testDataCorrect($data, $testModel)
    {
        foreach ($data as $testKey => $value) {
            $this->assertEquals($value, $testModel->getData($testKey));
        }
    }
}
