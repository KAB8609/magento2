<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Core_Model_DirTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Test_Helper_FileSystem
     */
    protected $_filesystemHelper;

    public function setUp()
    {
        $this->_filesystemHelper = new Magento_Test_Helper_FileSystem($this);
    }

    public function testGetWritableDirCodes()
    {
        $codes = Mage_Core_Model_Dir::getWritableDirCodes();
        $this->assertInternalType('array', $codes);
        $this->assertNotEmpty($codes);
        $dir = $this->_filesystemHelper->createDirInstance(__DIR__);
        foreach ($codes as $code) {
            $this->assertNotEmpty($dir->getDir($code));
        }
    }

    /**
     * @param string $code
     * @param string $value
     * @expectedException InvalidArgumentException
     * @dataProvider invalidUriDataProvider
     */
    public function testInvalidUri($code, $value)
    {
        $this->_filesystemHelper->createDirInstance(__DIR__, array($code => $value));
    }

    /**
     * @return array
     */
    public function invalidUriDataProvider()
    {
        return array(
            array(Mage_Core_Model_Dir::MEDIA, '/'),
            array(Mage_Core_Model_Dir::MEDIA, '//'),
            array(Mage_Core_Model_Dir::MEDIA, '/value'),
            array(Mage_Core_Model_Dir::MEDIA, 'value/'),
            array(Mage_Core_Model_Dir::MEDIA, '/value/'),
            array(Mage_Core_Model_Dir::MEDIA, 'one\\two'),
            array(Mage_Core_Model_Dir::MEDIA, '../dir'),
            array(Mage_Core_Model_Dir::MEDIA, './dir'),
            array(Mage_Core_Model_Dir::MEDIA, 'one/../two'),
        );
    }

    public function testGetUri()
    {
        $dir = $this->_filesystemHelper->createDirInstance(__DIR__, array(
            Mage_Core_Model_Dir::PUB   => '',
            Mage_Core_Model_Dir::MEDIA => 'test',
            'custom' => 'test2'
        ));

        // arbitrary custom value
        $this->assertEquals('test2', $dir->getUri('custom'));

        // setting empty value correctly adjusts its children
        $this->assertEquals('', $dir->getUri(Mage_Core_Model_Dir::PUB));
        $this->assertEquals('lib', $dir->getUri(Mage_Core_Model_Dir::PUB_LIB));

        // at the same time if another child has custom value, it must not be affected by its parent
        $this->assertEquals('test', $dir->getUri(Mage_Core_Model_Dir::MEDIA));
        $this->assertEquals('test/upload', $dir->getUri(Mage_Core_Model_Dir::UPLOAD));
    }

    public function testGetDir()
    {
        $newRoot = __DIR__ . DIRECTORY_SEPARATOR . 'root';
        $newMedia = __DIR__ . DIRECTORY_SEPARATOR . 'media';
        $dir = $this->_filesystemHelper->createDirInstance(__DIR__, array(), array(
            Mage_Core_Model_Dir::ROOT => $newRoot,
            Mage_Core_Model_Dir::MEDIA => $newMedia,
            'custom' => 'test2'
        ));

        // arbitrary custom value
        $this->assertEquals('test2', $dir->getDir('custom'));

        // new root has affected all its non-customized children
        $this->assertStringStartsWith($newRoot, $dir->getDir(Mage_Core_Model_Dir::APP));
        $this->assertStringStartsWith($newRoot, $dir->getDir(Mage_Core_Model_Dir::MODULES));

        // but it didn't affect the customized dirs
        $this->assertEquals($newMedia, $dir->getDir(Mage_Core_Model_Dir::MEDIA));
        $this->assertStringStartsWith($newMedia, $dir->getDir(Mage_Core_Model_Dir::UPLOAD));

        // uris should not be affected
        $default = $this->_filesystemHelper->createDirInstance(__DIR__);
        foreach (array(
            Mage_Core_Model_Dir::PUB,
            Mage_Core_Model_Dir::PUB_LIB,
            Mage_Core_Model_Dir::MEDIA,
            Mage_Core_Model_Dir::UPLOAD) as $code
        ) {
            $this->assertEquals($default->getUri($code), $dir->getUri($code));
        }
    }
}
