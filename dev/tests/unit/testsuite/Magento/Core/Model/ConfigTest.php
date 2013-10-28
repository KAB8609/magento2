<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\Model;

class ConfigTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Core\Model\Config
     */
    protected $_model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_configScopeMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_objectManagerMock;

    /**
     * @var \Magento\Module\ModuleListInterface
     */
    protected $_moduleListMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_sectionPoolMock;

    protected function setUp()
    {
        $xml = '<config>
                    <default>
                        <first>
                            <custom>
                                <node>value</node>
                            </custom>
                        </first>
                    </default>
                </config>';

        $areas = array('adminhtml' => array(
            'base_controller' => 'base_controller',
            'routers' => array(
                'admin' => array(
                    'class' => 'class'
                )
            ),
            'frontName' => 'backend'
        ));

        $configBase = new \Magento\Core\Model\Config\Base($xml);
        $this->_objectManagerMock = $this->getMock('Magento\App\ObjectManager', array(), array(), '', false);
        $modulesReaderMock = $this->getMock('Magento\Core\Model\Config\Modules\Reader', array(), array(), '', false);
        $this->_configScopeMock = $this->getMock('Magento\Config\ScopeInterface');
        $this->_moduleListMock = $this->getMock('Magento\Module\ModuleListInterface');
        $this->_sectionPoolMock = $this->getMock('Magento\Core\Model\Config\SectionPool', array(), array(), '', false);

        $this->_model = new \Magento\Core\Model\Config(
            $this->_objectManagerMock,
            $modulesReaderMock,
            $this->_moduleListMock,
            $this->_configScopeMock,
            $this->_sectionPoolMock,
            $areas
        );
    }
    public function testSetNodeData()
    {
        $this->_model->setNode('some/custom/node', 'true');

        $actual = (string)$this->_model->getNode('some/custom/node');
        $this->assertEquals('true', $actual);
    }

    public function testGetNode()
    {
        $this->assertInstanceOf(
            'Magento\Core\Model\Config\Element',
            $this->_model->getNode('default/first/custom/node')
        );
    }

    public function testSetValue()
    {
        $scope = 'default';
        $scopeCode = null;
        $value = 'test';
        $path = 'test/path';
        $sectionMock = $this->getMock('Magento\Core\Model\Config\Data', array(), array(), '', false);
        $this->_sectionPoolMock->expects($this->once())
            ->method('getSection')
            ->with($scope, $scopeCode)
            ->will($this->returnValue($sectionMock));
        $sectionMock->expects($this->once())
            ->method('setValue')
            ->with($path, $value);
        $this->_model->setValue($path, $value);
    }

    public function testGetValue()
    {
        $path = 'test/path';
        $scope = 'default';
        $scopeCode = null;
        $sectionMock = $this->getMock('Magento\Core\Model\Config\Data', array(), array(), '', false);
        $this->_sectionPoolMock->expects($this->once())->method('getSection')->with($scope, $scopeCode)
            ->will($this->returnValue($sectionMock));
        $sectionMock->expects($this->once())
            ->method('getValue')
            ->with($path);
        $this->_model->getValue($path);
    }

}
