<?php
/**
 * Mage_Webhook_Model_Observer
 *
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Webhook
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Webhook_Model_ObserverTest extends PHPUnit_Framework_TestCase
{
    /** @var PHPUnit_Framework_MockObject_MockObject|Mage_Webhook_Model_Observer */
    protected $_observer;

    /** @var PHPUnit_Framework_MockObject_MockObject */
    private $_webapiEventHandler;

    /** @var PHPUnit_Framework_MockObject_MockObject */
    private $_subscriptionSet;

    /** @var PHPUnit_Framework_MockObject_MockObject */
    private $_logger;

    public function setUp()
    {
        $this->_webapiEventHandler = $this->_getBasicMock('Mage_Webhook_Model_Webapi_EventHandler');
        $this->_subscriptionSet = $this->_getBasicMock('Mage_Webhook_Model_Resource_Subscription_Collection');
        $this->_logger = $this->_getBasicMock('Magento_Core_Model_Logger');

        $this->_observer = new Mage_Webhook_Model_Observer(
            $this->_webapiEventHandler,
            $this->_subscriptionSet,
            $this->_logger
        );
    }

    /**
     * @param string $className
     *
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    protected function _getBasicMock($className)
    {
        return $this->getMockBuilder($className)
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function testAfterWebapiUserDeleteSuccess()
    {

        $mockSubscription = $this->getMockBuilder('Mage_Webhook_Model_Subscription')
            ->disableOriginalConstructor()
            ->setMethods(array('setStatus', 'save'))
            ->getMock();

        $this->_subscriptionSet->expects($this->once())
            ->method('getActivatedSubscriptionsWithoutApiUser')
            ->withAnyParameters()
            ->will($this->returnValue(array($mockSubscription)));

        $mockSubscription->expects($this->once())
            ->method('setStatus')
            ->with($this->equalTo(Mage_Webhook_Model_Subscription::STATUS_INACTIVE))
            ->will($this->returnSelf());

        $mockSubscription->expects($this->once())
            ->method('save');

        $this->_logger->expects($this->never())
            ->method('logException');

        $this->_observer->afterWebapiUserDelete();
    }

    public function testAfterWebapiUserDeleteWithException()
    {

        $mockSubscription = $this->getMockBuilder('Mage_Webhook_Model_Subscription')
            ->disableOriginalConstructor()
            ->setMethods(array('setStatus', 'save'))
            ->getMock();

        $this->_subscriptionSet->expects($this->once())
            ->method('getActivatedSubscriptionsWithoutApiUser')
            ->withAnyParameters()
            ->will($this->returnValue(array($mockSubscription)));

        $mockSubscription->expects($this->once())
            ->method('setStatus')
            ->with($this->equalTo(Mage_Webhook_Model_Subscription::STATUS_INACTIVE))
            ->will($this->returnSelf());

        $exception = new Exception('exception');
        $mockSubscription->expects($this->once())
            ->method('save')
            ->withAnyParameters()
            ->will($this->throwException($exception));

        $this->_logger->expects($this->once())
            ->method('logException')
            ->with($this->equalTo($exception));

        $this->_observer->afterWebapiUserDelete();
    }

    public function testAfterWebapiUserChange()
    {
        $mockObserver = $this->_getBasicMock('Magento_Event_Observer');
        $mockVarienEvent = $this->getMockBuilder('Magento_Event')
            ->setMethods(array('getObject'))
            ->disableOriginalConstructor()
            ->getMock();

        $mockObserver->expects($this->once())
            ->method('getEvent')
            ->withAnyParameters()
            ->will($this->returnValue($mockVarienEvent));

        $model = 'model';
        $mockVarienEvent->expects($this->once())
            ->method('getObject')
            ->withAnyParameters()
            ->will($this->returnValue($model));

        $this->_webapiEventHandler->expects($this->once())
            ->method('userChanged')
            ->with($this->equalTo($model));

        $this->_observer->afterWebapiUserChange($mockObserver);
    }

    public function testAfterWebapiUserChangeWithException()
    {
        $mockObserver = $this->_getBasicMock('Magento_Event_Observer');
        $mockVarienEvent = $this->getMockBuilder('Magento_Event')
            ->setMethods(array('getObject'))
            ->disableOriginalConstructor()
            ->getMock();

        $mockObserver->expects($this->once())
            ->method('getEvent')
            ->withAnyParameters()
            ->will($this->returnValue($mockVarienEvent));

        $exception = new Exception('exception');
        $this->_logger->expects($this->once())
            ->method('logException')
            ->with($this->equalTo($exception));

        $mockVarienEvent->expects($this->once())
            ->method('getObject')
            ->withAnyParameters()
            ->will($this->throwException($exception));

        $this->_observer->afterWebapiUserChange($mockObserver);
    }

    public function testAfterWebapiRoleChange()
    {
        $mockObserver = $this->_getBasicMock('Magento_Event_Observer');
        $mockVarienEvent = $this->getMockBuilder('Magento_Event')
            ->setMethods(array('getObject'))
            ->disableOriginalConstructor()
            ->getMock();

        $mockObserver->expects($this->once())
            ->method('getEvent')
            ->withAnyParameters()
            ->will($this->returnValue($mockVarienEvent));

        $model = 'model';
        $mockVarienEvent->expects($this->once())
            ->method('getObject')
            ->withAnyParameters()
            ->will($this->returnValue($model));

        $this->_webapiEventHandler->expects($this->once())
            ->method('roleChanged')
            ->with($this->equalTo($model));

        $this->_observer->afterWebapiRoleChange($mockObserver);
    }

    public function testAfterWebapiRoleChangeWithException()
    {
        $mockObserver = $this->_getBasicMock('Magento_Event_Observer');
        $mockVarienEvent = $this->getMockBuilder('Magento_Event')
            ->setMethods(array('getObject'))
            ->disableOriginalConstructor()
            ->getMock();

        $mockObserver->expects($this->once())
            ->method('getEvent')
            ->withAnyParameters()
            ->will($this->returnValue($mockVarienEvent));

        $exception = new Exception('exception');
        $this->_logger->expects($this->once())
            ->method('logException')
            ->with($this->equalTo($exception));

        $mockVarienEvent->expects($this->once())
            ->method('getObject')
            ->withAnyParameters()
            ->will($this->throwException($exception));

        $this->_observer->afterWebapiRoleChange($mockObserver);
    }
}
