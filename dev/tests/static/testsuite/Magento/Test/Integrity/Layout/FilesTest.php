<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Test\Integrity\Layout;

class FilesTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var string
     */
    protected $_schemaFile;

    protected function setUp()
    {
        $this->_schemaFile = \Magento\TestFramework\Utility\Files::init()->getModuleFile(
            'Magento', 'Core', 'etc' . DIRECTORY_SEPARATOR . 'layout_single.xsd'
        );
    }

    public function testLayouts()
    {
        $invoker = new \Magento\TestFramework\Utility\AggregateInvoker($this);
        $invoker(
            function ($layout) {
                $dom = new \DOMDocument();
                $dom->loadXML(file_get_contents($layout));
                $errors = $this->_validateDomDocument($dom, $this->_schemaFile);
                $this->assertTrue(empty($errors), print_r($errors, true));
            },
            \Magento\TestFramework\Utility\Files::init()->getLayoutFiles()
        );
    }

    /**
     * @param \DOMDocument $dom
     * @param $schemaFileName
     * @return array
     */
    protected function _validateDomDocument(\DOMDocument $dom, $schemaFileName)
    {
        libxml_use_internal_errors(true);
        $result = $dom->schemaValidate($schemaFileName);
        $errors = array();
        if (!$result) {
            $validationErrors = libxml_get_errors();
            if (count($validationErrors)) {
                foreach ($validationErrors as $error) {
                    $errors[] = "{$error->message} Line: {$error->line}\n";
                }
            } else {
                $errors[] = 'Unknown validation error';
            }
        }
        libxml_use_internal_errors(false);
        return $errors;
    }
}
