<?php
/**
 * Magento_PubSub_Job_QueueHandler
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_PubSub_Job_QueueHandlerTest extends PHPUnit_Framework_TestCase
{
    /** @var  Magento_PubSub_Job_QueueHandler */
    private $_queueHandler;

    /** @var  PHPUnit_Framework_MockObject_MockObject */
    private $_subscriptionMockA;

    /** @var  PHPUnit_Framework_MockObject_MockObject */
    private $_subscriptionMockB;

    /** @var  PHPUnit_Framework_MockObject_MockObject */
    private $_eventMockA;

    /** @var  PHPUnit_Framework_MockObject_MockObject */
    private $_eventMockB;

    /** @var  PHPUnit_Framework_MockObject_MockObject */
    private $_queueReaderMock;

    /** @var  PHPUnit_Framework_MockObject_MockObject */
    private $_queueWriterMock;

    /** @var  PHPUnit_Framework_MockObject_MockObject */
    private $_messageMockA;

    /** @var  PHPUnit_Framework_MockObject_MockObject */
    private $_messageMockB;

    /** @var  PHPUnit_Framework_MockObject_MockObject */
    private $_msgFactoryMock;

    /** @var  PHPUnit_Framework_MockObject_MockObject */
    private $_transportMock;

    /** @var  PHPUnit_Framework_MockObject_MockObject */
    private $_endpointMockA;

    /** @var  PHPUnit_Framework_MockObject_MockObject */
    private $_endpointMockB;

    public function setUp()
    {
        // Object mocks
        $this->_subscriptionMockA = $this->_makeMock('Mage_Webhook_Model_Subscription');
        $this->_subscriptionMockB =  $this->_makeMock('Mage_Webhook_Model_Subscription');
        $this->_eventMockA = $this->_makeMock('Mage_Webhook_Model_Event');
        $this->_eventMockB = $this->_makeMock('Mage_Webhook_Model_Event');
        $this->_msgFactoryMock = $this->_makeMock('Magento_Outbound_Message_Factory');
        $this->_transportMock = $this->_makeMock('Magento_Outbound_Transport_Http');
        $this->_queueReaderMock = $this->_makeMock('Mage_Webhook_Model_Job_QueueReader');
        $this->_queueWriterMock = $this->_makeMock('Mage_Webhook_Model_Job_QueueWriter');
        $this->_messageMockA = $this->_makeMock('Magento_Outbound_Message');
        $this->_messageMockB = $this->_makeMock('Magento_Outbound_Message');
        $this->_endpointMockA = $this->_makeMock('Magento_Outbound_EndpointInterface');
        $this->_endpointMockB = $this->_makeMock('Magento_Outbound_EndpointInterface');

        $this->_subscriptionMockA->expects($this->any())
            ->method('getEndpoint')
            ->will($this->returnValue($this->_endpointMockA));

        $this->_subscriptionMockB->expects($this->any())
            ->method('getEndpoint')
            ->will($this->returnValue($this->_endpointMockB));

    }

    public function testHandle()
    {
        // Resources for stubs
        $jobMsgMap = array(
            array($this->_endpointMockA, $this->_eventMockA, $this->_messageMockA),
            array($this->_endpointMockB, $this->_eventMockB, $this->_messageMockB),
        );

        $responseA = $this->_makeMock('Magento_Outbound_Transport_Http_Response');
        $responseB = $this->_makeMock('Magento_Outbound_Transport_Http_Response');

        $responseA->expects($this->once())
            ->method('isSuccessful')
            ->will($this->returnValue(true));

        $responseB->expects($this->once())
            ->method('isSuccessful')
            ->will($this->returnValue(false));

        $msgResponseMap = array(
            array($this->_messageMockA, $responseA),
            array($this->_messageMockB, $responseB),
        );

        // Message factory create
        $this->_msgFactoryMock->expects($this->exactly(2))
            ->method('create')
            ->will($this->returnValueMap($jobMsgMap));

        // Transport dispatch
        $this->_transportMock->expects($this->exactly(2))
            ->method('dispatch')
            ->will($this->returnValueMap($msgResponseMap));

        // Job stubs
        $jobMockA = $this->_makeMock('Mage_Webhook_Model_Job');
        $jobMockB = $this->_makeMock('Mage_Webhook_Model_Job');

        $jobMockA->expects($this->once())
            ->method('complete');

        $jobMockB->expects($this->once())
            ->method('handleFailure');

        $jobMockA->expects($this->once())
            ->method('getSubscription')
            ->with()
            ->will($this->returnValue($this->_subscriptionMockA));

        $jobMockB->expects($this->once())
            ->method('getSubscription')
            ->with()
            ->will($this->returnValue($this->_subscriptionMockB));

        $jobMockA->expects($this->once())
            ->method('getEvent')
            ->with()
            ->will($this->returnValue($this->_eventMockA));

        $jobMockB->expects($this->once())
            ->method('getEvent')
            ->with()
            ->will($this->returnValue($this->_eventMockB));

        // Queue contains two jobs, and will then return null to stop the loop
        $this->_queueReaderMock->expects($this->exactly(3))
            ->method('poll')
            ->with()
            ->will($this->onConsecutiveCalls(
                $jobMockA,
                $jobMockB,
                null
            ));

        $this->_queueHandler = new Magento_PubSub_Job_QueueHandler(
            $this->_queueReaderMock,
            $this->_queueWriterMock,
            $this->_transportMock,
            $this->_msgFactoryMock
        );

        $this->_queueHandler->handle();
    }

    public function testHandleEmptyQueue()
    {
        $this->_expectedCodes = array ();

        // Queue contains no jobs
        $this->_queueReaderMock->expects($this->once())
            ->method('poll')
            ->with()
            ->will($this->onConsecutiveCalls(
                null
            ));

        // Message factory create should never  be called
        $this->_msgFactoryMock->expects($this->never())
            ->method('create');

        // Transport dispatch should never be called
        $this->_transportMock->expects($this->never())
            ->method('dispatch');

        $this->_queueHandler = new Magento_PubSub_Job_QueueHandler(
            $this->_queueReaderMock,
            $this->_queueWriterMock,
            $this->_transportMock,
            $this->_msgFactoryMock
        );

        $this->_queueHandler->handle();
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
}