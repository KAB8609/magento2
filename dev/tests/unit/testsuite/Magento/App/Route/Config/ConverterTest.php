<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\App\Route\Config;

class ConverterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\App\Route\Config\Converter
     */
    protected $_model;

    protected function setUp()
    {
        $this->_model = new \Magento\App\Route\Config\Converter();
    }

    public function testConvert()
    {
        $basePath = realpath(__DIR__) . '/_files/';
        $path = $basePath . 'routes.xml';
        $domDocument = new \DOMDocument();
        $domDocument->load($path);
        $expectedData = include($basePath . 'routes.php');
        $this->assertEquals($expectedData, $this->_model->convert($domDocument));
    }
}
