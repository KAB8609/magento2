<?php
/**
 * Test Web API error codes.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Webapi_HttpErrorCodeTest extends Magento_Test_TestCase_WebapiAbstract
{
    public function setUp()
    {
        if (self::ADAPTER_REST != TESTS_WEB_API_ADAPTER) {
            $this->markTestSkipped("This test is intended to be run with REST adapter only");
        }
        parent::setUp();
    }

    public function testSuccess()
    {
        $this->_markTestAsRestOnly();
        $serviceInfo = array(
            'rest' => array(
                'resourcePath' => '/V1/errortest/success',
                'httpMethod' => 'GET'
            ),
        );

        $item = $this->_webApiCall($serviceInfo);

         // TODO: check Http Status = 200, cannot do yet due to missing header info returned

        $this->assertEquals('a good id', $item['id'], 'Success case is correct');
    }

    public function testNotFound()
    {
        $this->_markTestAsRestOnly();
        $serviceInfo = array(
            'rest' => array(
                'resourcePath' => '/V1/errortest/notfound',
                'httpMethod' => 'GET'
            ),
        );

        // Mage_Service_ResourceNotFoundException
        $this->_errorTest($serviceInfo, Mage_Webapi_Exception::HTTP_NOT_FOUND, 2345, 'Resource not found');
    }

    public function testUnauthorized()
    {
        $this->_markTestAsRestOnly();
        $serviceInfo = array(
            'rest' => array(
                'resourcePath' => '/V1/errortest/unauthorized',
                'httpMethod' => 'GET'
            ),
        );

        // Mage_Service_AuthorizationException
        $this->_errorTest(
            $serviceInfo, Mage_Webapi_Exception::HTTP_UNAUTHORIZED, 4567, 'Service authorization exception');
    }

    public function testServiceException()
    {
        $this->_markTestAsRestOnly();
        $serviceInfo = array(
            'rest' => array(
                'resourcePath' => '/V1/errortest/serviceexception',
                'httpMethod' => 'GET'
            ),
        );

        // Mage_Service_Exception
        $this->_errorTest($serviceInfo, Mage_Webapi_Exception::HTTP_BAD_REQUEST, 3456, 'Generic service exception');
    }

    public function testServiceExceptionWithParameters()
    {
        $this->_markTestAsRestOnly();
        $serviceInfo = array(
            'rest' => array(
                'resourcePath' => '/V1/errortest/parameterizedexception',
                'httpMethod' => 'GET'
            )
        );

        // Mage_Service_Exception (with parameters)
        $this->_errorTest(
            $serviceInfo, Mage_Webapi_Exception::HTTP_BAD_REQUEST, 1234, 'Parameterized service exception',
            array('product', 'email'));
    }

    public function testOtherException()
    {
        $this->_markTestAsRestOnly();
        $serviceInfo = array(
            'rest' => array(
                'resourcePath' => '/V1/errortest/otherexception',
                'httpMethod' => 'GET'
            ),
        );

        // Something other than Mage_Service_Exception
        $this->_errorTest(
            $serviceInfo, Mage_Webapi_Exception::HTTP_INTERNAL_ERROR, 5678, 'Non service exception', null);
    }

    /**
     * Perform a negative REST api call test case and compare the results with expected values.
     *
     * @param string $serviceInfo - REST Service information (i.e. resource path and HTTP method)
     * @param int $httpStatus - Expected HTTP status
     * @param int $errorCode - Expected error code
     * @param string $errorMessage - Exception error message
     * @param array $parameters - Optional parameters array, or null if no parameters
     */
    protected function _errorTest ($serviceInfo, $httpStatus, $errorCode, $errorMessage, $parameters = array())
    {
        // TODO: need to get header info instead of catching the exception
        try {
            $this->_webApiCall($serviceInfo);
        } catch (Exception $e) {
            $this->assertEquals($httpStatus, $e->getCode(), 'Checking HTTP status code');

            $body = json_decode($e->getMessage(), true);
            $this->assertEquals($errorCode, $body['errors'][0]['code'], 'Checking body code');
            $this->assertEquals($errorMessage, $body['errors'][0]['message'], 'Checking body message');

            $this->assertEquals($parameters, $body['errors'][0]['parameters'], 'Checking body parameters');
        }
    }
}
