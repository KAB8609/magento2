<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\App\Resource\Config;

class SchemaLocatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var string
     */
    protected $_expected;

    /**
     * @var \Magento\App\Resource\Config\SchemaLocator
     */
    protected $_model;

    protected function setUp()
    {
        $this->_expected = BP . str_replace('\\', DIRECTORY_SEPARATOR, '\lib\Magento\App\etc\resources.xsd');
        $this->_model = new \Magento\App\Resource\Config\SchemaLocator();
    }

    public function testGetSchema()
    {
        $this->assertEquals($this->_expected, $this->_model->getSchema());
    }

    public function testGetPerFileSchema()
    {
        $this->assertEquals($this->_expected, $this->_model->getPerFileSchema());
    }
}