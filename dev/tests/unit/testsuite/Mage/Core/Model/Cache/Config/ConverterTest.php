<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Core_Model_Cache_Config_ConverterTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var PHPUnit_Framework_MockObject_MockObject|Mage_Core_Model_Cache_Config_Converter
     */
    protected $_model;

    public function setUp()
    {
        $this->_model = new Mage_Core_Model_Cache_Config_Converter();
    }

    public function testConvert()
    {
        $dom = new DOMDocument();
        $xmlFile = __DIR__ . DIRECTORY_SEPARATOR . '_files' . DIRECTORY_SEPARATOR . 'cache_config.xml';
        $dom->loadXML(file_get_contents($xmlFile));

        $convertedFile = __DIR__ . DIRECTORY_SEPARATOR . '_files' . DIRECTORY_SEPARATOR . 'cache_config.php';
        $expectedResult = include $convertedFile;
        $this->assertEquals($expectedResult, $this->_model->convert($dom));
    }

    /**
     * @param string $xmlData
     * @dataProvider wrongXmlDataProvider
     * @expectedException Exception
     */
    public function testMapThrowsExceptionWhenXmlHasWrongFormat($xmlData)
    {
        $dom = new DOMDocument();
        $dom->loadXML($xmlData);
        $this->_model->convert($dom);
    }

    /**
     * @return array
     */
    public function wrongXmlDataProvider()
    {
        return array(
            array(
                '<?xml version="1.0"?><config>',
            )
        );
    }
}