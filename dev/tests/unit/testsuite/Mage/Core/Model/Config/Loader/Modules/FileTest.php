<?php
/**
 * {license_notice}
 *
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Mage_Core_Model_Config_Loader_Modules_File
 */
class Mage_Core_Model_Config_Loader_Modules_FileTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Core_Model_Config_Loader_Modules_File
     */
    protected $_model;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_modulesConfigMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_prototypeFactoryMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_dirsMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_baseConfigMock;

    protected function setUp()
    {
        $this->_modulesConfigMock = $this->getMock('Mage_Core_Model_Config_Modules',
            array(), array(), '', false, false);
        $this->_prototypeFactoryMock = $this->getMock('Mage_Core_Model_Config_BaseFactory',
            array(), array(), '', false, false);
        $this->_dirsMock = $this->getMock('Mage_Core_Model_Dir', array(), array(), '', false, false);
        $this->_baseConfigMock = $this->getMock('Mage_Core_Model_Config_Base', array(), array(), '', false, false);
        $this->_model = new Mage_Core_Model_Config_Loader_Modules_File(
            $this->_dirsMock,
            $this->_prototypeFactoryMock
        );
    }

    protected function tearDown()
    {
        unset($this->_modulesConfigMock);
        unset($this->_prototypeFactoryMock);
        unset($this->_dirsMock);
        unset($this->_baseConfigMock);
        unset($this->_model);
    }

    public function testLoadConfigurationFromFile()
    {
        $nodes = new Mage_Core_Model_Config_Element('<modules><mod1><active>1</active></mod1></modules>');
        $fileName = 'acl.xml';
        $this->_prototypeFactoryMock->expects($this->exactly(2))
            ->method('create')
            ->with($this->equalTo('<config/>'))
            ->will($this->returnValue($this->_baseConfigMock));
        $this->_modulesConfigMock->expects($this->exactly(2))
            ->method('getNode')
            ->will($this->returnValueMap(array(
                array('modules', $nodes),
                array('modules/mod1/codePool', 'core')
            )));
        $result = $this->_model->loadConfigurationFromFile($this->_modulesConfigMock, $fileName, null, null, array());
        $this->assertInstanceOf('Mage_Core_Model_Config_Base', $result);
    }

    public function testLoadConfigurationFromFileMergeToObject()
    {
        $nodes = new Mage_Core_Model_Config_Element('<config><mod1><active>1</active></mod1></config>');
        $modulesConfigMock = $this->getMock('Mage_Core_Model_ConfigInterface', array(), array(), '', false, false);
        $fileName = 'acl.xml';
        $mergeToObject = $this->getMock('Mage_Core_Model_Config_Base', array(), array(), '', false, false);
        $mergeModel = null;
        $configCache = array();
        $modulesConfigMock->expects($this->exactly(2))
            ->method('getNode')
            ->will($this->returnValue($nodes)
        );
        $this->_prototypeFactoryMock->expects($this->exactly(1))
            ->method('create')
            ->with($this->equalTo('<config/>'))
            ->will($this->returnValue($mergeToObject)
        );
        $this->_model->loadConfigurationFromFile($modulesConfigMock, $fileName, $mergeToObject, $mergeModel,
            $configCache);
    }

    public function testGetModuleDirWithoutData()
    {
        $moduleName = '';
        $type = '';
        $expectedValue = '\\\\';
        $this->_modulesConfigMock->expects($this->once())
            ->method('getNode');
        $this->_dirsMock->expects($this->once())
            ->method('getDir')
            ->with($this->equalTo(Mage_Core_Model_Dir::MODULES));
        $actualValue = $this->_model->getModuleDir($this->_modulesConfigMock, $type, $moduleName);
        $this->assertEquals($expectedValue, $actualValue);
    }

    public function testGetModuleDirWithData()
    {
        $moduleName = 'test';
        $type = 'etc';
        $path = realpath(__DIR__. '/../../_files/testdir/etc');
        $this->_model->setModuleDir($moduleName, $type, $path);
        $this->assertEquals($path, $this->_model->getModuleDir($this->_modulesConfigMock, $type, $moduleName));
    }
}