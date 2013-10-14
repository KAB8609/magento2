<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Adminhtml;

class DashboardTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\App\RequestInterface|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_request;

    protected function setUp()
    {
        $objectManagerHelper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $helperMock = $this->getMockBuilder('Magento\Backend\Helper\DataProxy')
            ->disableOriginalConstructor()
            ->getMock();
        /** @var $request \Magento\App\RequestInterface|PHPUnit_Framework_MockObject_MockObject */
        $this->_request = $objectManagerHelper->getObject('Magento\App\RequestInterface',
            array('helper' => $helperMock));
    }

    protected function tearDown()
    {
        $this->_request = null;
    }

    public function testTunnelAction()
    {
        $fixture = uniqid();
        $this->_request->setParam('ga', urlencode(base64_encode(json_encode(array(1)))));
        $this->_request->setParam('h', $fixture);

        $tunnelResponse = new \Zend_Http_Response(200, array('Content-Type' => 'test_header'), 'success_msg');
        $httpClient = $this->getMock('Magento\HTTP\ZendClient', array('request'));
        $httpClient->expects($this->once())->method('request')->will($this->returnValue($tunnelResponse));
        /** @var $helper \Magento\Adminhtml\Helper\Dashboard\Data|PHPUnit_Framework_MockObject_MockObject */
        $helper = $this->getMock('Magento\Adminhtml\Helper\Dashboard\Data',
            array('getChartDataHash'), array(), '', false, false
        );
        $helper->expects($this->any())->method('getChartDataHash')->will($this->returnValue($fixture));

        $objectManager = $this->getMock('Magento\ObjectManager');
        $objectManager->expects($this->at(0))
            ->method('get')
            ->with('Magento\Adminhtml\Helper\Dashboard\Data')
            ->will($this->returnValue($helper));
        $objectManager->expects($this->at(1))
            ->method('create')
            ->with('Magento\HTTP\ZendClient')
            ->will($this->returnValue($httpClient));

        $controller = $this->_factory($this->_request, null, $objectManager);
        $controller->tunnelAction();
        $this->assertEquals('success_msg', $controller->getResponse()->getBody());
    }

    public function testTunnelAction400()
    {
        $controller = $this->_factory($this->_request);
        $controller->tunnelAction();
        $this->assertEquals(400, $controller->getResponse()->getHttpResponseCode());
    }

    public function testTunnelAction503()
    {
        $fixture = uniqid();
        $this->_request->setParam('ga', urlencode(base64_encode(json_encode(array(1)))));
        $this->_request->setParam('h', $fixture);

        /** @var $helper \Magento\Adminhtml\Helper\Dashboard\Data|PHPUnit_Framework_MockObject_MockObject */
        $helper = $this->getMock('Magento\Adminhtml\Helper\Dashboard\Data',
            array('getChartDataHash'), array(), '', false, false
        );
        $helper->expects($this->any())->method('getChartDataHash')->will($this->returnValue($fixture));

        $objectManager = $this->getMock('Magento\ObjectManager');
        $objectManager->expects($this->at(0))
            ->method('get')
            ->with('Magento\Adminhtml\Helper\Dashboard\Data')
            ->will($this->returnValue($helper));
        $exceptionMock = new \Exception();
        $objectManager->expects($this->at(1))
            ->method('create')
            ->with('Magento\HTTP\ZendClient')
            ->will($this->throwException($exceptionMock));
        $loggerMock = $this->getMock('Magento\Core\Model\Logger', array('logException'), array(), '', false);
        $loggerMock->expects($this->once())->method('logException')->with($exceptionMock);
        $objectManager->expects($this->at(2))
            ->method('get')
            ->with('Magento\Core\Model\Logger')
            ->will($this->returnValue($loggerMock));

        $controller = $this->_factory($this->_request, null, $objectManager);
        $controller->tunnelAction();
        $this->assertEquals(503, $controller->getResponse()->getHttpResponseCode());
    }

    /**
     * Create the tested object
     *
     * @param \Magento\App\RequestInterface $request
     * @param \Magento\App\Response\Http|null $response
     * @param \Magento\ObjectManager|null $objectManager
     * @return \Magento\Adminhtml\Controller\Dashboard|PHPUnit_Framework_MockObject_MockObject
     */
    protected function _factory($request, $response = null, $objectManager = null)
    {
        if (!$response) {
            $eventManager = $this->getMock('Magento\Event\ManagerInterface', array(), array(), '', false);
            /** @var $response \Magento\App\ResponseInterface|PHPUnit_Framework_MockObject_MockObject */
            $response = $this->getMock('Magento\App\ResponseInterface');
            $response->headersSentThrowsException = false;
        }
        if (!$objectManager) {
            $objectManager = new \Magento\ObjectManager\ObjectManager();
        }
        $rewriteFactory = $this->getMock('Magento\Core\Model\Url\RewriteFactory', array('create'), array(), '', false);
        $helper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $varienFront = $helper->getObject('Magento\App\FrontController',
            array('rewriteFactory' => $rewriteFactory)
        );

        $arguments = array(
            'request' => $request,
            'response' => $response,
            'objectManager' => $objectManager,
            'frontController' => $varienFront,
        );
        $context = $helper->getObject('Magento\Backend\Controller\Context', $arguments);
        return new \Magento\Adminhtml\Controller\Dashboard($context);
    }
}

