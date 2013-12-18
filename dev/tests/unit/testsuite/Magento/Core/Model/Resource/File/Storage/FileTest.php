<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Core\Model\Resource\File\Storage;

/**
 * Class FileTest
 */
class FileTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Core\Model\File\Storage\File
     */
    protected $_model;

    /**
     * @var \Magento\Core\Helper\File\Media
     */
    protected $_loggerMock;

    /**
     * @var \Magento\Filesystem
     */
    protected $_filesystemMock;

    /**
     * @var \Magento\Filesystem\Directory\Read
     */
    protected $_directoryReadMock;

    protected function setUp()
    {
        $this->_loggerMock = $this->getMock('Magento\Logger', array(), array(), '', false);
        $this->_filesystemMock = $this->getMock('Magento\Filesystem', array('getDirectoryRead'), array(), '', false);
        $this->_directoryReadMock =
            $this->getMock('Magento\Filesystem\Directory\Read', array('isDirectory', 'read'), array(), '', false);
        $this->_directoryReadMock
            ->expects($this->any())
            ->method('isDirectory')
            ->will($this->returnValue(true));
        $this->_directoryReadMock
            ->expects($this->any())
            ->method('read')
            ->with('pub')
            ->will($this->returnValue(array(
                'media/customer',
                'media/downloadable',
                'media/theme',
                'media/theme_customization',
                'media')
            ));
        $this->_filesystemMock
            ->expects($this->any())
            ->method('getDirectoryRead')
            ->with('media')
            ->will($this->returnValue($this->_directoryReadMock));
        $this->_model = new \Magento\Core\Model\Resource\File\Storage\File(
            $this->_filesystemMock,
            $this->_loggerMock
        );
    }

    protected function tearDown()
    {
        unset($this->_model);
    }

    public function testGetStorageData()
    {
        $directories = array(
            array('name' => 'customer', 'path' => 'media'),
            array('name' => 'downloadable', 'path' => 'media'),
            array('name' => 'theme', 'path' => 'media'),
            array('name' => 'theme_customization', 'path' => 'media'),
            array('name' => 'media', 'path' => '/'),

        );
        $expected = array('files' => array(), 'directories' => $directories);
        $actual = $this->_model->getStorageData('pub');
        $this->assertEquals($expected, $actual);
    }
}
