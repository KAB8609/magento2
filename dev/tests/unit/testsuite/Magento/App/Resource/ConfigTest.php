<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\App\Resource;

class ConfigTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\App\Resource\Config
     */
    protected $_model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_scopeMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_cacheMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_readerMock;

    /**
     * @var array
     */
    protected $_resourcesConfig;

    protected function setUp()
    {
        $this->_scopeMock = $this->getMock('Magento\Config\ScopeInterface');
        $this->_cacheMock = $this->getMock('Magento\Config\CacheInterface');

        $this->_readerMock = $this->getMock(
            'Magento\App\Resource\Config\Reader', array(), array(), '', false
        );

        $this->_resourcesConfig = array(
            'mainResourceName' => array(
                'name' => 'mainResourceName',
                'extends' => 'anotherResourceName',
            ),
            'otherResourceName' => array(
                'name' => 'otherResourceName',
                'connection' => 'otherConnectionName',
            ),
            'anotherResourceName' => array(
                'name' => 'anotherResourceName',
                'connection' => 'anotherConnection'
            ),
            'brokenResourceName' => array(
                'name' => 'brokenResourceName',
                'extends' => 'absentResourceName',
            ),
        );

        $this->_cacheMock->expects($this->once())
            ->method('load')
            ->will($this->returnValue(serialize($this->_resourcesConfig)));

        $this->_model = new \Magento\App\Resource\Config(
            $this->_readerMock,
            $this->_scopeMock,
            $this->_cacheMock,
            'cacheId'
        );
    }

    /**
     * @dataProvider getConnectionNameDataProvider
     * @param string $resourceName
     * @param string $connectionName
     */
    public function testGetConnectionName($resourceName, $connectionName)
    {
        $this->assertEquals($connectionName, $this->_model->getConnectionName($resourceName));
    }

    /**
     * @return array
     */
    public function getConnectionNameDataProvider()
    {
        return array(
            array(
                'resourceName' => 'otherResourceName',
                'connectionName' => 'otherConnectionName',
            ),
            array(
                'resourceName' => 'mainResourceName',
                'connectionName' => 'anotherConnection',
            ),
            array(
                'resourceName' => 'brokenResourceName',
                'connectionName' => \Magento\App\Resource\Config::DEFAULT_SETUP_CONNECTION,
            )
        );
    }
}
