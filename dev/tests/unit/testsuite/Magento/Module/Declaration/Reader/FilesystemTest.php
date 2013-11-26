<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Module\Declaration\Reader;

class FilesystemTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Module\Declaration\Reader\Filesystem
     */
    protected $_model;

    protected function setUp()
    {
        $baseDir = __DIR__ . '/../FileResolver/_files';
        $applicationDirs = $this->getMock('Magento\App\Dir', array(), array('getDir'), '', false);
        $applicationDirs->expects($this->any())->method('getDir')
            ->will($this->returnValueMap(array(
                array(
                    \Magento\App\Dir::CONFIG,
                    $baseDir . '/app/etc',
                ),
                array(
                    \Magento\App\Dir::MODULES,
                        $baseDir . '/app/code',
                ),
            )));
        $fileResolver = new \Magento\Module\Declaration\FileResolver($applicationDirs);
        $converter = new \Magento\Module\Declaration\Converter\Dom();
        $schemaLocatorMock = $this->getMock(
            'Magento\Module\Declaration\SchemaLocator', array(), array(), '', false
        );
        $validationStateMock = $this->getMock('Magento\Config\ValidationStateInterface');
        $this->_model = new \Magento\Module\Declaration\Reader\Filesystem(
            $fileResolver, $converter, $schemaLocatorMock, $validationStateMock
        );
    }

    public function testRead()
    {
        $expectedResult = array(
            'Module_One' => array(
                'name' => 'Module_One',
                'version' => '1.0.0.0',
                'active' => true,
                'dependencies' => array(
                    'modules' => array(),
                    'extensions' => array(
                        'strict' => array(
                            array('name' => 'simplexml'),
                        ),
                        'alternatives' => array(array(
                            array('name' => 'gd'),
                            array('name' => 'imagick', 'minVersion' => '3.0.0'),
                        )),
                    ),
                ),
            ),
            'Module_Four' => array(
                'name' => 'Module_Four',
                'version' => '1.0.0.0',
                'active' => true,
                'dependencies' => array(
                    'modules' => array('Module_One'),
                    'extensions' => array(
                        'strict' => array(),
                        'alternatives' => array(),
                    ),
                ),
            ),
            'Module_Three' => array(
                'name' => 'Module_Three',
                'version' => '1.0.0.0',
                'active' => true,
                'dependencies' => array(
                    'modules' => array('Module_Four'),
                    'extensions' => array(
                        'strict' => array(),
                        'alternatives' => array(),
                    ),
                ),
            ),
        );
        $this->assertEquals($expectedResult, $this->_model->read('global'));
    }
}
