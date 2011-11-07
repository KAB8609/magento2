<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Core
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @group module:Mage_Core
 */
class Mage_Core_Controller_RequestHttpTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Core_Controller_Request_Http
     */
    protected $_model;

    public function setUp()
    {
        $this->_model = new Mage_Core_Controller_Request_Http();
    }

    public function testGetOriginalPathInfo()
    {
        $this->assertEmpty($this->_model->getOriginalPathInfo());
    }

    public function testGetStoreCodeFromPath()
    {
        $this->assertEquals(Mage::app()->getStore()->getCode(), $this->_model->getStoreCodeFromPath());
    }

    /**
     * @magentoConfigFixture current_store web/url/use_store 1
     */
    public function testGetStoreCodeFromPathStoreCodeInUrl()
    {
        $this->_model->setPathInfo('admin/test/');
        $this->assertEquals('admin', $this->_model->getStoreCodeFromPath());
    }

    public function testSetGetPathInfo()
    {
        $this->_model->setPathInfo();
        $this->assertEmpty($this->_model->getPathInfo());

        $this->_model->setRequestUri('test');
        $this->_model->setPathInfo();
        $this->assertEquals('test', $this->_model->getPathInfo());


        $this->_model->setPathInfo('new_test');
        $this->assertEquals('new_test', $this->_model->getPathInfo());

    }

    /**
     * @covers Mage_Core_Controller_Request_Http::rewritePathInfo
     * @covers Mage_Core_Controller_Request_Http::getOriginalPathInfo
     * @magentoConfigFixture current_store web/url/use_store 1
     */
    public function testRewritePathInfoStoreCodeInUrl()
    {
        $pathInfo = $this->_model->getPathInfo();
        $this->_model->rewritePathInfo('test/path');
        $this->assertNotEquals($pathInfo, $this->_model->getPathInfo());
        $this->assertEquals('test/path', $this->_model->getPathInfo());
        $this->assertEquals($pathInfo, $this->_model->getOriginalPathInfo());
    }

    public function testIsDirectAccessFrontendName()
    {
        $this->assertFalse($this->_model->isDirectAccessFrontendName('test'));
        $this->assertTrue($this->_model->isDirectAccessFrontendName('api'));
    }

    public function testGetDirectFrontNames()
    {
        $this->assertContains('api', array_keys($this->_model->getDirectFrontNames()));
    }

    public function testGetOriginalRequest()
    {
        $this->assertInstanceOf('Zend_Controller_Request_Http', $this->_model->getOriginalRequest());
        $this->assertEquals(
            $this->_model->getOriginalPathInfo(),
            $this->_model->getOriginalRequest()->getPathInfo()
        );
    }

    public function testGetRequestString()
    {
        $this->assertEmpty($this->_model->getRequestString());
        $this->_model->setRequestUri('test');
        $this->_model->setPathInfo();
        $this->assertEquals('test', $this->_model->getRequestString());
    }

    public function testGetBasePath()
    {
        $this->assertEquals('/', $this->_model->getBasePath());
    }

    public function testGetBaseUrl()
    {
        $this->assertEmpty($this->_model->getBaseUrl());
    }

    public function testSetGetRouteName()
    {
        $this->assertEmpty($this->_model->getRouteName());
        $this->_model->setRouteName('test');
        $this->assertEquals('test', $this->_model->getRouteName());
    }

    public function testGetHttpHost()
    {
        $this->assertFalse($this->_model->getHttpHost());
        $_SERVER['HTTP_HOST'] = 'localhost:80';
        $this->assertEquals($_SERVER['HTTP_HOST'], $this->_model->getHttpHost(false));
        $this->assertEquals('localhost', $this->_model->getHttpHost());
    }

    public function testSetPost()
    {
        $post = $_POST;
        $this->_model->setPost(array('test' => 'test'));
        $post['test'] = 'test';
        $this->assertEquals($post, $this->_model->getPost());

        $this->_model->setPost('key', 'value');
        $post['key'] = 'value';
        $this->assertEquals($post, $this->_model->getPost());
    }

    public function testInitForward()
    {
        $this->_model->setParam('test', 'test');
        $this->_model->initForward();
        $this->assertEquals(array('test' => 'test'), $this->_model->getBeforeForwardInfo('params'));

        $this->_model->setParam('test', 'test1');
        /* the call shouldn't override existing info*/
        $this->_model->initForward();
        $this->assertEquals(array('test' => 'test'), $this->_model->getBeforeForwardInfo('params'));
    }

    public function testIsStraight()
    {
        $this->assertFalse($this->_model->isStraight());
        $this->assertTrue($this->_model->isStraight(true));
        $this->assertTrue($this->_model->isStraight());
    }

    public function testIsAjax()
    {
        $this->assertFalse($this->_model->isAjax());
        $this->_model->setParam('isAjax', 1);
        $this->assertTrue($this->_model->isAjax());
    }

}
