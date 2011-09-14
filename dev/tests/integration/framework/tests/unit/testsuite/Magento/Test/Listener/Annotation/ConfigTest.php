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

/**
 * Test class for Magento_Test_Listener_Annotation_Config.
 */
class Magento_Test_Listener_Annotation_ConfigTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Test_Listener
     */
    protected $_listener;

    /**
     * @var Magento_Test_Listener_Annotation_Config|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_annotation;

    protected function setUp()
    {
        $this->_listener = new Magento_Test_Listener;
        $this->_listener->startTest($this);

        $this->_annotation = $this->getMock(
            'Magento_Test_Listener_Annotation_Config',
            array('_getConfigValue', '_setConfigValue'),
            array($this->_listener)
        );
    }

    protected function tearDown()
    {
        $this->_listener->endTest($this->_listener->getCurrentTest(), 0);
    }

    /**
     * @magentoConfigFixture web/unsecure/base_url http://example.com/
     */
    public function testGlobalConfig()
    {
        $this->_annotation
            ->expects($this->at(0))
            ->method('_getConfigValue')
            ->with('web/unsecure/base_url')
            ->will($this->returnValue('http://localhost/'))
        ;
        $this->_annotation
            ->expects($this->at(1))
            ->method('_setConfigValue')
            ->with('web/unsecure/base_url', 'http://example.com/')
        ;
        $this->_annotation->startTest();

        $this->_annotation
            ->expects($this->once())
            ->method('_setConfigValue')
            ->with('web/unsecure/base_url', 'http://localhost/')
        ;
        $this->_annotation->endTest();
    }

    /**
     * @magentoConfigFixture current_store dev/restrict/allow_ips 192.168.0.1
     */
    public function testCurrentStoreConfig()
    {
        $this->_annotation
            ->expects($this->at(0))
            ->method('_getConfigValue')
            ->with('dev/restrict/allow_ips', '')
            ->will($this->returnValue('127.0.0.1'))
        ;
        $this->_annotation
            ->expects($this->at(1))
            ->method('_setConfigValue')
            ->with('dev/restrict/allow_ips', '192.168.0.1', '')
        ;
        $this->_annotation->startTest();

        $this->_annotation
            ->expects($this->once())
            ->method('_setConfigValue')
            ->with('dev/restrict/allow_ips', '127.0.0.1', '')
        ;
        $this->_annotation->endTest();
    }

    /**
     * @magentoConfigFixture admin_store dev/restrict/allow_ips 192.168.0.2
     */
    public function testSpecificStoreConfig()
    {
        $this->_annotation
            ->expects($this->at(0))
            ->method('_getConfigValue')
            ->with('dev/restrict/allow_ips', 'admin')
            ->will($this->returnValue('192.168.0.1'))
        ;
        $this->_annotation
            ->expects($this->at(1))
            ->method('_setConfigValue')
            ->with('dev/restrict/allow_ips', '192.168.0.2', 'admin')
        ;
        $this->_annotation->startTest();

        $this->_annotation
            ->expects($this->once())
            ->method('_setConfigValue')
            ->with('dev/restrict/allow_ips', '192.168.0.1', 'admin')
        ;
        $this->_annotation->endTest();
    }

    /**
     * @magentoConfigFixture web/unsecure/base_url http://example.com/
     */
    public function testInitFrontControllerBefore()
    {
        $this->_annotation
            ->expects($this->at(0))
            ->method('_getConfigValue')
            ->with('web/unsecure/base_url')
            ->will($this->returnValue('http://localhost/'))
        ;
        $this->_annotation
            ->expects($this->at(1))
            ->method('_setConfigValue')
            ->with('web/unsecure/base_url', 'http://example.com/')
        ;
        $this->_annotation->initFrontControllerBefore();
    }
}
