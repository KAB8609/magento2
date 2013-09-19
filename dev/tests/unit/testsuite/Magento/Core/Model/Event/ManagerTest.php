<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\Model\Event;

class ManagerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Core\Model\Event\InvokerInterface|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_invoker;

    /**
     * @var \Magento\EventFactory|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_eventFactory;

    /**
     * @var \Magento\Event|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_event;

    /**
     * @var Magento_Event_ObserverFactory|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_observerFactory;

    /**
     * @var \Magento\Event\Observer|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_observer;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_eventConfigMock;

    /**
     * @var \Magento\Core\Model\Event\Manager
     */
    protected $_eventManager;

    protected function setUp()
    {
        $this->_invoker = $this->getMock('Magento\Core\Model\Event\InvokerInterface');
        $this->_eventFactory = $this->getMock('Magento\EventFactory', array('create'), array(), '', false);
        $this->_event = $this->getMock('Magento\Event', array(), array(), '', false);
        $this->_observerFactory = $this->getMock('Magento\Event\ObserverFactory', array('create'), array(), '',
            false);
        $this->_observer = $this->getMock('Magento\Event\Observer', array(), array(), '', false);
        $this->_eventConfigMock = $this->getMock('Magento\Core\Model\Event\ConfigInterface');

        $this->_eventManager = new \Magento\Core\Model\Event\Manager(
            $this->_invoker, $this->_eventConfigMock, $this->_eventFactory, $this->_observerFactory
        );
    }

    public function testDispatch()
    {
        $data = array('123');

        $this->_event->expects($this->once())->method('setName')->with('some_event')->will($this->returnSelf());
        $this->_eventFactory->expects($this->once())->method('create')->with(array('data' => $data))
            ->will($this->returnValue($this->_event));

        $this->_observer->expects($this->once())->method('setData')
            ->with(array_merge(array('event' => $this->_event), $data))->will($this->returnSelf());
        $this->_observerFactory->expects($this->once())->method('create')
            ->will($this->returnValue($this->_observer));
        $this->_invoker->expects($this->once())->method('dispatch')->with(array(
            'instance' => 'class',
            'method' => 'method',
            'name' => 'observer'
        ), $this->_observer);

        $this->_eventConfigMock->expects($this->once())
            ->method('getObservers')
            ->with('some_event')
            ->will($this->returnValue(array(
                'observer' => array('instance' => 'class', 'method' => 'method', 'name' => 'observer')
            )));
        $this->_eventManager->dispatch('some_event', array('123'));
    }

    public function testDispatchWithEmptyEventObservers()
    {
        $this->_eventConfigMock->expects($this->once())
            ->method('getObservers')
            ->with('some_event')
            ->will($this->returnValue(array()));
        $this->_invoker->expects($this->never())->method('dispatch');
        $this->_eventManager->dispatch('some_event');
    }
}
