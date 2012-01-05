<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Magento
 * @package     Mage_OAuth
 * @subpackage  integration_tests
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magento.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Test OAuth Server
 *
 */
class Mage_OAuth_Model_ServerTest extends Magento_TestCase
{
    /**
     * Message of message of raised exception fail
     */
    const RAISED_EXCEPTION_FAIL_MESSAGE = 'An expected Zend_Oauth_Exception has not been raised.';

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     */
    protected function setUp()
    {
        /** @var $consumer Mage_OAuth_Model_Consumer */
        $consumer = Mage::getModel('oauth/consumer');

        $consumer->setName('Unit Test Consumer')
            ->setCallbackUrl('http://' . TESTS_HTTP_HOST . '/oauth/client/callback')
            ->setKey('12345678901234567890123456789012')
            ->setSecret('12345678901234567890123456789012');

        $consumer->save();

        $this->setFixture('consumer', $consumer);

        /** @var $helper Mage_OAuth_Helper_Data */
        $helper = Mage::helper('oauth');

        $config = array(
            'requestTokenUrl' => $helper->getProtocolEndpointUrl(Mage_OAuth_Helper_Data::ENDPOINT_INITIATE),
            'accessTokenUrl'  => $helper->getProtocolEndpointUrl(Mage_OAuth_Helper_Data::ENDPOINT_TOKEN),
            'authorizeUrl'    => $helper->getProtocolEndpointUrl(Mage_OAuth_Helper_Data::ENDPOINT_AUTHORIZE_CUSTOMER),
            'requestMethod'   => Zend_Oauth::POST,
            'consumerKey'     => $consumer->getKey(),
            'consumerSecret'  => $consumer->getSecret(),
            'callbackUrl'     => $consumer->getCallbackUrl(),
            'signatureMethod' => 'HMAC-SHA1'
        );

        $this->setFixture('client', new Zend_Oauth_Consumer($config));

        parent::setUp();
    }

    /**
     * Tears down the fixture, for example, close a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        $this->deleteFixture('consumer', true);

        parent::tearDown();
    }

    /**
     * Retrieve array from body string
     *
     * @param string $body response body string
     * @return array
     */
    public function _getArrayFromBody($body)
    {
        $getParamLines = explode('&', $body);
        $getParams = array();
        foreach ($getParamLines as $getParamLine) {
            $tmp = explode('=', $getParamLine);
            $getParams[$tmp[0]] = $tmp[1];
        }
        return $getParams;
    }

    /**
     * Get varien http client
     *
     * @param array|null $options
     * @param string $method
     * @return Varien_Http_Client
     */
    protected function _getHttpClient($options = null, $method = Varien_Http_Client::POST)
    {
        /** @var $httpUtility Zend_Oauth_Http_Utility */
        $httpUtility = new Zend_Oauth_Http_Utility;

        /** @var $helper Mage_OAuth_Helper_Data */
        $helper = Mage::helper('oauth');

        /** @var $client Varien_Http_Client */
        $client = new Varien_Http_Client($helper->getProtocolEndpointUrl(Mage_OAuth_Helper_Data::ENDPOINT_INITIATE));

        /** @var $consumer Mage_OAuth_Model_Consumer */
        $consumer = $this->getFixture('consumer');

        $data = array(
            'oauth_consumer_key'     => $consumer->getKey(),
            'oauth_nonce'            => $httpUtility->generateNonce(),
            'oauth_timestamp'        => $httpUtility->generateTimestamp(),
            'oauth_signature_method' => 'HMAC-SHA1',
            'oauth_version'          => '1.0',
            'oauth_callback'         => $consumer->getCallbackUrl()
        );

        if (is_array($options)) {
            foreach ($options as $key => $value) {
                $data[$key] = $value;
            }
        }

        $data['oauth_signature'] = $httpUtility->sign(
            $data,
            $data['oauth_signature_method'],
            $consumer->getSecret(),
            null,
            Varien_Http_Client::POST,
            $helper->getProtocolEndpointUrl(Mage_OAuth_Helper_Data::ENDPOINT_INITIATE)
        );

        $client->setMethod($method);
        if (Varien_Http_Client::POST == $method) {
            $client->setParameterPost($data);
        } elseif (Varien_Http_Client::GET == $method) {
            $client->setParameterGet($data);
        } else {
            $this->fail('Wrong HTTP method.');
        }
        return $client;
    }

