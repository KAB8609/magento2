<?php
/**
 * Test for \Magento\Filesystem\Stream\Local
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Filesystem\Directory;

use Magento\TestFramework\Helper\Bootstrap;

/**
 * Class ReadTest
 * Test for Magento\Filesystem\Directory\Read class
 * @package Magento\Filesystem\Directory
 */
class ReadTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test instance of Read
     */
    public function testInstance()
    {
        $dir = $this->getDirectoryInstance('foo');
        $this->assertTrue($dir instanceof ReadInterface);
    }

    /**
     * Test for getAbsolutePath method
     */
    public function testGetAbsolutePath()
    {
        $dir = $this->getDirectoryInstance('foo');
        $this->assertContains(
            '../_files/foo/bar',
            $dir->getAbsolutePath('bar')
        );
    }

    /**
     * Test for read method
     *
     * @dataProvider readProvider
     * @param string $dirPath
     * @param string $readPath
     * @param array $expectedResult
     */
    public function testRead($dirPath, $readPath, $expectedResult)
    {
        $dir = $this->getDirectoryInstance($dirPath);
        $result = $dir->read($readPath);
        foreach ($expectedResult as $path) {
            $this->assertTrue(in_array($path, $result));
        }
    }

    /**
     * Data provider for testRead
     *
     * @return array
     */
    public function readProvider()
    {
        return array(
            array('foo', null, array('bar', 'file_three.txt')),
            array('foo/bar', null, array('baz', 'file_two.txt')),
            array('foo', 'bar', array('bar/baz', 'bar/file_two.txt'))
        );
    }

    /**
     * Test for search method
     *
     * @dataProvider searchProvider
     * @param string $dirPath
     * @param string $pattern
     * @param array $expectedResult
     */
    public function testSearch($dirPath, $pattern, $expectedResult)
    {
        $dir = $this->getDirectoryInstance($dirPath);
        $result = $dir->search($pattern);
        foreach ($expectedResult as $path) {
            $this->assertTrue(in_array($path, $result));
        }
    }

    /**
     * Data provider for testSearch
     *
     * @return array
     */
    public function searchProvider()
    {
        return array(
            array('foo', '/bar/', array('bar/baz/file_one.txt', 'bar/file_two.txt')),
            array('foo', '/\.txt/', array('bar/baz/file_one.txt', 'bar/file_two.txt', 'file_three.txt')),
            array('foo', '/notfound/', array())
        );
    }

    /**
     * Test for isExist method
     *
     * @dataProvider existsProvider
     * @param string $dirPath
     * @param string $path
     * @param bool $exists
     */
    public function testIsExist($dirPath, $path, $exists)
    {
        $dir = $this->getDirectoryInstance($dirPath);
        $this->assertEquals($exists, $dir->isExist($path));
    }

    /**
     * Data provider for testIsExist
     *
     * @return array
     */
    public function existsProvider()
    {
        return array(
            array('foo', 'bar', true),
            array('foo', 'bar/baz/', true),
            array('foo', 'bar/notexists', false)
        );
    }

    /**
     * Test for stat method
     *
     * @dataProvider statProvider
     * @param string $dirPath
     * @param string $path
     */
    public function testStat($dirPath, $path)
    {
        $dir = $this->getDirectoryInstance($dirPath);
        $expectedInfo =  array(
            'dev', 'ino', 'mode', 'nlink', 'uid',
            'gid', 'rdev', 'size', 'atime',
            'mtime', 'ctime', 'blksize', 'blocks'
        );
        $result = $dir->stat($path);
        foreach ($expectedInfo as $key) {
            $this->assertTrue(array_key_exists($key, $result));
        }
    }

    /**
     * Data provider for testStat
     *
     * @return array
     */
    public function statProvider()
    {
        return array(
            array('foo', 'bar'),
            array('foo', 'file_three.txt')
        );
    }

    /**
     * Test for isReadable method
     *
     * @dataProvider isReadbaleProvider
     * @param string $dirPath
     * @param string $path
     * @param bool $readable
     */
    public function testIsReadable($dirPath, $path, $readable)
    {
        $dir = $this->getDirectoryInstance($dirPath);
        $this->assertEquals($readable, $dir->isReadable($path));
    }

    /**
     * Data provider for testIsReadable
     *
     * @return array
     */
    public function isReadbaleProvider()
    {
        return array(
            array('foo', 'bar', true),
            array('foo', 'file_three.txt', true)
        );
    }

    /**
     * Test for openFile method
     */
    public function testOpenFile()
    {
        $file = $this->getDirectoryInstance('foo')->openFile('file_three.txt');
        $file->close();
        $this->assertTrue($file instanceof \Magento\Filesystem\File\ReadInterface);
    }

    /**
     * Get readable file instance
     * Get full path for files located in _files directory
     *
     * @param string $path
     * @return Read
     */
    private function getDirectoryInstance($path)
    {
        $fullPath = __DIR__ . '/../_files/' . $path;
        $readFactory = Bootstrap::getObjectManager()->create(
            'Magento\Filesystem\File\ReadFactory', array('path' => $fullPath)
        );
        return new Read($fullPath, $readFactory);
    }
}