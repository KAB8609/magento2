<?php
/**
 * {license_notice}
 * 
 * @copyright {copyright}
 * @license   {license_link}
 */
class Mage_Core_Model_Config_PrimaryTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Core_Model_Config_Primary
     */
    protected $_model;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_dirMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_loaderMock;

    /**
     * @var string
     */
    protected $_configString;

    protected function setUp()
    {
        $this->_dirMock = $this->getMock('Mage_Core_Model_Dir', array(), array(), '', false);
        $this->_dirMock->expects($this->any())->method('getDir')->will($this->returnValueMap(array(
            array(Mage_Core_Model_Dir::DI, '/path_to_root/var/di'),
            array(Mage_Core_Model_Dir::ROOT, '/path_to_root'),
        )));
        $this->_loaderMock = $this->getMock('Mage_Core_Model_Config_LoaderInterface');
        $that = $this;
        $this->_loaderMock->expects($this->once())->method('load')->will($this->returnCallback(
            function($config) use ($that) {
                $testConfig = new Mage_Core_Model_Config_Base($that->getConfigString());
                $config->getNode()->extend($testConfig->getNode());
            }
        ));
    }

    protected function tearDown()
    {
        unset($this->_dirMock);
        unset($this->_loaderMock);
        unset($this->_model);
    }

    public function getConfigString()
    {
        return $this->_configString;
    }

    public function testGetDefinitionPathReturnsDefaultPathIfNothingSpecified()
    {
        $this->_model = new Mage_Core_Model_Config_Primary(BP, array(), $this->_dirMock, $this->_loaderMock);
        $expectedPath = '/path_to_root/var/di' . DIRECTORY_SEPARATOR . 'definitions.php';
        $this->assertEquals($expectedPath, $this->_model->getDefinitionPath());
    }

    public function testGetDefinitionPathReturnsAbsolutePath()
    {
        $this->_configString = '<config><global><di>'
            . '<definitions><path>customPath</path></definitions>'
            . '</di></global></config>';
        $this->_model = new Mage_Core_Model_Config_Primary(BP, array(), $this->_dirMock, $this->_loaderMock);
        $this->assertEquals('customPath', $this->_model->getDefinitionPath());
    }

    public function testGetDefinitionPathReturnsRelativePath()
    {
        $this->_configString = '<config><global><di>'
            . '<definitions><relativePath>customPath</relativePath></definitions>'
            . '</di></global></config>';
        $this->_model = new Mage_Core_Model_Config_Primary(BP, array(), $this->_dirMock, $this->_loaderMock);
        $expectedPath = '/path_to_root' . DIRECTORY_SEPARATOR . 'customPath';
        $this->assertEquals($expectedPath, $this->_model->getDefinitionPath());
    }
}
