<?php
/**
 * Test Webapi Request model.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Webapi_Controller_Request_RestTest extends PHPUnit_Framework_TestCase
{
    /**
     * Request mock.
     *
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_request;

    /** @var PHPUnit_Framework_MockObject_MockObject */
    protected $_interpreterFactory;

    protected function setUp()
    {
        parent::setUp();
        /** Prepare mocks for request constructor arguments. */
        $this->_interpreterFactory = $this->getMockBuilder('Mage_Webapi_Controller_Request_Rest_Interpreter_Factory')
            ->setMethods(array('interpret', 'get'))
            ->disableOriginalConstructor()
            ->getMock();
        $helper = $this->getMockBuilder('Mage_Webapi_Helper_Data')
            ->disableOriginalConstructor()
            ->setMethods(array('__'))
            ->getMock();
        $helper->expects($this->any())->method('__')->will($this->returnArgument(0));
        $configMock = $this->getMockBuilder('Mage_Core_Model_Config')->disableOriginalConstructor()->getMock();
        /** Instantiate request. */
        // TODO: Get rid of SUT mocks.
        $this->_request = $this->getMock(
            'Mage_Webapi_Controller_Request_Rest',
            array('getHeader', 'getMethod', 'isGet', 'isPost', 'isPut', 'isDelete', 'getRawBody'),
            array($configMock, $this->_interpreterFactory, $helper)
        );
    }

    protected function tearDown()
    {
        unset($this->_request);
        parent::tearDown();
    }

    /**
     * Test for getAcceptTypes() method.
     *
     * @dataProvider providerAcceptType
     * @param string $acceptHeader Value of Accept HTTP header
     * @param array $expectedResult Method call result
     */
    public function testGetAcceptTypes($acceptHeader, $expectedResult)
    {
        $this->_request
            ->expects($this->once())
            ->method('getHeader')
            ->with('Accept')
            ->will($this->returnValue($acceptHeader));
        /** @var Mage_Webapi_Controller_Request_Rest _requestMock */
        $this->assertSame($expectedResult, $this->_request->getAcceptTypes());
    }

    /**
     * Test for getBodyParams() method.
     */
    public function testGetBodyParams()
    {
        $params = array('a' => 123, 'b' => 145);
        $this->_prepareSutForGetBodyParamsTest($params);
        $this->assertEquals(
            $params,
            $this->_request->getBodyParams(),
            'Body parameters were retrieved incorrectly.'
        );
    }

    /**
     * Prepare SUT for GetBodyParams() method mock.
     *
     * @param array $params
     */
    protected function _prepareSutForGetBodyParamsTest($params)
    {
        $rawBody = 'rawBody';
        $this->_request->expects($this->once())
            ->method('getRawBody')
            ->will($this->returnValue($rawBody));
        $contentType = 'contentType';
        $this->_request
            ->expects($this->once())
            ->method('getHeader')
            ->with('Content-Type')
            ->will($this->returnValue($contentType));
        $interpreter = $this->getMockBuilder('Mage_Webapi_Controller_Request_Rest_Interpreter_Json')
            ->disableOriginalConstructor()
            ->setMethods(array('interpret'))
            ->getMock();
        $interpreter->expects($this->once())
            ->method('interpret')
            ->with($rawBody)
            ->will($this->returnValue($params));
        $this->_interpreterFactory->expects($this->once())
            ->method('get')
            ->with($contentType)
            ->will($this->returnValue($interpreter));
    }

    /**
     * Test for getContentType() method.
     *
     * @dataProvider providerContentType
     * @param string $contentTypeHeader 'Content-Type' header value
     * @param string $contentType Appropriate content type for header value
     * @param string|boolean $exceptionMessage Exception message (boolean FALSE if exception is not expected)
     */
    public function testGetContentType($contentTypeHeader, $contentType, $exceptionMessage = false)
    {
        $this->_request
            ->expects($this->once())
            ->method('getHeader')
            ->with('Content-Type')
            ->will($this->returnValue($contentTypeHeader));

        try {
            $this->assertEquals($contentType, $this->_request->getContentType());
        } catch (Mage_Webapi_Exception $e) {
            if ($exceptionMessage) {
                $this->assertEquals(
                    $exceptionMessage,
                    $e->getMessage(),
                    'Exception message does not match the expected one.'
                );
                return;
            } else {
                $this->fail('Exception is thrown on valid header: ' . $e->getMessage());
            }
        }
        if ($exceptionMessage) {
            $this->fail('Expected exception was not raised.');
        }
    }

    /**
     * Test for getServiceType() method.
     *
     */
    public function testGetServiceType()
    {
        $this->assertNull($this->_request->getServiceType());
        $resource = 'test_service';
        $this->_request->setServiceType($resource);
        $this->assertEquals($resource, $this->_request->getServiceType());
    }

    /**
     * Data provider for testGetAcceptTypes().
     *
     * @return array
     */
    public function providerAcceptType()
    {
        return array(
            // Each element is: array(Accept HTTP header value, expected result))
            array('', array()),
            array(
                'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                array('text/html', 'application/xhtml+xml', 'application/xml', '*/*')
            ),
            array(
                'text/html, application/*, text, */*',
                array('text/html', 'application/*', 'text', '*/*')
            ),
            array(
                'text/html, application/xml;q=0.9, application/xhtml+xml, image/png, image/webp,'
                    . ' image/jpeg, image/gif, image/x-xbitmap, */*;q=0.1',
                array(
                    'text/html',
                    'application/xhtml+xml',
                    'image/png',
                    'image/webp',
                    'image/jpeg',
                    'image/gif',
                    'image/x-xbitmap',
                    'application/xml',
                    '*/*'
                )
            )
        );
    }

    /**
     * Data provider for testGetContentType().
     *
     * @return array
     */
    public function providerContentType()
    {
        return array(
            // Each element is: array(Content-Type header value, content-type part[, expected exception message])
            array('', null, 'Content-Type header is empty.'),
            array('_?', null, 'Content-Type header is invalid.'),
            array('application/x-www-form-urlencoded; charset=UTF-8', 'application/x-www-form-urlencoded'),
            array('application/x-www-form-urlencoded; charset=utf-8', 'application/x-www-form-urlencoded'),
            array('text/html; charset=uTf-8', 'text/html'),
            array('text/html; charset=', null, 'Content-Type header is invalid.'),
            array('text/html;', null, 'Content-Type header is invalid.'),
            array('application/dialog.dot-info7+xml', 'application/dialog.dot-info7+xml'),
            array('application/x-www-form-urlencoded; charset=cp1251', null, 'UTF-8 is the only supported charset.')
        );
    }

    /**
     * @dataProvider dataProviderTestGetActionTypeByMethod
     * @param string $methodName
     * @param string $expectedActionType
     */
    public function testGetActionTypeByMethod($methodName, $expectedActionType)
    {
        $this->assertEquals(
            $expectedActionType,
            Mage_Webapi_Controller_Request_Rest::getActionTypeByOperation($methodName),
            "Action type was identified incorrectly by method name."
        );
    }

    public static function dataProviderTestGetActionTypeByMethod()
    {
        return array(
            array(
                Mage_Webapi_Controller_ActionAbstract::METHOD_CREATE,
                Mage_Webapi_Controller_Request_Rest::ACTION_TYPE_COLLECTION
            ),
            array(
                Mage_Webapi_Controller_ActionAbstract::METHOD_DELETE,
                Mage_Webapi_Controller_Request_Rest::ACTION_TYPE_ITEM
            ),
        );
    }

    public function testGetActionTypeException()
    {
        $methodName = 'invalidMethodV1';
        $this->setExpectedException(
            'InvalidArgumentException',
            sprintf('The "%s" method is not a valid service method.', $methodName)
        );
        Mage_Webapi_Controller_Request_Rest::getActionTypeByOperation($methodName);
    }

    public function testGetServiceVersion()
    {
        $this->_request->setParam('serviceVersion', 'v1');
        $this->assertEquals(1, $this->_request->getServiceVersion(), 'Version number was missed.');
    }

    public function testGetServiceVersionVersionIsNotSpecifiedException()
    {
        $this->markTestIncomplete("Needs to be fixed after service layer implementation.");
        $this->setExpectedException(
            'Mage_Webapi_Exception',
            'Resource version is not specified or invalid one is specified',
            Mage_Webapi_Exception::HTTP_BAD_REQUEST
        );
        $this->_request->getServiceVersion();
    }

    public function testGetServiceName()
    {
        $serviceName = 'serviceName';
        $this->_request->setServiceName($serviceName);
        $this->assertEquals($serviceName, $this->_request->getServiceName());
    }

    public function testGetOperationNameMethodNotExistException()
    {
        /** Prepare mocks for SUT constructor. */
        $this->setExpectedException(
            'Mage_Webapi_Exception',
            'Requested method does not exist.',
            Mage_Webapi_Exception::HTTP_NOT_FOUND
        );
        $this->_request->expects($this->once())->method('isPost')->will($this->returnValue(true));
        $this->_request->expects($this->once())->method('getMethod')->will($this->returnValue('POST'));
        $this->_request->setServiceType(Mage_Webapi_Controller_Request_Rest::ACTION_TYPE_ITEM);
        /** Initialize SUT. */
        $this->_request->getOperationName();
    }

    /**
     * Prepare SUT for GetOperationName() with create action.
     */
    protected function _prepareSutForGetOperationNameWithCreateMethod()
    {
        $this->_request->setServiceType(Mage_Webapi_Controller_Request_Rest::ACTION_TYPE_COLLECTION);
        $this->_request->expects($this->once())->method('isPost')->will($this->returnValue(true));
        $this->_request->expects($this->once())
            ->method('getMethod')
            ->will($this->returnValue('POST'));
        $this->_request->setServiceName('serviceName');
    }
}
