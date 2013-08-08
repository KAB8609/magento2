<?php
/**
 * Mage_Webhook_Model_Resource_Subscription_Collection
 *
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Webhook
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Webhook_Model_Resource_Subscription_CollectionTest extends PHPUnit_Framework_TestCase
{
    /**
     * Arguments passed to methods under testing
     */
    const TOPIC = 'customer/topic';
    const ALIAS = 'some_alias';
    const API_USER_ID = 'api_user id';

    /** @var PHPUnit_Framework_MockObject_MockObject  */
    private $_connectionMock;

    /** @var PHPUnit_Framework_MockObject_MockObject  */
    private $_selectMock;

    /** @var PHPUnit_Framework_MockObject_MockObject  */
    private $_endpointResMock;

    /** @var PHPUnit_Framework_MockObject_MockObject  */
    private $_fetchStrategyMock;

    /** @var PHPUnit_Framework_MockObject_MockObject  */
    private $_resourceMock;

    public function setUp()
    {
        $this->_selectMock = $this->_makeMock('Zend_Db_Select');
        $this->_selectMock->expects($this->any())
            ->method('from')
            ->with(array('main_table' => null));
        $this->_connectionMock = $this->_makeMock('Magento_DB_Adapter_Pdo_Mysql');

        $this->_connectionMock->expects($this->any())
            ->method('select')
            ->will($this->returnValue($this->_selectMock));

        $subscriptionMock = $this->_makeMock('Mage_Webhook_Model_Subscription');
        $eventMgrMock = $this->_makeMock('Mage_Core_Model_Event_Manager');
        $configResourceMock = $this->_makeMock('Mage_Core_Model_Config_Resource');

        // Arguments to collection constructor
        $this->_fetchStrategyMock = $this->_makeMock('Magento_Data_Collection_Db_FetchStrategyInterface');
        $this->_endpointResMock = $this->_makeMock('Mage_Webhook_Model_Resource_Endpoint');
        $this->_resourceMock = $this-> _makeMock('Mage_Webhook_Model_Resource_Subscription');
        $this->_resourceMock->expects($this->any())
            ->method('getReadConnection')
            ->will($this->returnValue($this->_connectionMock));

        // Mock object manager
        $createReturnMap = array(
            array('Mage_Webhook_Model_Resource_Subscription', array(), $this->_resourceMock),
            array('Mage_Webhook_Model_Subscription', array(), $subscriptionMock)
        );
        $getReturnMap = array(
            array('Mage_Core_Model_Event_Manager', $eventMgrMock),
            array('Mage_Core_Model_Config_Resource', $configResourceMock)
        );
        $mockObjectManager = $this->_setMageObjectManager();
        $mockObjectManager->expects($this->any())
            ->method('create')
            ->will($this->returnValueMap($createReturnMap));
        $mockObjectManager->expects($this->any())
            ->method('get')
            ->will($this->returnValueMap($getReturnMap));
    }

    public function tearDown()
    {
        // Unsets object manager
        Mage::reset();
    }

    public function testInitialization()
    {
        $collection = $this->_makeCollectionMock(array('load')); // At least one method has to be specified
        $this->assertEquals('Mage_Webhook_Model_Subscription', $collection->getModelName());
        $this->assertEquals('Mage_Webhook_Model_Resource_Subscription', $collection->getResourceModelName());
    }

    public function testGetSubscriptionsByTopic()
    {
        $subscriptions = array('subscription1', 'subscription2', 'subscription3');
        $methods = array('getItems', 'addTopicFilter');
        $collection = $this->_makeCollectionMock($methods);

        $collection->expects($this->once())
            ->method('getItems')
            ->with()
            ->will($this->returnValue($subscriptions));
        $collection->expects($this->once())
            ->method('addTopicFilter')
            ->with(self::TOPIC)
            ->will($this->returnSelf());

        $this->assertEquals($subscriptions, $collection->getSubscriptionsByTopic(self::TOPIC));
    }

    public function testGetSubscriptionsByAlias()
    {
        $subscriptions = array('subscription1', 'subscription2', 'subscription3');
        $methods = array('getItems', 'addAliasFilter');
        $collection = $this->_makeCollectionMock($methods);

        $collection->expects($this->once())
            ->method('getItems')
            ->with()
            ->will($this->returnValue($subscriptions));
        $collection->expects($this->once())
            ->method('addAliasFilter')
            ->with(self::ALIAS)
            ->will($this->returnSelf());

        $this->assertEquals($subscriptions, $collection->getSubscriptionsByAlias(self::ALIAS));
    }

    public function testGetActivatedSubscriptionsWithoutApiUser()
    {
        $methods = array('addEndpointIdsFilter', 'getItems');
        $subscriptions = array('subscription1', 'subscription2', 'subscription3');
        $endpointIds = array('endpoint_id_1','endpoint_id_2','endpoint_id_3');

        $this->_endpointResMock->expects($this->once())
            ->method('getEndpointsWithoutApiUser')
            ->will($this->returnValue($endpointIds));

        $collection = $this->_makeCollectionMock($methods);
        $collection->expects($this->once())
            ->method('addEndpointIdsFilter')
            ->with($endpointIds)
            ->will($this->returnSelf());
        $collection->expects($this->once())
            ->method('getItems')
            ->will($this->returnValue($subscriptions));

        $this->assertEquals($subscriptions, $collection->getActivatedSubscriptionsWithoutApiUser());
    }

    public function testGetApiUserSubscriptions()
    {
        $methods = array('addEndpointIdsFilter', 'getItems');
        $subscriptions = array('subscription1', 'subscription2', 'subscription3');
        $endpointIds = array('endpoint_id_1','endpoint_id_2','endpoint_id_3');

        $this->_endpointResMock->expects($this->once())
            ->method('getApiUserEndpoints')
            ->with(self::API_USER_ID)
            ->will($this->returnValue($endpointIds));

        $collection = $this->_makeCollectionMock($methods);
        $collection->expects($this->once())
            ->method('addEndpointIdsFilter')
            ->with($endpointIds)
            ->will($this->returnSelf());
        $collection->expects($this->once())
            ->method('getItems')
            ->will($this->returnValue($subscriptions));

        $this->assertEquals($subscriptions, $collection->getApiUserSubscriptions(self::API_USER_ID));
    }

    public function testClearFilters()
    {
        $collection = $this->_makeCollectionMock(array('load'));
        // Cannot test number of calls because other tests use this member
        $this->_selectMock->expects($this->any())
            ->method('from')
            ->with(array('main_table' => null));
        $this->assertInstanceOf('Mage_Webhook_Model_Resource_Subscription_Collection', $collection->clearFilters());
    }

    public function testAddEndpointIdsFilter()
    {
        $collection = $this->_makeCollectionMock(array('load'));
        $endpointIds = array('endpoint_id_1','endpoint_id_2','endpoint_id_3');
        $this->_selectMock->expects($this->once())
            ->method('where')
            ->with('endpoint_id IN (?)', $endpointIds);
        $this->assertInstanceOf('Mage_Webhook_Model_Resource_Subscription_Collection',
            $collection->addEndpointIdsFilter($endpointIds));
    }

    public function testAddTopicFilter()
    {
        $this->_connectionMock->expects($this->once())
            ->method('quoteInto')
            ->with('hooks.subscription_id=main_table.subscription_id AND hooks.topic=?', self::TOPIC);
        $collection = $this->_makeCollectionMock(array('load'));
        $this->assertInstanceOf('Mage_Webhook_Model_Resource_Subscription_Collection',
            $collection->addTopicFilter(self::TOPIC));
    }

    public function testAddAliasFilter()
    {
        $collection = $this->_makeCollectionMock(array('addFieldToFilter'));
        $collection->expects($this->once())
            ->method('addFieldToFilter')
            ->with('alias', self::ALIAS);

        $this->assertInstanceOf('Mage_Webhook_Model_Resource_Subscription_Collection',
            $collection->addAliasFilter(self::ALIAS));
    }

    public function testAddIsActiveFilter()
    {
        $collection = $this->_makeCollectionMock(array('addFieldToFilter'));
        $collection->expects($this->once())
            ->method('addFieldToFilter')
            ->with('status', Magento_PubSub_SubscriptionInterface::STATUS_ACTIVE);
        $this->assertInstanceOf('Mage_Webhook_Model_Resource_Subscription_Collection',
            $collection->addIsActiveFilter(true));
    }

    public function testAddIsActiveFilterNotActive()
    {
        $collection = $this->_makeCollectionMock(array('addFieldToFilter'));
        $collection->expects($this->once())
            ->method('addFieldToFilter')
            ->with('status', Magento_PubSub_SubscriptionInterface::STATUS_INACTIVE);
        $this->assertInstanceOf('Mage_Webhook_Model_Resource_Subscription_Collection',
            $collection->addIsActiveFilter(false));
    }

    public function testAddNotInactiveFilter()
    {
        $collection = $this->_makeCollectionMock(array('load'));
        $this->_selectMock->expects($this->any())
            ->method('where')
            ->with('status IN (?)', array(
                Mage_Webhook_Model_Subscription::STATUS_ACTIVE,
                Mage_Webhook_Model_Subscription::STATUS_REVOKED));
        $this->assertInstanceOf('Mage_Webhook_Model_Resource_Subscription_Collection',
            $collection->addNotInactiveFilter());
    }

    /**
     * Generations a collection mock, with the given methods stubbed
     *
     * @param array $methods
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    private function _makeCollectionMock(array $methods)
    {
        return $this->getMock('Mage_Webhook_Model_Resource_Subscription_Collection',
            $methods, array($this->_fetchStrategyMock, $this->_endpointResMock, $this->_resourceMock), '', true);
    }

    /**
     * Generates a mock object of the given class
     *
     * @param string $className
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    private function _makeMock($className)
    {
        return $this->getMockBuilder($className)
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * Makes sure that Mage has a mock object manager set, and returns that instance.
     *
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    protected function _setMageObjectManager()
    {
        Mage::reset();
        $mockObjectManager = $this->getMockBuilder('Magento_ObjectManager')
            ->disableOriginalConstructor()
            ->getMock();
        Mage::setObjectManager($mockObjectManager);

        return $mockObjectManager;
    }
}
