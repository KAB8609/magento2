<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Framework
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Config_XsdTest extends PHPUnit_Framework_TestCase
{
    /**
     * @param string $xsdFile
     * @param string $invalidXmlFile
     * @param int $expectedErrorsQty
     * @dataProvider invalidXmlFileDataProvider
     */
    public function testInvalidXmlFile($xsdFile, $invalidXmlFile, $expectedErrorsQty)
    {
        $dom = new DOMDocument();
        $dom->load(__DIR__ . "/_files/{$invalidXmlFile}");
        libxml_use_internal_errors(true);
        $result = $dom->schemaValidate(__DIR__ . "/../../../../../../lib/Magento/Config/{$xsdFile}");
        $errorsQty = count(libxml_get_errors());
        libxml_use_internal_errors(false);
        $this->assertFalse($result);
        $this->assertEquals($expectedErrorsQty, $errorsQty);
    }

    /**
     * @return array
     */
    public function invalidXmlFileDataProvider()
    {
        return array(
//            array('view.xsd', 'view_invalid.xml', 2),
            array('theme.xsd', 'theme_invalid.xml', 2),
        );
    }
}
