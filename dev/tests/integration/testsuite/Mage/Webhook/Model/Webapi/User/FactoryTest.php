<?php
/**
 * Mage_Webhook_Model_Webapi_User_Factory
 *
 * @magentoDbIsolation enabled
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Webhook
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Webhook_Model_Webapi_User_FactoryTest extends PHPUnit_Framework_TestCase
{
    /** Values being sent to user service */
    const VALUE_COMPANY_NAME = 'company name';
    const VALUE_SECRET_VALUE = 'secret_value';
    const VALUE_KEY_VALUE = 'key_value';
    const VALUE_EMAIL = 'email@example.com';

    /** @var  array */
    private $_userContext;

    /** @var  int */
    private $_apiUserId;

    public function setUp()
    {
        $this->_userContext = array(
            'email'     => self::VALUE_EMAIL,
            'key'       => self::VALUE_KEY_VALUE,
            'secret'    => self::VALUE_SECRET_VALUE,
            'company'   => self::VALUE_COMPANY_NAME
        );
    }

    public function tearDown()
    {
        /** @var Magento_Webapi_Model_Acl_User $user */
        $user = Mage::getModel('Magento_Webapi_Model_Acl_User');
        $user->load($this->_apiUserId);
        $user->delete();
    }

    public function testCreate()
    {
        /** @var Mage_Webhook_Model_Webapi_User_Factory $userFactory */
        $userFactory = Mage::getModel('Mage_Webhook_Model_Webapi_User_Factory');
        $this->_apiUserId = $userFactory->createUser($this->_userContext, array('webhook/create'));

        /** @var Magento_Webapi_Model_Acl_User $user */
        $user = Mage::getModel('Magento_Webapi_Model_Acl_User');
        $user->load($this->_apiUserId);

        $this->assertEquals(self::VALUE_COMPANY_NAME, $user->getCompanyName());
        $this->assertEquals(self::VALUE_EMAIL, $user->getContactEmail());
        $this->assertEquals(self::VALUE_SECRET_VALUE, $user->getSecret());
        $this->assertEquals(self::VALUE_KEY_VALUE, $user->getApiKey());
        $this->assertNotEquals(0, $user->getRoleId());

        /** @var Magento_Webapi_Model_Resource_Acl_Rule $ruleResources */
        $ruleResources = Mage::getModel('Magento_Webapi_Model_Resource_Acl_Rule');
        $rules = $ruleResources->getResourceIdsByRole($user->getRoleId());
        $this->assertNotEmpty($rules);
    }

}
