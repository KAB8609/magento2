<?php
/**
 * Magento_Webhook_Model_Subscription_Config
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Webhook
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Webhook_Model_Subscription_ConfigTest extends PHPUnit_Framework_TestCase
{

    /**
     * String constants representing XML strings to be used in stub config element.
     *
     * Single-quotes are used because phpcs does not handle heredocs well.
     */
    const SETTING_NAME_XML =
                    '<xml>
                        <setting_name>
                            <name>Extension Name</name>
                        </setting_name>
                    </xml>';

    const NAME_MISSING_XML =
                '<xml>
                    <name_missing>
                        <!-- Missing name on purpose -->
                    </name_missing>
                </xml>';

    const EXISTING_SUBSCRIPTION =
                '<xml>
                    <setting_name_on_existing_subscription>
                        <name>Extension Name</name>
                        <topics>
                            <topic_one>
                                <subcall/>
                            </topic_one>
                        </topics>
                    </setting_name_on_existing_subscription>
                </xml>';

    const AUTHENTICATION_TYPE =
        '<xml>
            <setting_authentication_type>
                <name>Extension Name</name>
                <authentication_type>HMAC</authentication_type>
            </setting_authentication_type>
        </xml>';

    /** @var Magento_Webhook_Model_Subscription_Config that is also our unit under test */
    protected $_config;

    /** @var PHPUnit_Framework_MockObject_MockObject */
    protected $_mockMageConfig;

    /** @var PHPUnit_Framework_MockObject_MockObject */
    protected $_mockSubscribFactory;

    /** @var PHPUnit_Framework_MockObject_MockObject */
    protected $_mockCollection;

    /** @var PHPUnit_Framework_MockObject_MockObject */
    protected $_mockLogger;

    /** @var PHPUnit_Framework_MockObject_MockObject */
    protected $_mockSubscription;

    public function setUp()
    {
        $this->_mockSubscription = $this->_createMockSubscription();
    }



    public function testSettingNameNewSubscription()
    {
        $configNode = $this->_createStubConfigElement(self::SETTING_NAME_XML);

        // Set expectations
        $this->_mockSubscription->expects($this->atLeastOnce())
            ->method('save');
        $this->_mockSubscription->expects($this->atLeastOnce())
            ->method('setName')
            ->with($this->equalTo('Extension Name'));

        $this->_stubMock($configNode);

        // Run test
        $this->_config->updateSubscriptionCollection();
    }

    public function testNameMissing()
    {
        $configNode = $this->_createStubConfigElement(self::NAME_MISSING_XML);

        // Set expectations
        $this->_mockSubscription->expects($this->never())
            ->method('save');
        $this->_mockSubscription->expects($this->never())
            ->method('setName');

        $expectedErrors = array(
            __("Invalid config data for subscription '%1'.", 'name_missing'),
        );

        $this->_stubMock($configNode, null, $expectedErrors);

        // Run test
        $this->_config->updateSubscriptionCollection();
    }

    public function testSettingNameExistingSubscription()
    {
        // Make sure we never call save or setName on the existing subscription
        $existingSubscription = $this->_createMockSubscription();
        $existingSubscription->expects($this->once())
            ->method('save');
        $existingSubscription->expects($this->once())
            ->method('setName');

        $configNode = $this->_createStubConfigElement(self::EXISTING_SUBSCRIPTION);

        // Set expectations
        $this->_mockSubscription->expects($this->never())
            ->method('save');
        $this->_mockSubscription->expects($this->never())
            ->method('setName');

        $subxCollection = $this->_createMockSubscriptionCollection(
            array(
                'setting_name_on_existing_subscription' => array(&$existingSubscription)
            )
        );

        $this->_stubMock($configNode, $subxCollection);

        // Run test
        $this->_config->updateSubscriptionCollection();
    }

    public function testSettingAuthenticationType()
    {
        $configNode = $this->_createStubConfigElement(self::AUTHENTICATION_TYPE);

        // Set expectations
        $this->_mockSubscription->expects($this->atLeastOnce())
            ->method('save');
        $this->_mockSubscription->expects($this->atLeastOnce())
            ->method('setName')
            ->with($this->equalTo('Extension Name'));
        $this->_mockSubscription->expects($this->atLeastOnce())
            ->method('setAuthenticationType')
            ->with($this->equalTo('HMAC'));

        $this->_stubMock($configNode);

        // Run test
        $this->_config->updateSubscriptionCollection();
    }

    /**
     * Internal factory for mock subscription, stubs necessary magic methods
     *
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    protected function _createMockSubscription()
    {
        // We need to define all magic methods.  Once we define any method, we need to define all methods
        // If we don't define any methods, then we can only stub out concrete methods, but not any
        // of the magic methods, since they weren't explicitly defined.
        $methods = array('setData', 'getData', 'unsetData', 'save', 'setName', 'setTopics', 'setFormat',
                        'setEndpointUrl', 'getAuthenticationOptions', 'unsetAuthenticationOption',
                        'setAuthenticationType');
        $mock = $this->getMockBuilder('Magento_Webhook_Model_Subscription')
            ->disableOriginalConstructor()
            ->setMethods($methods)
            ->getMock();
        foreach ($methods as $method) {
            $mock->expects($this->any())
                ->method($method)
                ->will($this->returnSelf());
        }
        return $mock;
    }

    /**
     * Creates stub config element given a fragment of valid xml string
     *
     * @param string $xmlString
     * @return Magento_Core_Model_Config_Element
     */
    protected function _createStubConfigElement($xmlString)
    {
        return new Magento_Core_Model_Config_Element($xmlString);
    }

    /**
     * Initializes a set of mocks and stubs
     *
     * @param Magento_Core_Model_Config_Element          $configNode
     * @param PHPUnit_Framework_MockObject_MockObject $subxCollection
     *        Mocks Magento_Webhook_Model_Resource_Subscription_Collection
     * @param string[]                                $expectedErrors
     */
    protected function _stubMock($configNode, $subxCollection = null, $expectedErrors = null)
    {
        // Mock objects
        $this->_mockCollection = $this->getMockBuilder('Magento_Webhook_Model_Resource_Subscription_Collection')
            ->disableOriginalConstructor()
            ->getMock();

        $this->_mockMageConfig = $this->getMockBuilder('Magento_Core_Model_Config')
            ->disableOriginalConstructor()
            ->getMock();

        $this->_mockSubscribFactory = $this->getMockBuilder('Magento_Webhook_Model_Subscription_Factory')
            ->disableOriginalConstructor()
            ->getMock();

        $this->_mockLogger = $this->getMockBuilder('Magento_Core_Model_Logger')
            ->disableOriginalConstructor()
            ->getMock();

        // Stub create
        $this->_mockSubscribFactory->expects($this->any())
            ->method('create')
            ->will($this->returnValue($this->_mockSubscription));

        // Stub logException
        if ($expectedErrors !== null) {
            $this->_mockLogger->expects($this->once())
                ->method('logException')
                ->with($this->equalTo(new Magento_Webhook_Exception(implode("\n", $expectedErrors))));
        }

        // Stub getNode
        $this->_mockMageConfig->expects($this->any())
            ->method('getNode')
            ->with($this->equalTo(Magento_Webhook_Model_Subscription_Config::XML_PATH_SUBSCRIPTIONS))
            ->will($this->returnValue($configNode));

        // Get or set subscription collection mock
        if ($subxCollection !== null) {
            $this->_mockCollection = $subxCollection;
        } else {
            $this->_mockCollection = $this->_createMockSubscriptionCollection();
        }

        // Create config object
        $this->_config = new Magento_Webhook_Model_Subscription_Config(
            $this->_mockCollection,
            $this->_mockMageConfig,
            $this->_mockSubscribFactory,
            $this->_mockLogger);
    }

    /**
     * Pseudo-factory method for mock subscription collection
     *
     * @param array $idToSubscriptionsMap
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    protected function _createMockSubscriptionCollection($idToSubscriptionsMap = array())
    {
        $mock = $this->getMockBuilder('Magento_Webhook_Model_Resource_Subscription_Collection')
            ->disableOriginalConstructor()
            ->getMock();
        // Order matters when mocking out methods.  We need the more restrictive one first.
        foreach ($idToSubscriptionsMap as $id => $subscriptions) {
            $mock->expects($this->any())
                ->method('getSubscriptionsByAlias')
                ->with($this->equalTo($id))
                ->will($this->returnValue($subscriptions));
        }
        // Put the less restrictive stub at the end
        $mock->expects($this->any())
            ->method('getSubscriptionsByAlias')
            ->will($this->returnValue(array()));
        return $mock;
    }
}