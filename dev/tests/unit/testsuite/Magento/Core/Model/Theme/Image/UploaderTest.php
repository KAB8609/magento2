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

/**
 * Test for theme image uploader
 */
class Magento_Core_Model_Theme_Image_UploaderTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Core\Model\Theme\Image\Uploader|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_model;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_helperMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_filesystemMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_transferAdapterMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_fileUploader;

    protected function setUp()
    {
        $this->_filesystemMock = $this->getMock('Magento\Filesystem', array(), array(), '', false);
        $this->_transferAdapterMock = $this->getMock('Zend_File_Transfer_Adapter_Http', array(), array(), '', false);
        $this->_fileUploader = $this->getMock('Magento\File\Uploader', array(), array(), '', false);

        $uploaderFactory = $this->getMock('Magento\File\UploaderFactory', array('create'), array(), '', false);
        $uploaderFactory->expects($this->any())->method('create')->will($this->returnValue($this->_fileUploader));

        $this->_model = new \Magento\Core\Model\Theme\Image\Uploader(
            $this->_filesystemMock,
            $this->_transferAdapterMock,
            $uploaderFactory
        );
    }

    protected function tearDown()
    {
        $this->_model = null;
        $this->_transferAdapterMock = null;
        $this->_fileUploader = null;
    }

    /**
     * @covers \Magento\Core\Model\Theme\Image\Uploader::__construct
     */
    public function testCunstructor()
    {
        $this->assertNotEmpty(new \Magento\Core\Model\Theme\Image\Uploader(
            $this->getMock('Magento\Filesystem', array(), array(), '', false),
            $this->getMock('Zend_File_Transfer_Adapter_Http', array(), array(), '', false),
            $this->getMock('Magento\File\UploaderFactory', array('create'), array(), '', false)
        ));
    }

    /**
     * @return array
     */
    public function uploadDataProvider()
    {
        return array(
            array(
                'isUploaded'            => true,
                'isValid'               => true,
                'checkAllowedExtension' => true,
                'save'                  => true,
                'result'                => '/tmp' . DIRECTORY_SEPARATOR . 'test_filename',
                'exception'             => null
            ),
            array(
                'isUploaded'            => false,
                'isValid'               => true,
                'checkAllowedExtension' => true,
                'save'                  => true,
                'result'                => false,
                'exception'             => null
            ),
            array(
                'isUploaded'            => true,
                'isValid'               => false,
                'checkAllowedExtension' => true,
                'save'                  => true,
                'result'                => false,
                'exception'             => '\Magento\Core\Exception'
            ),
            array(
                'isUploaded'            => true,
                'isValid'               => true,
                'checkAllowedExtension' => false,
                'save'                  => true,
                'result'                => false,
                'exception'             => '\Magento\Core\Exception'
            ),
            array(
                'isUploaded'            => true,
                'isValid'               => true,
                'checkAllowedExtension' => true,
                'save'                  => false,
                'result'                => false,
                'exception'             => '\Magento\Core\Exception'
            ),
        );
    }

    /**
     * @dataProvider uploadDataProvider
     * @covers \Magento\Core\Model\Theme\Image\Uploader::uploadPreviewImage
     */
    public function testUploadPreviewImage($isUploaded, $isValid, $checkExtension, $save, $result, $exception)
    {
        if ($exception) {
            $this->setExpectedException($exception);
        }
        $testScope = 'scope';
        $this->_transferAdapterMock->expects($this->any())->method('isUploaded')->with($testScope)
            ->will($this->returnValue($isUploaded));
        $this->_transferAdapterMock->expects($this->any())->method('isValid')->with($testScope)
            ->will($this->returnValue($isValid));
        $this->_fileUploader->expects($this->any())->method('checkAllowedExtension')
            ->will($this->returnValue($checkExtension));
        $this->_fileUploader->expects($this->any())->method('save')
            ->will($this->returnValue($save));
        $this->_fileUploader->expects($this->any())->method('getUploadedFileName')
            ->will($this->returnValue('test_filename'));

        $this->assertEquals($result, $this->_model->uploadPreviewImage($testScope, '/tmp'));
    }
}
