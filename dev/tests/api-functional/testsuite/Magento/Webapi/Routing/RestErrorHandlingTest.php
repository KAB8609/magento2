<?php
/**
 * Test Web API error codes.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Webapi_Routing_RestErrorHandlingTest extends Magento_Test_TestCase_WebapiAbstract
{
    public function setUp()
    {
        $this->_markTestAsRestOnly();
        parent::setUp();
    }

    public function testSuccess()
    {
        $serviceInfo = array(
            'rest' => array(
                'resourcePath' => '/V1/errortest/success',
                'httpMethod' => Magento_Webapi_Model_Rest_Config::HTTP_METHOD_GET
            ),
        );

        $item = $this->_webApiCall($serviceInfo);

         // TODO: check Http Status = 200, cannot do yet due to missing header info returned

        $this->assertEquals('a good id', $item['id'], 'Success case is correct');
    }

    public function testNotFound()
    {
        $serviceInfo = array(
            'rest' => array(
                'resourcePath' => '/V1/errortest/notfound',
                'httpMethod' => Magento_Webapi_Model_Rest_Config::HTTP_METHOD_GET
            ),
        );

        // Magento_Service_ResourceNotFoundException
        $this->_errorTest($serviceInfo, array(), Magento_Webapi_Exception::HTTP_NOT_FOUND, 2345, 'Resource not found');
    }

    public function testUnauthorized()
    {
        $serviceInfo = array(
            'rest' => array(
                'resourcePath' => '/V1/errortest/unauthorized',
                'httpMethod' => Magento_Webapi_Model_Rest_Config::HTTP_METHOD_GET
            ),
        );

        // Magento_Service_AuthorizationException
        $this->_errorTest(
            $serviceInfo,
            array(),
            Magento_Webapi_Exception::HTTP_UNAUTHORIZED,
            4567,
            'Service authorization exception'
        );
    }

    public function testServiceException()
    {
        $serviceInfo = array(
            'rest' => array(
                'resourcePath' => '/V1/errortest/serviceexception',
                'httpMethod' => Magento_Webapi_Model_Rest_Config::HTTP_METHOD_GET
            ),
        );

        // Magento_Service_Exception
        $this->_errorTest(
            $serviceInfo,
            array(),
            Magento_Webapi_Exception::HTTP_BAD_REQUEST,
            3456,
            'Generic service exception'
        );
    }

    public function testServiceExceptionWithParameters()
    {
        $serviceInfo = array(
            'rest' => array(
                'resourcePath' => '/V1/errortest/parameterizedserviceexception',
                'httpMethod' => Magento_Webapi_Model_Rest_Config::HTTP_METHOD_GET
            )
        );
        $details = array(
            'key1' => 'value1',
            'key2' => 'value2'
        );
        $arguments = array(
            'details' => $details
        );
        // Magento_Service_Exception (with parameters)
        $this->_errorTest(
            $serviceInfo,
            $arguments,
            Magento_Webapi_Exception::HTTP_BAD_REQUEST,
            1234,
            'Parameterized service exception',
            $details
        );
    }

    public function testOtherException()
    {
        $serviceInfo = array(
            'rest' => array(
                'resourcePath' => '/V1/errortest/otherexception',
                'httpMethod' => Magento_Webapi_Model_Rest_Config::HTTP_METHOD_GET
            ),
        );

        // Something other than Magento_Service_Exception
        $expectedErrorCode = Mage::app()->isDeveloperMode() ? 5678 : null;
        $expectedErrorMessage = Mage::app()->isDeveloperMode() ? 'Non service exception'
            : 'Internal Error. Details are available in Magento log file. Report ID: %1';
        $this->_errorTest(
            $serviceInfo,
            array(),
            Magento_Webapi_Exception::HTTP_INTERNAL_ERROR,
            $expectedErrorCode,
            $expectedErrorMessage
        );
    }

    public function testReturnIncompatibleDataType()
    {
        $serviceInfo = array(
            'rest' => array(
                'resourcePath' => '/V1/errortest/returnIncompatibleDataType',
                'httpMethod' => Magento_Webapi_Model_Rest_Config::HTTP_METHOD_GET
            ),
        );
        if (!Mage::app()->isDeveloperMode()) {
            /** Developer mode changes tested behavior and it cannot properly be tested for now */
            $expectedMessage = 'Internal Error. Details are available in Magento log file. Report ID: %1';
        } else {
            $expectedMessage = 'The method "returnIncompatibleDataType" of service '
                . '"Magento_TestModule3_Service_ErrorV1Interface" must return an array.';
        }
        // Magento_Service_Exception
        $this->_errorTest(
            $serviceInfo,
            array(),
            Magento_Webapi_Exception::HTTP_INTERNAL_ERROR,
            0,
            $expectedMessage,
            null
        );
    }

    /**
     * Perform a negative REST api call test case and compare the results with expected values.
     *
     * @param string $serviceInfo - REST Service information (i.e. resource path and HTTP method)
     * @param array $data - Data for the cal
     * @param int $httpStatus - Expected HTTP status
     * @param int $errorCode - Expected error code
     * @param string $errorMessage - Exception error message
     * @param array $parameters - Optional parameters array, or null if no parameters
     */
    protected function _errorTest(
        $serviceInfo,
        $data,
        $httpStatus,
        $errorCode,
        $errorMessage,
        $parameters = array()
    ) {
        // TODO: need to get header info instead of catching the exception
        try {
            $this->_webApiCall($serviceInfo, $data);
        } catch (Exception $e) {
            $this->assertEquals($httpStatus, $e->getCode(), 'Checking HTTP status code');

            $body = json_decode($e->getMessage(), true);
            if ($errorCode) {
                $this->assertEquals($errorCode, $body['errors'][0]['code'], 'Checking body code');
            }
            $this->assertEquals($errorMessage, $body['errors'][0]['message'], 'Checking body message');

            if ($parameters) {
                $this->assertEquals($parameters, $body['errors'][0]['parameters'], 'Checking body parameters');
            }
        }
    }
}
