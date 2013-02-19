<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Core
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test theme model
 */
class Mage_Core_Model_Theme_ImageTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Core_Model_Theme_Image
     */
    protected $_model;

    /**
     * @var Magento_ObjectManager
     */
    protected $_objectManager;

    /**
     * @var Magento_Filesystem
     */
    protected $_filesystem;

    /**
     * @var Mage_Core_Helper_Data
     */
    protected $_helper;

    protected function setUp()
    {
        $this->_objectManager = $this->getMock('Magento_ObjectManager', get_class_methods('Magento_ObjectManager'),
            array(), '', false);
        $this->_helper = $this->getMock('Mage_Core_Helper_Data', array(), array(), '', false);
        $this->_filesystem = $this->getMock('Magento_Filesystem', array(), array(), '', false);
        $this->_model = new Mage_Core_Model_Theme_Image($this->_objectManager, $this->_helper, $this->_filesystem);
        $this->_model->setTheme($this->getMock('Mage_Core_Model_Theme', array(), array(), '', false));
    }

    /**
     * @return PHPUnit_Framework_MockObject_MockObject|Mage_Core_Model_Design_Package
     */
    protected function _getDesignMock()
    {
        $designMock = $this->getMock('Mage_Core_Model_Design_Package', array('getViewFileUrl', 'getPublicDir'),
            array(), '', false);
        $designMock->expects($this->any())
            ->method('getPublicDir')
            ->will($this->returnValue('pub/media/theme'));
        $this->_objectManager->expects($this->any())
            ->method('get')
            ->with($this->equalTo('Mage_Core_Model_Design_Package'))
            ->will($this->returnValue($designMock));
        return $designMock;
    }

    /**
     * @return PHPUnit_Framework_MockObject_MockObject|Mage_Core_Model_Dir
     */
    protected function _getDirMock()
    {
        $dirMock = $this->getMock('Mage_Core_Model_Dir', array('getDir'), array(), '', false);
        $dirMock->expects($this->any())
            ->method('getDir')
            ->with($this->equalTo(Mage_Core_Model_Dir::THEME))
            ->will($this->returnValue('pub/media/theme'));
        $this->_objectManager->expects($this->any())
            ->method('get')
            ->with($this->equalTo('Mage_Core_Model_Dir'))
            ->will($this->returnValue($dirMock));
        return $dirMock;
    }

    public function testSavePreviewImage()
    {
        $this->_model->setTheme($this->getMock('Mage_Core_Model_Theme', array(), array(), '', false));
        $this->assertInstanceOf('Mage_Core_Model_Theme_Image', $this->_model->savePreviewImage());
    }

    public function testGetImagePathOrigin()
    {
        $dirMock = $this->_getDirMock();
        $expectedResult = $dirMock->getDir(Mage_Core_Model_Dir::THEME) . DIRECTORY_SEPARATOR
            . Mage_Core_Model_Theme_Image::IMAGE_DIR_ORIGIN;

        $this->assertEquals($expectedResult, $this->_model->getImagePathOrigin());
    }

    public function testCreatePreviewImageCopy()
    {
        $dirMock = $this->_getDirMock();
        $filePath = $dirMock->getDir(Mage_Core_Model_Dir::THEME) . DIRECTORY_SEPARATOR
            . Mage_Core_Model_Theme_Image::IMAGE_DIR_PREVIEW;
        $fileName = $filePath . DIRECTORY_SEPARATOR . 'image.jpg';

        $this->_filesystem->expects($this->any())
            ->method('copy')
            ->with($this->equalTo($fileName), $this->equalTo($fileName))
            ->will($this->returnValue(true));

        $themeMock = $this->getMock('Mage_Core_Model_Theme', array('getPreviewImage'), array(), '', false);
        $themeMock->expects($this->any())
            ->method('getPreviewImage')
            ->will($this->returnValue('image.jpg'));

        $this->_model->setTheme($themeMock);

        $this->assertInstanceOf('Mage_Core_Model_Theme_Image', $this->_model->createPreviewImageCopy());
        $this->assertEquals('image.jpg', $this->_model->getPreviewImage());
    }

    /**
     * @param string $previewImage
     * @param string $defaultImage
     * @param string $expectedResult
     * @dataProvider getPreviewImageUrlProvider
     */
    public function testGetPreviewImageUrl($previewImage, $defaultImage, $expectedResult)
    {
        if (null === $previewImage) {
            $designMock = $this->_getDesignMock();
            $designMock->expects($this->any())
                ->method('getViewFileUrl')
                ->with($this->equalTo($defaultImage))
                ->will($this->returnArgument(0));
        } else {
            $storeMock = $this->getMock('Mage_Core_Model_Store', array('getBaseUrl'), array(), '', false);
            $storeMock->expects($this->atLeastOnce())
                ->method('getBaseUrl')
                ->with($this->equalTo(Mage_Core_Model_Store::URL_TYPE_MEDIA))
                ->will($this->returnArgument(0));

            $appMock = $this->getMock('Mage_Core_Model_App', array('getStore'), array(), '', false);
            $appMock->expects($this->atLeastOnce())
                ->method('getStore')
                ->will($this->returnValue($storeMock));

            $this->_objectManager->expects($this->any())
                ->method('get')
                ->with($this->equalTo('Mage_Core_Model_App'))
                ->will($this->returnValue($appMock));

            $themeMock = $this->getMock('Mage_Core_Model_Theme', array('getPreviewImage'), array(), '', false);
            $themeMock->expects($this->any())
                ->method('getPreviewImage')
                ->will($this->returnValue($previewImage));
            $this->_model->setTheme($themeMock);
        }
        $this->assertEquals($expectedResult, $this->_model->getPreviewImageUrl());
    }

    /**
     * @return array
     */
    public function getPreviewImageUrlProvider()
    {
        return array(
            array(
                null,
                'Mage_Core::theme/default_preview.jpg',
                'Mage_Core::theme/default_preview.jpg',
            ),
            array(
                'Mage_Core::theme/default_preview.jpg',
                null,
                'mediatheme/preview/Mage_Core::theme/default_preview.jpg',
            ),
        );
    }
}
