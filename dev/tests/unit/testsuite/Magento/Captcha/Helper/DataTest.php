<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Captcha
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Captcha_Helper_DataTest extends PHPUnit_Framework_TestCase
{
    /**
     * Fixture for testing getFonts()
     */
    const FONT_FIXTURE = '<fonts><font_code><label>Label</label><path>path/to/fixture.ttf</path></font_code></fonts>';

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_dirMock;

    protected function setUp()
    {
        $this->_dirMock = $this->getMock('Magento_Core_Model_Dir', array(), array(), '', false, false);
    }

    /**
     * Return helper to be tested
     *
     * @param Magento_Core_Model_Store $store
     * @param Magento_Core_Model_Config $config
     * @return Magento_Captcha_Helper_Data
     */
    protected function _getHelper($store, $config)
    {
        $app = $this->getMockBuilder('Magento_Core_Model_App')
            ->disableOriginalConstructor()
            ->getMock();
        $app->expects($this->any())
            ->method('getWebsite')
            ->will($this->returnValue($this->_getWebsiteStub()));
        $app->expects($this->any())
            ->method('getStore')
            ->will($this->returnValue($store));

        $adapterMock = $this->getMockBuilder('Magento\Filesystem\Adapter\Local')
            ->getMock();
        $adapterMock->expects($this->any())
            ->method('isDirectory')
            ->will($this->returnValue(true));

        $filesystem = $this->getMock('Magento\Filesystem', array(), array(), '', false);

        $context = $this->getMock('Magento_Core_Helper_Context', array(), array(), '', false);

        return new Magento_Captcha_Helper_Data($context, $this->_dirMock, $app, $config, $filesystem);
    }

    /**
     * @covers Magento_Captcha_Helper_Data::getCaptcha
     */
    public function testGetCaptcha()
    {
        $store = $this->_getStoreStub();
        $store->expects($this->once())
            ->method('isAdmin')
            ->will($this->returnValue(false));

        $store->expects($this->once())
            ->method('getConfig')
            ->with('customer/captcha/type')
            ->will($this->returnValue('zend'));

        $objectManager = $this->getMock('Magento\ObjectManager');
        $config = $this->_getConfigStub();
        $config->expects($this->once())
            ->method('getModelInstance')
            ->with('Magento_Captcha_Model_Zend')
            ->will($this->returnValue(
            new Magento_Captcha_Model_Default($objectManager, array('formId' => 'user_create'))));

        $helper = $this->_getHelper($store, $config);
        $this->assertInstanceOf('Magento_Captcha_Model_Default', $helper->getCaptcha('user_create'));
    }

    /**
     * @covers Magento_Captcha_Helper_Data::getConfigNode
     */
    public function testGetConfigNode()
    {
        $store = $this->_getStoreStub();
        $store->expects($this->once())
            ->method('isAdmin')
            ->will($this->returnValue(false));

        $store->expects($this->once())
            ->method('getConfig')
            ->with('customer/captcha/enable')
            ->will($this->returnValue('1'));
        $object = $this->_getHelper($store, $this->_getConfigStub());
        $object->getConfigNode('enable');
    }

    public function testGetFonts()
    {
        $this->_dirMock->expects($this->once())
            ->method('getDir')
            ->with(Magento_Core_Model_Dir::LIB)
            ->will($this->returnValue(TESTS_TEMP_DIR . '/lib'));

        $object = $this->_getHelper($this->_getStoreStub(), $this->_getConfigStub());
        $fonts = $object->getFonts();
        $this->assertArrayHasKey('font_code', $fonts); // fixture
        $this->assertArrayHasKey('label', $fonts['font_code']);
        $this->assertArrayHasKey('path', $fonts['font_code']);
        $this->assertEquals('Label', $fonts['font_code']['label']);
        $this->assertStringStartsWith(TESTS_TEMP_DIR, $fonts['font_code']['path']);
        $this->assertStringEndsWith('path/to/fixture.ttf', $fonts['font_code']['path']);
    }

    /**
     * @covers Magento_Captcha_Model_Default::getImgDir
     * @covers Magento_Captcha_Helper_Data::getImgDir
     */
    public function testGetImgDir()
    {
        $this->_dirMock->expects($this->once())
            ->method('getDir')
            ->with(Magento_Core_Model_Dir::MEDIA)
            ->will($this->returnValue(TESTS_TEMP_DIR . '/media'));

        $object = $this->_getHelper($this->_getStoreStub(), $this->_getConfigStub());
        $this->assertFileNotExists(TESTS_TEMP_DIR . '/captcha');
        $result = $object->getImgDir();
        $result = str_replace('/', DIRECTORY_SEPARATOR, $result);
        $this->assertStringStartsWith(TESTS_TEMP_DIR, $result);
        $this->assertStringEndsWith('captcha' . DIRECTORY_SEPARATOR . 'base' . DIRECTORY_SEPARATOR, $result);
    }

    /**
     * @covers Magento_Captcha_Model_Default::getImgUrl
     * @covers Magento_Captcha_Helper_Data::getImgUrl
     */
    public function testGetImgUrl()
    {
        $object = $this->_getHelper($this->_getStoreStub(), $this->_getConfigStub());
        $this->assertEquals($object->getImgUrl(), 'http://localhost/pub/media/captcha/base/');
    }

    /**
     * Create Config Stub
     *
     * @return Magento_Core_Model_Config
     */
    protected function _getConfigStub()
    {
        $config = $this->getMock(
            'Magento_Core_Model_Config',
            array('getNode', 'getModelInstance'),
            array(), '', false
        );

        $config->expects($this->any())
            ->method('getNode')
            ->will($this->returnValue(new SimpleXMLElement(self::FONT_FIXTURE)));
        return $config;
    }

    /**
     * Create Website Stub
     *
     * @return Magento_Core_Model_Website
     */
    protected function _getWebsiteStub()
    {
        $website = $this->getMock(
            'Magento_Core_Model_Website',
            array('getCode'),
            array(), '', false
        );

        $website->expects($this->any())
            ->method('getCode')
            ->will($this->returnValue('base'));

        return $website;
    }

    /**
     * Create store stub
     *
     * @return Magento_Core_Model_Store
     */
    protected function _getStoreStub()
    {
        $store = $this->getMock(
            'Magento_Core_Model_Store',
            array('isAdmin', 'getConfig', 'getBaseUrl'),
            array(), '', false
        );

        $store->expects($this->any())
            ->method('getBaseUrl')
            ->will($this->returnValue('http://localhost/pub/media/'));

        return $store;
    }
}
