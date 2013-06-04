<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Enterprise_CustomerSegment_Model_LoggingTest extends PHPUnit_Framework_TestCase
{
    /**
     * @param int $qty
     * @param int|null $customerSegmentId
     * @param string $expectedText
     * @dataProvider postDispatchCustomerSegmentMatchDataProvider
     */
    public function testPostDispatchCustomerSegmentMatch($qty, $customerSegmentId, $expectedText)
    {
        $requestMock = $this->getMock('Mage_Core_Controller_Request_Http', array(), array(), '', false);
        $requestMock->expects($this->atLeastOnce())
            ->method('getParam')
            ->with('id')
            ->will($this->returnValue($customerSegmentId));
        $resourceMock = $this->getMock('Enterprise_CustomerSegment_Model_Resource_Segment',
            array(), array(), '', false);
        $resourceMock->expects($this->once())
            ->method('getSegmentCustomersQty')
            ->with($customerSegmentId)
            ->will($this->returnValue($qty));
        $helperMock = $this->getMock('Enterprise_CustomerSegment_Helper_Data', array(), array(), '', false);
        $helperMock->expects($this->any())
            ->method('__')
            ->with('Matched %d Customers of Segment %s', $qty, $customerSegmentId)
            ->will($this->returnValue($expectedText));

        $model = new Enterprise_CustomerSegment_Model_Logging($resourceMock, $requestMock, $helperMock);
        $config = new Varien_Simplexml_Element('<config/>');
        $eventMock = $this->getMock('Enterprise_Logging_Model_Event', array('setInfo'), array(), '', false);
        $eventMock->expects($this->once())
            ->method('setInfo')
            ->with($expectedText);

        $model->postDispatchCustomerSegmentMatch($config, $eventMock);
    }

    public function postDispatchCustomerSegmentMatchDataProvider()
    {
        return array(
            'specific segment' => array(10, 1, "Matched 10 Customers of Segment 1"),
            'no segment'       => array(10, null, '-'),
        );
    }
}
