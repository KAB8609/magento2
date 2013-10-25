<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\App\Module\Declaration\Reader;

class FilesystemTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\App\Module\Declaration\Reader\Filesystem
     */
    protected $_model;

    protected function setUp()
    {
        $baseDir = __DIR__ . str_replace('/', DIRECTORY_SEPARATOR, '/../FileResolver/_files');
        $applicationDirs = $this->getMock('Magento\App\Dir', array(), array('getDir'), '', false);
        $applicationDirs->expects($this->any())->method('getDir')
            ->will($this->returnValueMap(array(
                array(
                    \Magento\App\Dir::CONFIG,
                    $baseDir . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR .'etc',
                ),
                array(
                    \Magento\App\Dir::MODULES,
                        $baseDir . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR .'code',
                ),
            )));
        $fileResolver = new \Magento\App\Module\Declaration\FileResolver($applicationDirs);
        $converter = new \Magento\App\Module\Declaration\Converter\Dom();
        $schemaLocatorMock = $this->getMock(
            'Magento\App\Module\Declaration\SchemaLocator', array(), array(), '', false
        );
        $validationStateMock = $this->getMock('Magento\Config\ValidationStateInterface');
        $this->_model = new \Magento\App\Module\Declaration\Reader\Filesystem(
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
