<?php
/**
 * Test class for \Magento\Core\Model\Config\Cache
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Core_Model_Config_CacheTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Core\Model\Config\Cache
     */
    protected $_model;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_baseFactoryMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_cacheMock;

    protected function setUp()
    {
        $this->_cacheMock = $this->getMock('Magento\Core\Model\Cache\Type\Config', array(), array(), '', false, false);
        $this->_baseFactoryMock = $this->getMock('Magento\Core\Model\Config\BaseFactory',
            array(), array(), '', false, false);
        $this->_model = new \Magento\Core\Model\Config\Cache($this->_cacheMock, $this->_baseFactoryMock);
    }

    protected function tearDown()
    {
        unset($this->_cacheMock);
        unset($this->_configSectionsMock);
        unset($this->_contFactoryMock);
        unset($this->_baseFactoryMock);
        unset($this->_model);
    }


    public function testCacheLifetime()
    {
        $lifetime = 10;
        $this->_model->setCacheLifetime($lifetime);
        $this->assertEquals($lifetime, $this->_model->getCacheLifeTime());
    }

    public function testLoadWithoutConfig()
    {
        $this->assertFalse($this->_model->load());
    }

    public function testLoadWithConfig()
    {
        $this->_cacheMock->expects($this->once())
            ->method('load')
            ->will($this->returnValue(array('sourceData' => 'test_config')));

        $this->_baseFactoryMock->expects($this->once())
            ->method('create')
            ->with($this->equalTo(array('sourceData' => 'test_config')))
            ->will($this->returnValue('some_instance'));

        $this->assertEquals('some_instance', $this->_model->load());
    }

    public function testSave()
    {
        $configMock = $this->getMock('Magento\Core\Model\Config\Base', array(), array(), '', false);
        $nodeMock = $this->getMock('stdClass', array('asNiceXml'));

        $configMock->expects($this->once())->method('getNode')->will($this->returnValue($nodeMock));
        $nodeMock->expects($this->once())->method('asNiceXml')->with('', false)->will($this->returnValue('test'));
        $this->_cacheMock->expects($this->once())->method('save')->with('test');
        $this->_model->save($configMock);
    }

    public function testClean()
    {
        $this->_cacheMock->expects($this->once())
            ->method('clean');
        $this->_model->clean();
    }
}
