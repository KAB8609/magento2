<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\App;

class CronTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\App\Cron
     */
    protected $_model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_eventManagerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_configScopeMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_stateMock;

    protected function setUp()
    {
        $this->_eventManagerMock = $this->getMock('Magento\Event\ManagerInterface');
        $this->_stateMock = $this->getMock('Magento\App\State', array(), array(), '', false);
        $this->_model = new Cron(
            $this->_eventManagerMock,
            $this->_stateMock
        );
    }

    public function testExecuteDispatchesCronEvent()
    {
        $this->_stateMock->expects($this->once())->method('setAreaCode')->with('crontab');
        $this->_eventManagerMock->expects($this->once())->method('dispatch')->with('default');
        $this->assertEquals(0, $this->_model->execute());
    }
}