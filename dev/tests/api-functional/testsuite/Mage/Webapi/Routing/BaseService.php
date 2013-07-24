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
abstract class Mage_Webapi_Routing_BaseService extends Magento_Test_TestCase_WebapiAbstract
{

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
        } catch (Exception $e) {
            $this->assertContains(
                '{"errors":[{"code":404,"message":"Request does not match any route.',
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
     * TODO: Temporary Exception assertion. Need to refine
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
        } catch (Exception $e) {
            $this->assertEquals(
                get_class($e),
                'SoapFault',
                sprintf(
                    'Expected SoapFault exception not generated for
                    Service - "%s" and serviceVersion - "%s" and Operation - "%s"',
                    $serviceInfo['soap']['service'],
                    $serviceInfo['soap']['serviceVersion'],
                    $serviceInfo['soap']['operation']
                )
            );
        }
    }

}
