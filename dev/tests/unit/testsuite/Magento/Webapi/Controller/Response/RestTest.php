<?php
/**
 * Test Rest response controller.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Webapi_Controller_Response_RestTest extends PHPUnit_Framework_TestCase
{
    /** @var \Magento\Webapi\Controller\Response\Rest */
    protected $_responseRest;

    /** @var \Magento\Core\Model\App */
    protected $_appMock;

    /** @var \Magento\Webapi\Controller\Response\Rest\Renderer\Xml */
    protected $_rendererMock;

    /** @var \Magento\Webapi\Controller\Dispatcher\ErrorProcessor */
    protected $_errorProcessorMock;

    protected function setUp()
    {
        /** Mock all objects required for SUT. */
        $this->_rendererMock = $this->getMockBuilder('Magento\Webapi\Controller\Response\Rest\Renderer\Json')
            ->disableOriginalConstructor()->getMock();
        $rendererFactoryMock = $this->getMockBuilder('Magento\Webapi\Controller\Response\Rest\Renderer\Factory')
            ->disableOriginalConstructor()->getMock();
        $rendererFactoryMock->expects($this->any())->method('get')->will($this->returnValue($this->_rendererMock));
        $this->_errorProcessorMock = $this->getMockBuilder('Magento\Webapi\Controller\Dispatcher\ErrorProcessor')
            ->disableOriginalConstructor()->getMock();
        $this->_errorProcessorMock->expects($this->once())->method('maskException')->will($this->returnArgument(0));
        $this->_appMock = $this->getMockBuilder('Magento\Core\Model\App')->disableOriginalConstructor()->getMock();

        /** Init SUP. */
        $this->_responseRest = new \Magento\Webapi\Controller\Response\Rest(
            $rendererFactoryMock,
            $this->_errorProcessorMock,
            $this->_appMock
        );
        $this->_responseRest->headersSentThrowsException = false;
        parent::setUp();
    }

    protected function tearDown()
    {
        unset($this->_responseRest);
        unset($this->_appMock);
        unset($this->_rendererMock);
        unset($this->_errorProcessorMock);
        parent::tearDown();
    }

    /**
     * Test setException method with \Magento\Webapi\Exception.
     */
    public function testSetWebapiExceptionException()
    {
        /** Init \Magento\Webapi\Exception */
        $apiException = new \Magento\Webapi\Exception('Exception message.', 401);
        $this->_responseRest->setException($apiException);
        /** Assert that \Magento\Webapi\Exception was set and presented in the list. */
        $this->assertTrue(
            $this->_responseRest->hasExceptionOfType('\Magento\Webapi\Exception'),
            '\Magento\Webapi\Exception was not set.'
        );
    }

    /**
     * Test sendResponse method with internal error exception during messages rendering.
     */
    public function testSendResponseRenderMessagesException()
    {
        /** Init logic exception. */
        $logicException = new LogicException();
        /** Mock renderer to throw LogicException in getMimeType method. */
        $this->_rendererMock->expects($this->any())->method('getMimeType')->will(
            $this->throwException($logicException)
        );
        /** Assert that renderException method will be executed once with specified parameters. */
        $this->_errorProcessorMock->expects($this->once())->method('renderException')->with(
            $logicException,
            \Magento\Webapi\Exception::HTTP_INTERNAL_ERROR
        );
        /** Set exception to Rest response to get in to the _renderMessages method. */
        $this->_responseRest->setException(new \Magento\Webapi\Exception('Message.', 400));
        $this->_responseRest->sendResponse();
    }

    /**
     * Test sendResponse method with HTTP Not Acceptable error exception during messages rendering.
     */
    public function testSendResponseRenderMessagesHttpNotAcceptable()
    {
        /** Init logic exception. */
        $logicException = new LogicException('Message', \Magento\Webapi\Exception::HTTP_NOT_ACCEPTABLE);
        /** Mock renderer to throw LogicException in getMimeType method. */
        $this->_rendererMock->expects($this->any())->method('getMimeType')->will(
            $this->throwException($logicException)
        );
        /** Assert that renderException method will be executed once with specified parameters. */
        $this->_errorProcessorMock->expects($this->once())->method('renderException')->with(
            $logicException,
            \Magento\Webapi\Exception::HTTP_NOT_ACCEPTABLE
        );
        /** Set exception to Rest response to get in to the _renderMessages method. */
        $this->_responseRest->setException(new \Magento\Webapi\Exception('Message.', 400));
        $this->_responseRest->sendResponse();
    }

    /**
     * Test sendResponse method with exception rendering.
     *
     * @dataProvider dataProviderForSendResponseWithException
     */
    public function testSendResponseWithException($exception, $expectedResult, $assertMessage)
    {
        /** Mock all required objects. */
        $this->_rendererMock->expects($this->any())->method('getMimeType')->will(
            $this->returnValue('application/json')
        );
        $this->_rendererMock->expects($this->any())->method('render')->will(
            $this->returnCallback(array($this, 'callbackForSendResponseTest'), $this->returnArgument(0))
        );
        $this->_responseRest->setException($exception);
        /** Start output buffering. */
        ob_start();
        $this->_responseRest->sendResponse();
        /** Clear output buffering. */
        ob_end_clean();
        $actualResponse = $this->_responseRest->getBody();
        $this->assertEquals($expectedResult, $actualResponse, $assertMessage);
    }

    /**
     * Callback for testSendResponseRenderMessages method.
     *
     * @param $data
     * @return string
     */
    public function callbackForSendResponseTest($data)
    {
        return json_encode($data);
    }

    /**
     * Test sendResponse method with exception rendering.
     *
     * @dataProvider dataProviderForSendResponseWithExceptionInDeveloperMode
     */
    public function testSendResponseWithExceptionInDeveloperMode($exception, $expectedResult, $assertMessage)
    {
        /** Mock all required objects. */
        $this->_rendererMock->expects($this->any())->method('getMimeType')->will(
            $this->returnValue('application/json')
        );
        $this->_rendererMock->expects($this->any())->method('render')->will(
            $this->returnCallback(array($this, 'callbackForSendResponseTest'), $this->returnArgument(0))
        );
        $this->_appMock->expects($this->any())->method('isDeveloperMode')->will($this->returnValue(true));
        $this->_responseRest->setException($exception);
        /** Start output buffering. */
        ob_start();
        $this->_responseRest->sendResponse();
        /** Clear output buffering. */
        ob_end_clean();
        $actualResponse = $this->_responseRest->getBody();
        $this->assertStringStartsWith($expectedResult, $actualResponse, $assertMessage);
    }

    /**
     * Data provider for testSendResponseWithException.
     *
     * @return array
     */
    public function dataProviderForSendResponseWithException()
    {
        return array(
            '\Magento\Webapi\Exception' => array(
                new \Magento\Webapi\Exception('Message', 400),
                '{"messages":{"error":[{"code":400,"message":"Message"}]}}',
                'Response sending with \Magento\Webapi\Exception is invalid'
            ),
            'Logical Exception' => array(
                new LogicException('Message', 100),
                '{"messages":{"error":[{"code":500,"message":"Message"}]}}',
                'Response sending with Logical Exception is invalid'
            ),
        );
    }

    /**
     * Data provider for testSendResponseWithExceptionInDeveloperMode.
     *
     * @return array
     */
    public function dataProviderForSendResponseWithExceptionInDeveloperMode()
    {
        return array(
            '\Magento\Webapi\Exception' => array(
                new \Magento\Webapi\Exception('Message', 400),
                '{"messages":{"error":[{"code":400,"message":"Message","trace":"',
                'Response sending with \Magento\Webapi\Exception in developer mode is invalid'
            ),
            'Logical Exception' => array(
                new LogicException('Message'),
                '{"messages":{"error":[{"code":500,"message":"Message","trace":"',
                'Response sending with Logical Exception in developer mode is invalid'
            ),
        );
    }
}
