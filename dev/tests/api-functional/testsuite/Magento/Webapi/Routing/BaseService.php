<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Base class for all Service based routing tests
 */
namespace Magento\Webapi\Routing;

abstract class BaseService extends \Magento\TestFramework\TestCase\WebapiAbstract
{
    /**
     * Utility to check a particular adapter and assert unauthorized access
     *
     * @param $serviceInfo
     * @param $requestData
     */
    protected function assertUnauthorizedException($serviceInfo, $requestData = null)
    {
        if (TESTS_WEB_API_ADAPTER == self::ADAPTER_SOAP) {
            $this->assertSoapException($serviceInfo, $requestData);
        } else if (TESTS_WEB_API_ADAPTER == self::ADAPTER_REST) {
            $this->assertRestUnauthorizedeException($serviceInfo, $requestData);
        }
    }

    /**
     * This is a helper function to invoke the REST api and assert
     * access is unauthorized
     *
     * @param $serviceInfo
     * @param $requestData
     */
    protected function assertRestUnauthorizedeException($serviceInfo, $requestData = null)
    {
        try {
            $this->_webApiCall($serviceInfo, $requestData);
        } catch (\Exception $e) {
            $this->assertContains(
                '{"errors":[{"message":"Not Authorized.","http_code":401',
                $e->getMessage(),
                sprintf(
                    'REST routing did not fail as expected for the method "%s" of service "%s"',
                    $serviceInfo['rest']['httpMethod'],
                    $serviceInfo['rest']['resourcePath']
                )
            );
        }
    }

    /**
     * Utility to check a particular adapter and assert the exception
     *
     * @param $serviceInfo
     * @param $requestData
     */
    protected function assertNoRouteOrOperationException($serviceInfo, $requestData = null)
    {
        if (TESTS_WEB_API_ADAPTER == self::ADAPTER_SOAP) {
            $this->assertSoapException($serviceInfo, $requestData);
        } else if (TESTS_WEB_API_ADAPTER == self::ADAPTER_REST) {
            $this->assertNoRestRouteException($serviceInfo, $requestData);
        }
    }

    /**
     * This is a helper function to invoke the REST api and assert for
     * test cases that no such REST route exist
     *
     * @param $serviceInfo
     * @param $requestData
     */
    protected function assertNoRestRouteException($serviceInfo, $requestData = null)
    {
        try {
            $this->_webApiCall($serviceInfo, $requestData);
        } catch (\Exception $e) {
            $this->assertContains(
                '{"errors":[{"message":"Request does not match any route.","http_code":404',
                $e->getMessage(),
                sprintf(
                    'REST routing did not fail as expected for the method "%s" of service "%s"',
                    $serviceInfo['rest']['httpMethod'],
                    $serviceInfo['rest']['resourcePath']
                )
            );
        }
    }

    /**
     * TODO: Temporary \Exception assertion. Need to refine
     * This is a helper function to invoke the SOAP api and assert for the NoWebApiXmlTestTest
     * test cases that no such SOAP route exists
     *
     * @param $serviceInfo
     * @param $requestData
     */
    protected function assertSoapException($serviceInfo, $requestData = null)
    {
        try {
            $this->_webApiCall($serviceInfo, $requestData);
        } catch (\Exception $e) {
            $this->assertEquals(
                get_class($e),
                'SoapFault',
                sprintf(
                    'Expected SoapFault exception not generated for
                    Service - "%s" and Operation - "%s"',
                    $serviceInfo['soap']['service'],
                    $serviceInfo['soap']['operation']
                )
            );
        }
    }

}
