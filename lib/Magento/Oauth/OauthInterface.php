<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Oauth;

interface OauthInterface
{
    /**#@+
     * OAuth result statuses
     */
    const ERR_OK = 0;
    const ERR_VERSION_REJECTED = 1;
    const ERR_PARAMETER_ABSENT = 2;
    const ERR_PARAMETER_REJECTED = 3;
    const ERR_TIMESTAMP_REFUSED = 4;
    const ERR_NONCE_USED = 5;
    const ERR_SIGNATURE_METHOD_REJECTED = 6;
    const ERR_SIGNATURE_INVALID = 7;
    const ERR_CONSUMER_KEY_REJECTED = 8;
    const ERR_TOKEN_USED = 9;
    const ERR_TOKEN_EXPIRED = 10;
    const ERR_TOKEN_REVOKED = 11;
    const ERR_TOKEN_REJECTED = 12;
    const ERR_VERIFIER_INVALID = 13;
    const ERR_PERMISSION_UNKNOWN = 14;
    const ERR_PERMISSION_DENIED = 15;
    const ERR_METHOD_NOT_ALLOWED = 16;
    const ERR_CONSUMER_KEY_INVALID = 17;
    /**#@-*/

    /**#@+
     * Signature Methods
     */
    const SIGNATURE_SHA1 = 'HMAC-SHA1';
    const SIGNATURE_SHA256 = 'HMAC-SHA256';

    /**#@-*/

    /**
     * Create a new consumer account when an Add-On is installed.
     *
     * @param array $consumerData - Information provided by an Add-On when the Add-On is installed.
     * <pre>
     * array(
     *  'name' => 'Add-On Name',
     *  'key' => 'a6aa81cc3e65e2960a4879392445e718',
     *  'secret' => 'b7bb92dd4f76f3a71b598a4a3556f829',
     *  'http_post_url' => 'http://www.my-add-on.com'
     * )
     * </pre>
     * @return array - The Add-On (consumer) data.
     * @throws \Magento\Core\Exception
     * @throws \Magento\Oauth\Exception
     */
    public function createConsumer($consumerData);

    /**
     * Execute post to Add-On (consumer) HTTP Post URL. Generate and return oauth_verifier.
     *
     * @param array $request - The request data that includes the consumer Id.
     * <pre>
     * array('consumer_id' => 1)
     * </pre>
     * @return array - The oauth_verifier.
     * @throws \Magento\Core\Exception
     * @throws \Magento\Oauth\Exception
     */
    public function postToConsumer($request);

    /**
     * Issue a pre-authorization request token to the caller
     *
     * @param array $request array containing parameters necessary for requesting Request Token
     * <pre>
     * array (
     *         'oauth_version' => '1.0',
     *         'oauth_signature_method' => 'HMAC-SHA1',
     *         'oauth_nonce' => 'rI7PSWxTZRHWU3R',
     *         'oauth_timestamp' => '1377183099',
     *         'oauth_consumer_key' => 'a6aa81cc3e65e2960a4879392445e718',
     *         'oauth_signature' => 'VNg4mhFlXk7%2FvsxMqqUd5DWIj9s%3D'',
     *         'request_url' => 'http://magento.ll/oauth/token/access',
     *         'http_method' => 'POST'
     * )
     * </pre>
     * @return array - The request token/secret pair.
     * @throws \Magento\Oauth\Exception
     */
    public function getRequestToken($request);

    /**
     * Get access token for a pre-authorized request token
     *
     * @param array $request array containing parameters necessary for requesting Access Token
     * <pre>
     * array (
     *         'oauth_version' => '1.0',
     *         'oauth_signature_method' => 'HMAC-SHA1',
     *         'oauth_token' => 'a6aa81cc3e65e2960a487939244sssss',
     *         'oauth_nonce' => 'rI7PSWxTZRHWU3R',
     *         'oauth_timestamp' => '1377183099',
     *         'oauth_consumer_key' => 'a6aa81cc3e65e2960a4879392445e718',
     *         'oauth_signature' => 'VNg4mhFlXk7%2FvsxMqqUd5DWIj9s%3D',
     *         'oauth_verifier' => 'a6aa81cc3e65e2960a487939244vvvvv',
     *         'request_url' => 'http://magento.ll/oauth/token/access',
     *         'http_method' => 'POST'
     * )
     * </pre>
     * @return array - The access token/secret pair.
     * @throws \Magento\Oauth\Exception
     */
    public function getAccessToken($request);

    /**
     * Validate an access token request
     *
     * @param array $request containing parameters necessary for validating Access Token
     * <pre>
     * array (
     *         'oauth_version' => '1.0',
     *         'oauth_signature_method' => 'HMAC-SHA1',
     *         'oauth_token' => 'a6aa81cc3e65e2960a487939244sssss',
     *         'oauth_nonce' => 'rI7PSWxTZRHWU3R',
     *         'oauth_timestamp' => '1377183099',
     *         'oauth_consumer_key' => 'a6aa81cc3e65e2960a4879392445e718',
     *         'oauth_signature' => 'VNg4mhFlXk7%2FvsxMqqUd5DWIj9s%3D'',
     *         'request_url' => 'http://magento.ll/oauth/token/access',
     *         'http_method' => 'POST'
     * )
     * </pre>
     * @return boolean true if access token request is valid
     * @throws \Magento\Oauth\Exception
     */
    public function validateAccessTokenRequest($request);

    /**
     * Validate an access token string.
     *
     * @param array $request containing valid access token
     * <pre>
     *  array (
     *       'token' => 'a6aa81cc3e65e2960a4879392445e718'
     * )
     * </pre>
     * @return boolean true if requested access token exists, is associated with a consumer and is valid
     * @throws \Magento\Oauth\Exception
     */
    public function validateAccessToken($request);

    /**
     * Build the authorization header for an authenticated API request
     *
     * @param array $request containing parameters to build the Oauth HTTP Authorization header
     * <pre>
     *  array (
     *      'oauth_consumer_key' => 'edf957ef88492f0a32eb7e1731e85d',
     *      'oauth_consumer_secret' => 'asdawwewefrtyh2f0a32eb7e1731e85d',
     *      'oauth_token' => '7c0709f789e1f38a17aa4b9a28e1b06c',
     *      'oauth_secret' => 'a6agsfrsfgsrjjjjyy487939244ssggg',
     *      'request_url' => 'http://www.example.com/endpoint'
     *      'http_method' => 'POST' [OPTIONAL - defaulted to POST]
     *      'oauth_signature_method' => 'HMAC-SHA1', [OPTIONAL - defaulted to HMAC-SHA1]
     *      'custom_param1' => 'foo',
     *      'custom_param2' => 'bar'
     *   );
     * </pre>
     * @return string
     * <pre>
     * OAuth oauth_version="1.0", oauth_signature_method="HMAC-SHA1", oauth_nonce="5X1aWR2qzf2uFm1",
     * oauth_timestamp="1381930661", oauth_consumer_key="34edf957ef88492f0a32eb7e1731e85d",
     * oauth_token="7c0709f789e1f38a17aa4b9a28e1b06c", oauth_signature="agVxK0epXOOeQK4%2Bc7UAqUXoAok%3D"
     * <pre>
     * @throws \Magento\Oauth\Exception
     */
    public function buildAuthorizationHeader($request);
}
