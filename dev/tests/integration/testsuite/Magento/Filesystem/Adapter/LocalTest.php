<?php
/**
 * Test for Magento_Filesystem_Adapter_Local
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Filesystem_Adapter_LocalTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Filesystem_Adapter_Local
     */
    protected $_adapter;

    /**
     * @var string
     */
    protected $_filePath;

    /**
     * @var array
     */
    protected $_deleteFiles = array();

    protected function setUp()
    {
        $this->_adapter = new Magento_Filesystem_Adapter_Local();
    }

    protected function tearDown()
    {
        foreach ($this->_deleteFiles as $fileName) {
            if (is_dir($fileName)) {
                rmdir($fileName);
            } elseif (is_file($fileName)) {
                unlink($fileName);
            }
        }
    }

    /**
     * @return string
     */
    protected function _getFixturesPath()
    {
        return __DIR__ . DS . '..' . DS . '_files' . DS;
    }

    /**
     * @param string $key
     * @param bool $expected
     * @dataProvider existsDataProvider
     */
    public function testExists($key, $expected)
    {
        $this->assertEquals($expected, $this->_adapter->exists($key));
    }

    /**
     * @return array
     */
    public function existsDataProvider()
    {
        return array(
            'existed file' => array($this->_getFixturesPath() . 'popup.csv', true),
            'not existed file' => array($this->_getFixturesPath() . 'popup2.css', false),
        );
    }

    /**
     * @param string $fileName
     * @param string $expectedContent
     * @dataProvider readDataProvider
     */
    public function testRead($fileName, $expectedContent)
    {
        $this->assertEquals($expectedContent, $this->_adapter->read($fileName));
    }

    /**
     * @return array
     */
    public function readDataProvider()
    {
        return array(
            'read' => array($this->_getFixturesPath() . 'popup.csv', 'var myData = 5;'),
        );
    }

    /**
     * @param string $fileName
     * @param string $fileData
     * @dataProvider writeDataProvider
     */
    public function testWrite($fileName, $fileData)
    {
        $this->_deleteFiles = array($fileName);
        $this->_adapter->write($fileName, $fileData);
        $this->assertFileExists($fileName);
        $this->assertEquals(file_get_contents($fileName), $fileData);
    }

    /**
     * @return array
     */
    public function writeDataProvider()
    {
        return array(
            'correct file' => array($this->_getFixturesPath() . 'tempFile.css', 'temporary data'),
            'empty file' => array($this->_getFixturesPath() . 'tempFile2.css', '')
        );
    }

    public function testDeleteNotExists()
    {
        $fileName = $this->_getFixturesPath() . 'tempFile3.css';
        $this->_adapter->delete($fileName);
        $this->assertFileNotExists($fileName);
    }

    public function testDeleteDir()
    {
        $fileName = $this->_getFixturesPath() . 'new_directory' . DS . 'tempFile3.css';
        $dirName = $this->_getFixturesPath() . 'new_directory';
        $this->_deleteFiles[] = $fileName;
        $this->_deleteFiles[] = $dirName;
        mkdir($dirName, 0755);
        file_put_contents($fileName, 'test data');
        $this->_adapter->delete($dirName);
        $this->assertFileNotExists($dirName);
        $this->assertFileNotExists($fileName);
    }

    public function testDelete()
    {
        $fileName = $this->_getFixturesPath() . 'tempFile3.css';
        $this->_deleteFiles = array($fileName);
        file_put_contents($fileName, 'test data');
        $this->_adapter->delete($fileName);
        $this->assertFileNotExists($fileName);
    }

    public function testChangePermissionsFile()
    {
        $fileName = $this->_getFixturesPath() . 'tempFile3.css';
        $this->_deleteFiles[] = $fileName;
        file_put_contents($fileName, 'test data');
        $this->_adapter->changePermissions($fileName, 0666, false);
        $this->assertEquals(0666, fileperms($fileName) & 0777);
    }

    public function testChangePermissionsDir()
    {
        $fileName = $this->_getFixturesPath() . 'new_directory2' . DS . 'tempFile3.css';
        $dirName = $this->_getFixturesPath() . 'new_directory2';
        $this->_deleteFiles[] = $fileName;
        $this->_deleteFiles[] = $dirName;
        mkdir($dirName, 0777);
        file_put_contents($fileName, 'test data');
        $this->_adapter->changePermissions($dirName, 0755, true);
        $this->assertEquals(0755, fileperms($dirName) & 0777);
        $this->assertEquals(0755, fileperms($fileName) & 0777);
    }

    public function testGetFileMd5()
    {
        $this->assertEquals('e5f30e10b8965645d5f8ed5999d88600',
            $this->_adapter->getFileMd5($this->_getFixturesPath() . 'popup.csv'));
    }

    /**
     * @expectedException Magento_Filesystem_Exception
     * @expectedExceptionMessage Unable to get file hash
     */
    public function testGetFileMd5Exception()
    {
        $this->_adapter->getFileMd5($this->_getFixturesPath() . 'invalid.csv');
    }

    public function testIsFile()
    {
        $this->assertTrue($this->_adapter->isFile($this->_getFixturesPath() . 'popup.csv'));
    }

    public function testIsWritable()
    {
        $this->assertTrue($this->_adapter->isWritable($this->_getFixturesPath() . 'popup.csv'));
    }

    public function testIsReadable()
    {
        $this->assertTrue($this->_adapter->isReadable($this->_getFixturesPath() . 'popup.csv'));
    }

    public function testCreateStream()
    {
        $stream = $this->_adapter->createStream($this->_getFixturesPath() . 'popup.csv');
        $this->assertInstanceOf('Magento_Filesystem_Stream_Local', $stream);
    }

    /**
     * @param string $sourceName
     * @param string $targetName
     * @throws Exception
     * @dataProvider renameDataProvider
     */
    public function testRename($sourceName, $targetName)
    {
        $this->_deleteFiles[] = $sourceName;
        $this->_deleteFiles[] = $targetName;
        file_put_contents($sourceName, 'test data');
        $this->_adapter->rename($sourceName, $targetName);
        $this->assertFileExists($targetName);
        $this->assertFileNotExists($sourceName);
        $this->assertEquals(file_get_contents($targetName), 'test data');
    }

    /**
     * @return array
     */
    public function renameDataProvider()
    {
        return array(
            'test 1' => array($this->_getFixturesPath() . 'file1.js', $this->_getFixturesPath() . 'file2.js'),
        );
    }

    public function testIsDirectory()
    {
        $this->assertTrue($this->_adapter->isDirectory($this->_getFixturesPath()));
        $this->assertFalse($this->_adapter->isDirectory($this->_getFixturesPath() . 'popup.csv'));
    }

    public function testCreateDirectory()
    {
        $directoryName = $this->_getFixturesPath() . 'new_directory';
        $this->_deleteFiles[] = $directoryName;
        $this->_adapter->createDirectory($directoryName, 0755);
        $this->assertFileExists($directoryName);
        $this->assertTrue(is_dir($directoryName));
    }

    /**
     *
     * @expectedException Magento_Filesystem_Exception
     */
    public function testCreateDirectoryError()
    {
        $this->_adapter->createDirectory('', 0755);
    }

    /**
     * @dataProvider touchDataProvider
     * @param string $fileName
     * @param bool $newFile
     */
    public function testTouch($fileName, $newFile = false)
    {
        if ($newFile) {
            $this->_deleteFiles = array($fileName);
        }
        if ($newFile) {
            $this->assertFileNotExists($fileName);
        } else {
            $this->assertFileExists($fileName);
        }
        $this->_adapter->touch($fileName);
        $this->assertFileExists($fileName);
    }

    /**
     * @return array
     */
    public function touchDataProvider()
    {
        return array(
            'update file' => array($this->_getFixturesPath() . 'popup.csv', false),
            'create file' => array($this->_getFixturesPath() . 'popup2.css', true)
        );
    }

    /**
     * @param string $sourceName
     * @param string $targetName
     * @dataProvider renameDataProvider
     */
    public function testCopy($sourceName, $targetName)
    {
        $this->_deleteFiles = array($sourceName, $targetName);
        $testData = 'test data';
        file_put_contents($sourceName, $testData);
        $this->_adapter->copy($sourceName, $targetName);
        $this->assertFileExists($targetName);
        $this->assertFileExists($sourceName);
        $this->assertEquals($testData, file_get_contents($targetName));
        $this->assertEquals($testData, file_get_contents($targetName));
    }

    public function testGetMTime()
    {
        $filePath = $this->_getFixturesPath() . 'mtime.txt';
        $this->_adapter->write($filePath, 'Test');
        $this->assertFileExists($filePath);
        $this->assertGreaterThan(0, $this->_adapter->getMTime($filePath));
    }
}
