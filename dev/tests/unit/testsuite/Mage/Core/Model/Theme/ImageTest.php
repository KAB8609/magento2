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
        $this->_model = Mage::getObjectManager()->create('Mage_Core_Model_Theme_Image', array(
             'objectManager' => $this->_objectManager,
             'helper'   => $this->_helper,
             'filesystem' => $this->_filesystem,
        ));
    }

    /**
     * @return PHPUnit_Framework_MockObject_MockObject|Mage_Core_Model_Design_Package
     */
    protected function _getDesignMock()
    {
        $designMock = $this->getMock('Mage_Core_Model_Design_Package', array('getViewFileUrl'), array(), '', false);

        $this->_objectManager->expects($this->any())
            ->method('get')
            ->with($this->equalTo('Mage_Core_Model_Design_Package'))
            ->will($this->returnValue($designMock));
        return $designMock;
    }

    public function testSavePreviewImage()
    {
        $this->_model = $this->getMock('Mage_Core_Model_Theme_Image', array('createPreviewImage'), array(), '', false);
        $this->assertInstanceOf('Mage_Core_Model_Theme_Image', $this->_model->savePreviewImage());
    }

    public function testGetImagePathOrigin()
    {
        $designMock = $this->_getDesignMock();

        $expectedResult = $designMock->getPublicDir() . DIRECTORY_SEPARATOR
            . Mage_Core_Model_Theme_Image::IMAGE_DIR_ORIGIN;

        $this->assertEquals($expectedResult, $this->_model->getImagePathOrigin());
    }

    public function testCreatePreviewImageCopy()
    {
        $designMock = $this->_getDesignMock();
        $filePath = $designMock->getPublicDir() . DIRECTORY_SEPARATOR . Mage_Core_Model_Theme_Image::IMAGE_DIR_PREVIEW;
        $fileName = $filePath . DIRECTORY_SEPARATOR . 'image.jpg';

        $this->_filesystem->expects($this->any())
            ->method('copy')
            ->with($this->equalTo($fileName), $this->equalTo($fileName))
            ->will($this->returnValue(true));

        $this->_model->setPreviewImage('image.jpg');
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
                ->with($this->equalTo(Mage_Core_Model_Store::URL_TYPE_THEME))
                ->will($this->returnArgument(0));

            $appMock = $this->getMock('Mage_Core_Model_App', array('getStore'), array(), '', false);
            $appMock->expects($this->atLeastOnce())
                ->method('getStore')
                ->will($this->returnValue($storeMock));

            $this->_objectManager->expects($this->any())
                ->method('get')
                ->with($this->equalTo('Mage_Core_Model_App'))
                ->will($this->returnValue($appMock));

            $this->_model->setPreviewImage($previewImage);
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
                'themepreview/Mage_Core::theme/default_preview.jpg',
            ),
        );
    }
}
