<?php
/**
 * Test class for Mage_Webapi_Model_Acl_Loader_Service_ConfigReader
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Webapi_Model_Acl_Loader_Service_ConfigReaderTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Webapi_Model_Acl_Loader_Service_ConfigReader
     */
    protected $_reader;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject|Mage_Core_Model_Config
     */
    protected $_configMock;

    /**
     * Initialize reader instance
     */
    protected function setUp()
    {
        $path = array(__DIR__, '..', '..', '..', '_files', 'acl.xml');
        $path = realpath(implode(DIRECTORY_SEPARATOR, $path));
        $dirPath = array(
            __DIR__, '..', '..', '..', '..', '..', '..', '..', '..', '..', '..', 'app', 'code', 'Mage', 'Webapi', 'etc'
        );
        $dirPath = realpath(implode(DIRECTORY_SEPARATOR, $dirPath));
        $this->_fileListMock = $this->getMock('Magento_Acl_Loader_Resource_ConfigReader_FileListInterface');
        $this->_fileListMock->expects($this->any())->method('asArray')->will($this->returnValue(array($path)));
        $this->_mapperMock = $this->getMock('Magento_Acl_Loader_Resource_ConfigReader_Xml_ArrayMapper');
        $this->_converterMock = $this->getMock('Magento_Config_Dom_Converter_ArrayConverter');
        $this->_configMock = $this->getMock('Mage_Core_Model_Config', array(), array(), '', false);
        $this->_configMock->expects($this->any())
            ->method('getModuleDir')
            ->with('etc', 'Mage_Webapi')
            ->will($this->returnValue($dirPath));

        $this->_reader = new Mage_Webapi_Model_Acl_Loader_Service_ConfigReader(
            $this->_fileListMock,
            $this->_mapperMock,
            $this->_converterMock,
            $this->_configMock
        );
    }

    public function testGetSchemaFile()
    {
        $actualXsdPath = $this->_reader->getSchemaFile();
        $this->assertInternalType('string', $actualXsdPath);
        $this->assertFileExists($actualXsdPath);
    }

    public function testGetVirtualServices()
    {
        $services = $this->_reader->getAclVirtualServices();
        $this->assertEquals(1, $services->length, 'More than one virtual service.');
        $this->assertEquals('customer/list', $services->item(0)->getAttribute('id'), 'Wrong id of virtual service');
        $this->assertEquals('customer/get', $services->item(0)->getAttribute('parent'),
            'Wrong parent id of virtual service');
    }
}
