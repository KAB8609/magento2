<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Core_Model_Route_ConfigTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Core_Model_Route_Config
     */
    protected $_config;

    /**
     * @var Cache_Mock_Wrapper
     */
    protected $_readerMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_cacheMock;

    protected function setUp()
    {
        $this->_readerMock = $this->getMock('Mage_Core_Model_Route_Config_Reader', array(), array(), '', false);
        $this->_cacheMock = new Cache_Mock_Wrapper();
        $this->_config = new Mage_Core_Model_Route_Config(
            $this->_readerMock,
            $this->_cacheMock
        );
    }

    public function testGetIfCacheIsArray()
    {
        $this->_cacheMock->expects($this->once())
            ->method('get')->with('areaCode', 'RoutesConfig-routerCode')
            ->will($this->returnValue(array('expected')));
        $this->assertEquals(array('expected'), $this->_config->getRoutes('areaCode', 'routerCode'));
    }

    public function testGetIfKeyExist()
    {
        $this->_readerMock->expects($this->once())
            ->method('read')->with('areaCode')->will($this->returnValue(array()));
        $this->assertEquals(array(), $this->_config->getRoutes('areaCode', 'routerCode'));
    }

    public function testGetRoutes()
    {
        $areaConfig['routerCode']['routes'] = 'Expected Value';
        $this->_readerMock->expects($this->once())
            ->method('read')->with('areaCode')->will($this->returnValue($areaConfig));
        $this->_cacheMock->expects($this->once())
            ->method('put')->with('Expected Value', 'areaCode', 'RoutesConfig-routerCode');
        $this->assertEquals('Expected Value', $this->_config->getRoutes('areaCode', 'routerCode'));
    }
}

/**
 * Wrapper to pass method calls and arguments to mockup inside it
 */
class Cache_Mock_Wrapper extends PHPUnit_Framework_TestCase implements Magento_Cache_FrontendInterface
{
    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_mock;

    function __construct()
    {
        $this->_mock = $this->getMock('SomeClass', array('get', 'read', 'put'));
    }

    public function getRealMock()
    {
        return $this->_mock;
    }

    public function get($areaCode, $cacheId)
    {
        return $this->_mock->get($areaCode, $cacheId);
    }

    public function put($routes, $areaCode, $cacheId)
    {
        return $this->_mock->put($routes, $areaCode, $cacheId);
    }

    public function clean($mode = Zend_Cache::CLEANING_MODE_ALL, array $tags = array())
    {
        return $this->_mock->clean($mode, $tags);
    }

    public function load($identifier)
    {
        return $this->_mock->load($identifier);
    }

    public function test($identifier)
    {
        return $this->_mock->test($identifier);
    }

    public function remove($identifier)
    {
        return $this->_mock->remove($identifier);
    }

    public function save($data, $identifier, array $tags = array(), $lifeTime = null)
    {
        return $this->_mock->save($data, $identifier, $tags, $lifeTime);
    }

    public function getBackend()
    {
        return $this->_mock->getBackend();
    }

    public function getLowLevelFrontend()
    {
        return $this->_mock->getLowLevelFrontend();
    }
}