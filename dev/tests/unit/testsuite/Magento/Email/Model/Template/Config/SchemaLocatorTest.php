<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Email\Model\Template\Config;

class SchemaLocatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Email\Model\Template\Config\SchemaLocator
     */
    protected $_model;

    /**
     * @var \Magento\Module\Dir\Reader|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_moduleReader;

    protected function setUp()
    {
        $this->_moduleReader = $this->getMock(
            'Magento\Module\Dir\Reader', array('getModuleDir'), array(), '', false
        );
        $this->_moduleReader
            ->expects($this->once())
            ->method('getModuleDir')->with('etc', 'Magento_Email')
            ->will($this->returnValue('fixture_dir'))
        ;
        $this->_model = new \Magento\Email\Model\Template\Config\SchemaLocator($this->_moduleReader);
    }

    public function testGetSchema()
    {
        $actualResult = $this->_model->getSchema();
        $this->assertEquals('fixture_dir/email_templates.xsd', $actualResult);
        // Makes sure the value is calculated only once
        $this->assertEquals($actualResult, $this->_model->getSchema());
    }

    public function testGetPerFileSchema()
    {
        $actualResult = $this->_model->getPerFileSchema();
        $this->assertEquals('fixture_dir/email_templates.xsd', $actualResult);
        // Makes sure the value is calculated only once
        $this->assertEquals($actualResult, $this->_model->getPerFileSchema());
    }
}
