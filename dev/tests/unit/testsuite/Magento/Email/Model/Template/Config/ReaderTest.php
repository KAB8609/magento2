<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Email\Model\Template\Config;

class ReaderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Email\Model\Template\Config\Reader
     */
    protected $_model;

    /**
     * @var \Magento\Catalog\Model\Attribute\Config\Converter|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_converter;

    /**
     * @var \Magento\Module\Dir\ReverseResolver|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_moduleDirResolver;

    protected function setUp()
    {
        $fileResolver = $this->getMock(
            'Magento\Email\Model\Template\Config\FileResolver',
            array(),
            array(),
            '',
            false
        );
        $paths = array(
            __DIR__ . '/_files/Fixture/ModuleOne/etc/email_templates_one.xml',
            __DIR__ . '/_files/Fixture/ModuleTwo/etc/email_templates_two.xml',
        );


        $this->_converter = $this->getMock('Magento\Email\Model\Template\Config\Converter', array('convert'));

        $moduleReader = $this->getMock(
            'Magento\Module\Dir\Reader', array('getModuleDir'), array(), '', false
        );
        $moduleReader
            ->expects($this->once())
            ->method('getModuleDir')->with('etc', 'Magento_Email')
            ->will($this->returnValue('stub'))
        ;
        $schemaLocator = new \Magento\Email\Model\Template\Config\SchemaLocator($moduleReader);

        $validationState = $this->getMock('Magento\Config\ValidationStateInterface');
        $validationState->expects($this->once())->method('isValidated')->will($this->returnValue(false));

        $this->_moduleDirResolver = $this->getMock(
            'Magento\Module\Dir\ReverseResolver', array(), array(), '', false
        );
        $filesystemDirectoryMock = $this->getMock('\Magento\Filesystem\Directory\Read', array(), array(), '', false);

        $filesystemDirectoryMock->expects($this->any())
            ->method('readFile')
            ->will($this->returnValueMap(array(
                array($paths[0], file_get_contents($paths[0])),
                array($paths[1], file_get_contents($paths[1]))
            )));
        $filesystemDirectoryMock->expects($this->any())->method('getAbsolutePath')->will($this->returnArgument(0));

        $fileIterator = new \Magento\Email\Model\Template\Config\FileIterator(
            $filesystemDirectoryMock,
            $this->_moduleDirResolver,
            $paths
        );
        $fileResolver->expects($this->once())
            ->method('get')
            ->with('email_templates.xml', 'scope')
            ->will($this->returnValue($fileIterator)
        );

        $this->_model = new \Magento\Email\Model\Template\Config\Reader(
            $fileResolver,
            $this->_converter,
            $schemaLocator,
            $validationState,
            $this->_moduleDirResolver
        );
    }

    public function testRead()
    {
        $this->_moduleDirResolver
            ->expects($this->at(0))
            ->method('getModuleName')
            ->with(__DIR__ . '/_files/Fixture/ModuleOne/etc/email_templates_one.xml')
            ->will($this->returnValue('Fixture_ModuleOne'))
        ;
        $this->_moduleDirResolver
            ->expects($this->at(1))
            ->method('getModuleName')
            ->with(__DIR__ . '/_files/Fixture/ModuleTwo/etc/email_templates_two.xml')
            ->will($this->returnValue('Fixture_ModuleTwo'))
        ;
        $constraint = function (\DOMDocument $actual) {
            try {
                $expected = file_get_contents(__DIR__ . '/_files/email_templates_merged.xml');
                \PHPUnit_Framework_Assert::assertXmlStringEqualsXmlString($expected, $actual->saveXML());
                return true;
            } catch (\PHPUnit_Framework_AssertionFailedError $e) {
                return false;
            }
        };
        $expectedResult = new \stdClass();
        $this->_converter
            ->expects($this->once())
            ->method('convert')
            ->with($this->callback($constraint))
            ->will($this->returnValue($expectedResult))
        ;

        $this->assertSame($expectedResult, $this->_model->read('scope'));
    }

    /**
     * @expectedException \UnexpectedValueException
     * @expectedExceptionMessage Unable to determine a module
     */
    public function testReadUnknownModule()
    {
        $this->_moduleDirResolver->expects($this->once())->method('getModuleName')->will($this->returnValue(null));
        $this->_converter->expects($this->never())->method('convert');
        $this->_model->read('scope');
    }
}
