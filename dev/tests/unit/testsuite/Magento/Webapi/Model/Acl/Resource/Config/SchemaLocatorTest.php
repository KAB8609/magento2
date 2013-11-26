<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Webapi\Model\Acl\Resource\Config;

class SchemaLocatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_moduleReaderMock;

    /**
     * @var \Magento\Webapi\Model\Acl\Resource\Config\SchemaLocator
     */
    protected $_model;

    protected function setUp()
    {
        $this->_moduleReaderMock = $this->getMock(
            'Magento\Module\Dir\Reader', array(), array(), '', false
        );
        $this->_moduleReaderMock->expects($this->once())
            ->method('getModuleDir')->with('etc', 'Magento_Webapi')->will($this->returnValue('schema_dir'));
        $this->_model = new \Magento\Webapi\Model\Acl\Resource\Config\SchemaLocator($this->_moduleReaderMock);
    }

    public function testGetSchema()
    {
        $this->assertEquals('schema_dir/acl.xsd', $this->_model->getSchema());
    }

    public function testGetPerFileSchema()
    {
        $this->assertEquals(null, $this->_model->getPerFileSchema());
    }
}
