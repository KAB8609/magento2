<?php
/**
 * Test class for Mage_Webapi_Model_Authorization_Config_Reader
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Webapi_Model_Authorization_Config_ReaderTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Webapi_Model_Authorization_Config_Reader
     */
    protected $_reader;

    /**
     * Initialize reader instance.
     */
    protected function setUp()
    {
        $path = array(__DIR__, '..', '..', '_files', 'acl.xml');
        $path = realpath(implode(DIRECTORY_SEPARATOR, $path));
        $this->_reader = new Mage_Webapi_Model_Authorization_Config_Reader(array($path));
    }

    /**
     * Unset reader instance.
     */
    protected function tearDown()
    {
        unset($this->_reader);
    }

    /**
     * Check that correct XSD file is provided.
     */
    public function testGetSchemaFile()
    {
        $xsdPath = array(__DIR__, '..', '..', '_files', 'acl.xsd');
        $xsdPath = realpath(implode(DIRECTORY_SEPARATOR, $xsdPath));
        $actualXsdPath = $this->_reader->getSchemaFile();

        $this->assertInternalType('string', $actualXsdPath);
        $this->assertFileExists($actualXsdPath);
        $this->assertXmlFileEqualsXmlFile($xsdPath, $actualXsdPath);
    }
}