    /**
     * Test initiative request to oAuth server
     *
     * @return void
     */
    public function testGetRequestToken()
    {
        /** @var $client Zend_Oauth_Consumer */
        $client = $this->getFixture('client');

        /** @var $requestToken Zend_Oauth_Token_Request */
        $requestToken = $client->getRequestToken();
        $this->assertTrue($requestToken->isValid());

        /** @var $token Mage_OAuth_Model_Token */
        $token = Mage::getModel('oauth/token');
        $token->load($requestToken->getParam('oauth_token'), 'token');

        $this->assertGreaterThan(0, $token->getId());
        $this->assertNull($token->getCustomerId());
        $this->assertNull($token->getAdminId());
        $this->assertEmpty($token->getVerifier());
        $this->assertEquals($requestToken->getParam('oauth_token_secret'), $token->getSecret());
        $this->assertEquals(Mage_OAuth_Model_Token::TYPE_REQUEST, $token->getType());
        $this->assertEquals(0, $token->getAuthorized());
        $this->assertEquals(0, $token->getRevoked());

        /** @var $consumer Mage_OAuth_Model_Consumer */
        $consumer = $this->getFixture('consumer');
        $this->assertEquals($consumer->getCallbackUrl(), $token->getCallbackUrl());
        $this->assertEquals($consumer->getId(), $token->getConsumerId());
    }

    /**
     * Test success response code
     *
     * @return void
     */
    public function testSuccessResponseCode()
    {
        /** @var $client Zend_Oauth_Consumer */
        $client = $this->getFixture('client');

        /** @var $requestToken Zend_Oauth_Token_Request */
        $requestToken = $client->getRequestToken();
        $this->assertEquals(Mage_OAuth_Model_Server::HTTP_OK, $requestToken->getResponse()->getStatus());
    }

    /**
     * Test count of response params
     *
     * @return void
     */
    public function testResponseParams()
    {
        /** @var $client Zend_Oauth_Consumer */
        $client = $this->getFixture('client');

        /** @var $requestToken Zend_Oauth_Token_Request */
        $requestToken = $client->getRequestToken();
        $body = $requestToken->getResponse()->getBody();

        $params = explode('&', $body);
        $this->assertEquals(3, count($params));

        $params = explode('=', $body);
        $this->assertEquals(4, count($params));

        // oauth_callback_confirmed MUST be present and set to "true"
        $this->assertEquals('true', $requestToken->getParam('oauth_callback_confirmed'));
    }

    /**
     * Test request with a wrong signature method
     *
     * @return void
     */
    public function testWrongSignatureMethod()
    {
        /** @var $client Zend_Oauth_Consumer */
        $client = $this->getFixture('client');

        try {
            $client->getRequestToken(array('oauth_signature_method' => 'qwerty'));
        } catch (Zend_Oauth_Exception $e) {
            /** @var $lastResponse Zend_Http_Response */
            $lastResponse = $client->getHttpClient()->getLastResponse();
            $this->assertEquals(Mage_OAuth_Model_Server::HTTP_BAD_REQUEST, $lastResponse->getStatus());
            $this->assertEquals('oauth_problem=signature_method_rejected', $lastResponse->getBody());
            return;
        }

        $this->fail(self::RAISED_EXCEPTION_FAIL_MESSAGE);
    }

    /**
     * Test request with invalid signature
     *
     * @return void
     */
    public function testInvalidSignature()
    {
        /** @var $client Varien_Http_Client */
        $client = $this->_getHttpClient();
        $client->setParameterPost('oauth_signature', 'qwerty');

        /** @var $response Zend_Http_Response */
        $response = $client->request();

        $this->assertEquals(Mage_OAuth_Model_Server::HTTP_UNAUTHORIZED, $response->getStatus());
        $params = $this->_getArrayFromBody($response->getBody());

        $this->assertEquals('signature_invalid', $params['oauth_problem']);
    }

