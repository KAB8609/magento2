<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Design\FileResolution\Strategy\Fallback;

use Magento\Filesystem;
use Magento\Io\File;
use Magento\TestFramework\Helper\ProxyTesting;

/**
 * CachingProxy Test
 *
 * @package Magento\View
 */
class CachingProxyTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Temp directory for the model to store maps
     *
     * @var string
     */
    protected $tmpDir;

    /**
     * Mock of the model to be tested. Operates the mocked fallback object.
     *
     * @var CachingProxy
     */
    protected $model;

    /**
     * Mocked fallback object, with file resolution methods ready to be substituted.
     *
     * @var \Magento\View\Design\FileResolution\Strategy\Fallback|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $fallback;

    /**
     * Theme model, pre-created in setUp() for usage in tests
     *
     * @var \Magento\View\Design\ThemeInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $themeModel;

    /**
     * Direcoty with write permissions
     *
     * @var \Magento\Filesystem\Directory\Write | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $directoryWrite;

    /**
     * Set up
     */
    protected function setUp()
    {
        $this->tmpDir = TESTS_TEMP_DIR . '/' . 'fallback';
        mkdir($this->tmpDir);

        $this->fallback = $this->getMock(
            'Magento\View\Design\FileResolution\Strategy\Fallback',
            array(),
            array(),
            '',
            false
        );

        $this->themeModel = \PHPUnit_Framework_MockObject_Generator::getMock(
            'Magento\Core\Model\Theme',
            array(),
            array(),
            '',
            false,
            false
        );
        $this->themeModel->expects($this->any())
            ->method('getId')
            ->will($this->returnValue('t'));

        $this->model = new CachingProxy(
            $this->fallback,
            $this->getFilesystemMock(),
            $this->tmpDir,
            TESTS_TEMP_DIR,
            true
        );
    }

    /**
     * Tear down
     */
    protected function tearDown()
    {
        File::rmdirRecursive($this->tmpDir);
    }

    /**
     * Construct CachingProxy passing not a directory
     *
     * @expectedException \InvalidArgumentException
     */
    public function testConstructInvalidDir()
    {
        $this->model = new CachingProxy(
            $this->fallback,
            $this->getFilesystemMock(false),
            $this->tmpDir,
            TESTS_TEMP_DIR . 'invalid_dir'
        );
    }

    /**
     * Test for __destruct method
     */
    public function testDestruct()
    {
        $this->fallback->expects($this->once())
            ->method('getFile')
            ->will($this->returnValue(TESTS_TEMP_DIR . '/' . 'test.txt'));

        $expectedFile = $this->tmpDir . '/a_t_.ser';

        $this->model->getFile('a', $this->themeModel, 'does not matter', 'Some_Module');
        $this->assertFileNotExists($expectedFile);
        unset($this->model);
        $this->directoryWrite->expects($this->any())
            ->method('writeFile')
            ->with($expectedFile, $this->contains('Some_Module'));
    }

    /**
     * Test for destruct method with canSaveMap = false
     */
    public function testDestructNoMapSaved()
    {
        $this->fallback->expects($this->once())
            ->method('getFile')
            ->will($this->returnValue(TESTS_TEMP_DIR . '/test.txt'));
        $model = new CachingProxy(
            $this->fallback,
            $this->getFilesystemMock(),
            $this->tmpDir,
            TESTS_TEMP_DIR,
            false
        );

        $model->getFile('a', $this->themeModel, 'does not matter', 'Some_Module');
        unset($model);
        $this->directoryWrite->expects($this->never())
            ->method('writeFile');
    }

    /**
     * Test for all proxy methods
     *
     * @param string $method
     * @param array $params
     * @param string $expectedResult
     * @dataProvider proxyMethodsDataProvider
     * @covers \Magento\View\Design\FileResolution\Strategy\Fallback\CachingProxy::getFile
     * @covers \Magento\View\Design\FileResolution\Strategy\Fallback\CachingProxy::getLocaleFile
     * @covers \Magento\View\Design\FileResolution\Strategy\Fallback\CachingProxy::getViewFile
     */
    public function testProxyMethods($method, $params, $expectedResult)
    {
        $helper = new ProxyTesting();
        $actualResult = $helper->invokeWithExpectations(
            $this->model,
            $this->fallback,
            $method,
            $params,
            $expectedResult
        );
        $this->assertEquals($expectedResult, $actualResult);
    }

    /**
     * Data provider for testProxyMethods
     *
     * @return array
     */
    public static function proxyMethodsDataProvider()
    {
        $themeModel = \PHPUnit_Framework_MockObject_Generator::getMock(
            'Magento\Core\Model\Theme',
            array(),
            array(),
            '',
            false,
            false
        );

        return array(
            'getFile' => array(
                'getFile',
                array('area51', $themeModel, 'file.txt', 'Some_Module'),
                TESTS_TEMP_DIR . '/fallback/file.txt',
            ),
            'getLocaleFile' => array(
                'getLocaleFile',
                array('area51', $themeModel, 'sq_AL', 'file.txt'),
                'path/to/locale_file.txt',
            ),
            'getViewFile' => array(
                'getViewFile',
                array('area51', $themeModel, 'uk_UA', 'file.txt', 'Some_Module'),
                'path/to/view_file.txt',
            ),
        );
    }

    /**
     * Test for setViewFilePathToMap method
     */
    public function testSetViewFilePathToMap()
    {
        $materializedFilePath = TESTS_TEMP_DIR . '/path/file.txt';

        $result = $this->model->setViewFilePathToMap(
            'area51',
            $this->themeModel,
            'en_US',
            'Some_Module',
            'file.txt',
            $materializedFilePath
        );
        $this->assertEquals($this->model, $result);

        $this->fallback->expects($this->never())
            ->method('getViewFile');
        $result = $this->model->getViewFile('area51', $this->themeModel, 'en_US', 'file.txt', 'Some_Module');
        $this->assertEquals($materializedFilePath, $result);
    }

    /**
     * Get Filesystem mock
     *
     * @param bool $isDirectory
     * @return Filesystem
     */
    protected function getFilesystemMock($isDirectory = true)
    {
        $directoryRead = $this->getMock('Magento\Filesystem\Directory\Read', array('isDirectory'), array(), '', false);
        $directoryRead->expects($this->once())
            ->method('isDirectory')
            ->will($this->returnValue($isDirectory));
        $this->directoryWrite = $this->getMock(
            'Magento\Filesystem\Directory\Write',
            array('getRelativePath','isFile', 'readFile', 'isDirectory', 'create', 'writeFile'),
            array(), '', false
        );
        $methods = array('getDirectoryRead', 'getDirectoryWrite', '__wakeup');
        $filesystem = $this->getMock('Magento\Filesystem', $methods, array(), '', false);
        $filesystem->expects($this->once())
            ->method('getDirectoryRead')
            ->with(\Magento\Filesystem::ROOT)
            ->will($this->returnValue($directoryRead));
        $filesystem->expects($this->once())
            ->method('getDirectoryWrite')
            ->with(\Magento\Filesystem::VAR_DIR)
            ->will($this->returnValue($this->directoryWrite));
        return $filesystem;
    }
}
