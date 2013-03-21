<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Core_Model_Event_InvokerDefaultTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_observerFactoryMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_observerMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_listenerMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_appStateMock;

    /**
     * @var Enterprise_Queue_Model_Core_Event_InvokerAsynchronous
     */
    protected $_invokerDefault;

    protected function setUp()
    {
        $this->_observerFactoryMock = $this->getMock('Mage_Core_Model_ObserverFactory', array(), array(), '', false);
        $this->_observerMock = $this->getMock('Varien_Event_Observer', array(), array(), '', false);
        $this->_listenerMock = $this->getMock('Mage_Some_Model_Observer_Some', array('method_name'), array(), '',
            false);
        $this->_appStateMock = $this->getMock('Mage_Core_Model_App_State', array(), array(), '', false);

        $objectManagerHelper = new Magento_Test_Helper_ObjectManager($this);
        $this->_invokerDefault = $objectManagerHelper->getObject('Mage_Core_Model_Event_InvokerDefault', array(
            'observerFactory' => $this->_observerFactoryMock,
            'appState' => $this->_appStateMock,
        ));
    }

    public function testDispatchWithDisabledType()
    {
        $this->_observerFactoryMock->expects($this->never())->method('get');
        $this->_observerFactoryMock->expects($this->never())->method('create');

        $this->_invokerDefault->dispatch(array('type' => 'disabled'), $this->_observerMock);
    }

    /**
     * @param string $type
     * @dataProvider dataProviderForDispatchWithNotSingletonType
     */
    public function testDispatchWithNotSingletonType($type)
    {
        $this->_listenerMock->expects($this->once())->method('method_name');
        $this->_observerFactoryMock->expects($this->never())->method('get');
        $this->_observerFactoryMock->expects($this->once())->method('create')->with('class_name')
            ->will($this->returnValue($this->_listenerMock));

        $this->_invokerDefault->dispatch(
            array('type' => $type, 'model' => 'class_name', 'method' => 'method_name'),
            $this->_observerMock
        );
    }

    /**
     * @return array
     */
    public function dataProviderForDispatchWithNotSingletonType()
    {
        return array(
            array('object'),
            array('model'),
        );
    }

    public function testDispatchWithSingletonType()
    {
        $this->_listenerMock->expects($this->once())->method('method_name');
        $this->_observerFactoryMock->expects($this->never())->method('create');
        $this->_observerFactoryMock->expects($this->once())->method('get')->with('class_name')
            ->will($this->returnValue($this->_listenerMock));

        $this->_invokerDefault->dispatch(
            array('type' => 'unknown', 'model' => 'class_name', 'method' => 'method_name'),
            $this->_observerMock
        );
    }

    /**
     * @param string $type
     * @dataProvider dataProviderForMethodIsNotDefined
     * @expectedException Mage_Core_Exception
     */
    public function testMethodIsNotDefinedExceptionWithEnabledDeveloperMode($type)
    {
        $this->_observerFactoryMock->expects($this->any())->method('create')->with('class_name')
            ->will($this->returnValue($this->_listenerMock));
        $this->_observerFactoryMock->expects($this->any())->method('get')->with('class_name')
            ->will($this->returnValue($this->_listenerMock));
        $this->_appStateMock->expects($this->once())->method('isDeveloperMode')->will($this->returnValue(true));

        $this->_invokerDefault->dispatch(
            array('type' => $type, 'model' => 'class_name', 'method' => 'unknown_method_name'),
            $this->_observerMock
        );
    }

    /**
     * @param string $type
     * @dataProvider dataProviderForMethodIsNotDefined
     */
    public function testMethodIsNotDefinedWithDisabledDeveloperMode($type)
    {
        $this->_observerFactoryMock->expects($this->any())->method('create')->with('class_name')
            ->will($this->returnValue($this->_listenerMock));
        $this->_observerFactoryMock->expects($this->any())->method('get')->with('class_name')
            ->will($this->returnValue($this->_listenerMock));
        $this->_appStateMock->expects($this->once())->method('isDeveloperMode')->will($this->returnValue(false));

        $this->_invokerDefault->dispatch(
            array('type' => $type, 'model' => 'class_name', 'method' => 'unknown_method_name'),
            $this->_observerMock
        );
    }

    /**
     * @return array
     */
    public function dataProviderForMethodIsNotDefined()
    {
        return array(
            array('object'),
            array('model'),
            array('unknown'),
        );
    }
}
