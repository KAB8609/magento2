<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\App\FrontController\Plugin;

class DispatchExceptionHandlerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Core\App\FrontController\Plugin\DispatchExceptionHandler
     */
    protected $_model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_storeManagerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_dirMock;

    protected function setUp()
    {
        $this->_storeManagerMock = $this->getMock('\Magento\Core\Model\StoreManager', array(), array(), '', false);
        $this->_dirMock = $this->getMock('\Magento\App\Dir', array(), array(), '', false);
        $this->_model = new DispatchExceptionHandler(
            $this->_storeManagerMock,
            $this->_dirMock
        );
    }

    public function testAroundDispatch()
    {
        $invocationChainMock = $this->getMock('\Magento\Code\Plugin\InvocationChain', array(), array(), '', false);
        $arguments = array();
        $invocationChainMock->expects($this->once())->method('proceed')->with($arguments);
        $this->_model->aroundDispatch($arguments, $invocationChainMock);
    }
}