    /**
     * Test request with a wrong OAuth version
     *
     * @return void
     */
    public function testWrongOauthVersion()
    {
        /** @var $client Zend_Oauth_Consumer */
        $client = $this->getFixture('client');

        try {
            $client->getRequestToken(array('oauth_version' => '2.0'));
        } catch (Zend_Oauth_Exception $e) {
            /** @var $lastResponse Zend_Http_Response */
            $lastResponse = $client->getHttpClient()->getLastResponse();
            $this->assertEquals(Mage_OAuth_Model_Server::HTTP_BAD_REQUEST, $lastResponse->getStatus());
            $this->assertEquals('oauth_problem=version_rejected', $lastResponse->getBody());
            return;
        }

        $this->fail(self::RAISED_EXCEPTION_FAIL_MESSAGE);
    }

    /**
     * Test request with an absent param
     *
     * @return void
     */
    public function testAbsentParam()
    {
        /** @var $client Zend_Oauth_Consumer */
        $client = $this->getFixture('client');

        try {
            $client->getRequestToken(array('oauth_consumer_key' => ''));
        } catch (Zend_Oauth_Exception $e) {
            /** @var $lastResponse Zend_Http_Response */
            $lastResponse = $client->getHttpClient()->getLastResponse();
            $this->assertEquals(Mage_OAuth_Model_Server::HTTP_BAD_REQUEST, $lastResponse->getStatus());
            $this->assertEquals('oauth_problem=parameter_absent&oauth_parameters_absent=oauth_consumer_key',
                $lastResponse->getBody());
            return;
        }

        $this->fail(self::RAISED_EXCEPTION_FAIL_MESSAGE);
    }

    /**
     * Test request with "oob" callback url
     *
     * @return void
     */
    public function testOobCallbackUrl()
    {
        /** @var $client Varien_Http_Client */
        $client = $this->_getHttpClient(array('oauth_callback' => Mage_OAuth_Model_Server::CALLBACK_ESTABLISHED));

        /** @var $response Zend_Http_Response */
        $response = $client->request();

        $this->assertEquals(Mage_OAuth_Model_Server::HTTP_OK, $response->getStatus());
        $params = $this->_getArrayFromBody($response->getBody());

        /** @var $token Mage_OAuth_Model_Token */
        $token = Mage::getModel('oauth/token');
        $token->load($params['oauth_token'], 'token');

        $this->assertGreaterThan(0, $token->getId());
        $this->assertEquals(Mage_OAuth_Model_Server::CALLBACK_ESTABLISHED, $token->getCallbackUrl());
    }

    /**
     * Test request with invalid callback url
     *
     * @return void
     */
    public function testInvalidCallbackUrl()
    {
        /** @var $client Varien_Http_Client */
        $client = $this->_getHttpClient(array('oauth_callback' => 'qwerty'));

        /** @var $response Zend_Http_Response */
        $response = $client->request();

        $this->assertEquals(Mage_OAuth_Model_Server::HTTP_BAD_REQUEST, $response->getStatus());
        $this->assertEquals('oauth_problem=parameter_rejected&message=oauth_callback', $response->getBody());
    }

    /**
     * Test request with invalid timestamp
     *
     * @return void
     */
    public function testRefusedTimestamp()
    {
        /** @var $client Varien_Http_Client */
        $client = $this->_getHttpClient();
        $client->setParameterPost('oauth_timestamp', 'qwerty');

        /** @var $response Zend_Http_Response */
        $response = $client->request();

        $this->assertEquals(Mage_OAuth_Model_Server::HTTP_BAD_REQUEST, $response->getStatus());
        $this->assertEquals('oauth_problem=timestamp_refused', $response->getBody());
    }

    /**
     * Test request with invalid consumer key
     *
     * @return void
     */
    public function testRejectedConsumerKey()
    {
        /** @var $client Varien_Http_Client */
        $client = $this->_getHttpClient(array('oauth_consumer_key' => 'qwerty'));

        /** @var $response Zend_Http_Response */
        $response = $client->request();

        $this->assertEquals(Mage_OAuth_Model_Server::HTTP_UNAUTHORIZED, $response->getStatus());
        $this->assertEquals('oauth_problem=consumer_key_rejected', $response->getBody());
    }
}
