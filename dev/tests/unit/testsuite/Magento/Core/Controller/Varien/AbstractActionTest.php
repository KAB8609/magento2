<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Core\Controller\Varien;

/**
 * Test class \Magento\Core\Controller\Varien\AbstractAction
 */
class AbstractActionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\App\Action\AbstractAction
     */
    protected $_actionAbstract;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_request;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_response;

    /**
     * Setup before tests
     *
     * Create request, response and forward action (child of AbstractAction)
     */
    protected function setUp()
    {
        $this->_request = $this->getMock('Magento\App\Request\Http', array(), array(), '', false);
        $this->_response = $this->getMock('Magento\App\Response\Http', array(), array(), '', false);
        $this->_response->headersSentThrowsException = false;
        $this->_actionAbstract = new \Magento\App\Action\Forward($this->_request, $this->_response);
    }

    /**
     * Test for getRequest method
     *
     * @test
     * @covers \Magento\App\Action\AbstractAction::getRequest
     */
    public function testGetRequest()
    {
        $this->assertEquals($this->_request, $this->_actionAbstract->getRequest());
    }

    /**
     * Test for getResponse method
     *
     * @test
     * @covers \Magento\App\Action\AbstractAction::getResponse
     */
    public function testGetResponse()
    {
        $this->assertEquals($this->_response, $this->_actionAbstract->getResponse());
    }

    /**
     * Test for getResponse med. Checks that response headers are set correctly
     *
     * @test
     * @covers \Magento\App\Action\AbstractAction::getResponse
     */
    public function testResponseHeaders()
    {
        $routerListMock = $this->getMock('Magento\App\Route\ConfigInterface');
        $infoProcessorMock = $this->getMock('Magento\App\Request\PathInfoProcessorInterface');
        $infoProcessorMock->expects($this->any())->method('process')->will($this->returnArgument(1));
        $request = new \Magento\App\Request\Http($routerListMock, $infoProcessorMock);
        $response = new \Magento\App\Response\Http();
        $response->headersSentThrowsException = false;
        $action = new \Magento\App\Action\Forward($request, $response);

        $headers = array(
            array(
                'name' => 'X-Frame-Options',
                'value' => 'SAMEORIGIN',
                'replace' => false
            )
        );

        $this->assertEquals($headers, $action->getResponse()->getHeaders());
    }
}
