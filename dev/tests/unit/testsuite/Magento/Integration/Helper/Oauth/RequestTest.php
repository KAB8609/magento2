<?php
/**
 * Test WebAPI authentication helper.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Integration\Helper\Oauth;

class RequestTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\Oauth\Helper\Request */
    protected $_oauthHelper;

    protected function setUp()
    {
        $this->_oauthHelper = new \Magento\Oauth\Helper\Request();
    }

    protected function tearDown()
    {
        unset($this->_oauthHelper);
    }

    /**
     * @dataProvider dataProviderForPrepareErrorResponseTest
     */
    public function testPrepareErrorResponse($exception, $response, $expected)
    {
        /* @var $response \Zend_Controller_Response_Http */
        $errorResponse = $this->_oauthHelper->prepareErrorResponse($exception, $response);
        $this->assertEquals(['oauth_problem' => $expected[0]], $errorResponse);
        $this->assertEquals($expected[1], $response->getHttpResponseCode());
    }

    public function dataProviderForPrepareErrorResponseTest()
    {
        return [
            [
                new \Magento\Oauth\Exception('msg', \Magento\Oauth\OauthInterface::ERR_VERSION_REJECTED),
                new \Zend_Controller_Response_Http(),
                ['version_rejected&message=msg', \Magento\Oauth\Helper\Request::HTTP_BAD_REQUEST]
            ],
            [
                new \Magento\Oauth\Exception('msg', 255),
                new \Zend_Controller_Response_Http(),
                ['unknown_problem&code=255&message=msg', \Magento\Oauth\Helper\Request::HTTP_INTERNAL_ERROR]
            ],
            [
                new \Magento\Oauth\Exception('param', \Magento\Oauth\OauthInterface::ERR_PARAMETER_ABSENT),
                new \Zend_Controller_Response_Http(),
                ['parameter_absent&oauth_parameters_absent=param', \Magento\Oauth\Helper\Request::HTTP_BAD_REQUEST]
            ],
            [
                new \Exception('msg'),
                new \Zend_Controller_Response_Http(),
                ['internal_error&message=msg', \Magento\Oauth\Helper\Request::HTTP_INTERNAL_ERROR]
            ],
            [
                new \Exception(),
                new \Zend_Controller_Response_Http(),
                ['internal_error&message=empty_message', \Magento\Oauth\Helper\Request::HTTP_INTERNAL_ERROR]
            ],
        ];
    }
}