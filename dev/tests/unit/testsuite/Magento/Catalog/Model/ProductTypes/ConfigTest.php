<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model\ProductTypes;

class ConfigTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_readerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_cacheMock;

    /**
     * @var \Magento\Catalog\Model\ProductTypes\Config
     */
    protected $_model;

    protected function setUp()
    {
        $this->_readerMock = $this->getMock(
            'Magento\Catalog\Model\ProductTypes\Config\Reader', array(), array(), '', false);
        $this->_cacheMock = $this->getMock('Magento\Config\CacheInterface');
    }

    /**
     * @dataProvider getTypeDataProvider
     *
     * @param array $value
     * @param mixed $expected
     */
    public function testGetType($value, $expected)
    {
        $this->_cacheMock->expects($this->any())->method('load')->will($this->returnValue(serialize($value)));
        $this->_model = new \Magento\Catalog\Model\ProductTypes\Config($this->_readerMock,
            $this->_cacheMock, 'cache_id');
        $this->assertEquals($expected, $this->_model->getType('global'));
    }

    public function getTypeDataProvider()
    {
        return array(
            'global_key_exist' => array(array('global' => 'value'), 'value'),
            'return_default_value' => array(array('some_key' => 'value'), array())
        );
    }

    public function testGetAll()
    {
        $expected = array('Expected Data');
        $this->_cacheMock->expects($this->once())->method('load')->will($this->returnValue(serialize($expected)));
        $this->_model = new \Magento\Catalog\Model\ProductTypes\Config(
            $this->_readerMock,
            $this->_cacheMock,
            'cache_id'
        );
        $this->assertEquals($expected, $this->_model->getAll());
    }
}