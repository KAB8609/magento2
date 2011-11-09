<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

abstract class Magento_Test_TestCase_ControllerAbstract extends PHPUnit_Framework_TestCase
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
    }

    /**
     * Assert that there is a redirect to expected URL.
     * Omit expected URL to check that redirect to wherever has been occurred.
     *
     * @param string|null $expectedUrl
     */
    public function assertRedirect($expectedUrl = null)
    {
        $this->assertTrue($this->getResponse()->isRedirect());
        if ($expectedUrl) {
            $this->assertContains(array(
                'name'    => 'Location',
                'value'   => $expectedUrl,
                'replace' => true,
            ), $this->getResponse()->getHeaders());
        }
    }
}
