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
 * @package     Magento
 * @subpackage  integration_tests
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magento.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

abstract class Magento_Test_ControllerTestCaseAbstract extends Magento_TestCase
{
    protected $_runCode     = '';
    protected $_runScope    = 'store';
    protected $_runOptions  = array();
    protected $_request;
    protected $_response;

    /**
     * Bootstrap instance getter
     *
     * @return Magento_Test_Bootstrap
     */
    protected function _getBootstrap()
    {
        return Magento_Test_Bootstrap::getInstance();
    }

    /**
     * Bootstrap application before eny test
     *
     * @return void
     */
    protected function setUp()
    {
        /**
         * Use run options from bootstrap
         */
        $this->_runOptions = $this->_getBootstrap()->getAppOptions();
        $this->_runOptions['request']   = $this->getRequest();
        $this->_runOptions['response']  = $this->getResponse();
    }

    /**
     * Run request
     *
     * @return void
     */
    public function dispatch($uri)
    {
        //Unregister previously registered controller
        Mage::unregister('controller');
        $this->getRequest()->setRequestUri($uri);
        Mage::run($this->_runCode, $this->_runScope, $this->_runOptions);
    }

    /**
     * Request getter
     *
     * @return Mage_Core_Controller_Request_Http
     */
    public function getRequest()
    {
        if (!$this->_request) {
            $this->_request = new Magento_Test_Request();
        }
        return $this->_request;
    }

    /**
     * Response getter
     *
     * @return Zend_Controller_Response_Http
     */
    public function getResponse()
    {
        if (!$this->_response) {
            $this->_response = new Magento_Test_Response();
        }
        return $this->_response;
    }

    /**
     * Assert that response is '404 Not Found'
     */
    public function assert404NotFound()
    {
        $this->assertEquals('noRoute', $this->getRequest()->getActionName());
        $this->assertContains('404 Not Found', $this->getResponse()->getBody());
        $this->assertContains(
            '<h3>We are sorry, but the page you are looking for cannot be found.</h3>',
            $this->getResponse()->getBody()
        );
    }

    /**
     * Assert that there is a redirect to expected URL.
     * Omit expected URL to check that redirect to wherever has been occurred.
     *
     * @param string|null $expectedUrl      Expected URL on redirect
     * @param string $message               Custom error message
     */
    public function assertRedirect($expectedUrl = null, $message = '')
    {
        $messageAssert = $message ? $message : 'Response is not contain redirect header.';
        $this->assertTrue($this->getResponse()->isRedirect(), $messageAssert);
        if ($expectedUrl) {
            $redirectedUrl = null;
            foreach ($this->getResponse()->getHeaders() as $header) {
                if ('Location' == $header['name'] && true == $header['replace']) {
                    $redirectedUrl = $header['value'];

                    if ($redirectedUrl != $expectedUrl) {
                        $messageAssert = $message ? $message :
                            sprintf('Expected redirecting to URL "%s", but redirected to "%s"',
                                    $expectedUrl, $redirectedUrl);

                        $this->assertEquals($redirectedUrl, $expectedUrl, $messageAssert);
                    }
                }
            }

        }
    }

    /**
     * Login to admin panel
     *
     * @param string|null $username     Identity
     * @return Magento_Test_ControllerTestCaseAbstract
     * @throws Magento_Test_Exception   Throw exception when admin user not found
     */
    protected function loginToAdmin($username = null, $password = null)
    {
        /** @var $session Mage_Admin_Model_Session */
        $session = Mage::getSingleton('admin/session');
        if (null === $username) {
            $username = TESTS_ADMIN_USERNAME;
        }
        if (null === $password) {
            $password = TESTS_ADMIN_PASSWORD;
        }
        if (!$session->isLoggedIn() || false !== ($user = $session->getUser())
            && $user->getUsername() != $username
        ) {
            $session->login($username, $password);
            if (!$session->isLoggedIn()) {
                throw new Magento_Test_Exception(
                    sprintf('Admin cannot logged with username "%s".', $username));
            }
        }
        return $this;
    }
}
