<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\App;

class ActionFlagTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\App\ActionFlag
     */
    protected $_actionFlag;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_requestMock;

    protected function setUp()
    {

        $this->_requestMock = $this->getMock('Magento\App\Request\Http', array(), array(), '', false);
        $this->_actionFlag = new \Magento\App\ActionFlag($this->_requestMock);
    }

    public function testSetIfActionNotExist()
    {
        $this->_requestMock->expects($this->once())->method('getActionName')->will($this->returnValue('action_name'));
        $this->_requestMock->expects($this->once())->method('getRequestedRouteName');
        $this->_requestMock->expects($this->once())->method('getRequestedControllerName');
        $this->_actionFlag->set('', 'flag', 'value');
    }

    public function testSetIfActionExist()
    {
        $this->_requestMock->expects($this->never())->method('getActionName');
        $this->_requestMock->expects($this->once())->method('getRequestedRouteName');
        $this->_requestMock->expects($this->once())->method('getRequestedControllerName');
        $this->_actionFlag->set('action', 'flag', 'value');
    }

    public function testGetIfFlagNotExist()
    {
        $this->_requestMock->expects($this->once())->method('getActionName')->will($this->returnValue('action_name'));
        $this->_requestMock->expects($this->once())->method('getRequestedRouteName');
        $this->_requestMock->expects($this->once())->method('getRequestedControllerName');
        $this->assertEquals(array(), $this->_actionFlag->get(''));
    }

    public function testGetIfFlagExist()
    {
        $this->_requestMock->expects($this->never())->method('getActionName');
        $this->_requestMock->expects($this->exactly(3))
            ->method('getRequestedRouteName')->will($this->returnValue('route'));
        $this->_requestMock->expects($this->exactly(3))
            ->method('getRequestedControllerName')->will($this->returnValue('controller'));
        $this->_actionFlag->set('action', 'flag', 'value');
        $this->assertEquals('value', $this->_actionFlag->get('action', 'flag'));
    }
    public function testGetIfFlagWithControllerKryNotExist()
    {
        $this->_requestMock->expects($this->never())->method('getActionName');
        $this->_requestMock->expects($this->once())
            ->method('getRequestedRouteName')->will($this->returnValue('route'));
        $this->_requestMock->expects($this->once())
            ->method('getRequestedControllerName')->will($this->returnValue('controller'));
        $this->assertEquals(false, $this->_actionFlag->get('action', 'flag'));
    }
}