<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Theme Image model class
 */
class Mage_Core_Model_Theme_Image
{
    /**
     * Preview image width
     */
    const PREVIEW_IMAGE_WIDTH = 800;

    /**
     * Preview image height
     */
    const PREVIEW_IMAGE_HEIGHT = 800;

    /**
     * @var Magento_Filesystem
     */
    protected $_filesystem;

    /**
     * @var Mage_Core_Model_Image_Factory
     */
    protected $_imageFactory;

    /**
     * @var Mage_Core_Model_Theme_Image_UploaderProxy
     */
    protected $_uploader;

    /**
     * @var Mage_Core_Model_Theme_Image_Path
     */
    protected $_themeImagePath;

    /**
     * @var Mage_Core_Model_Logger
     */
    protected $_logger;

    /**
     * @var Mage_Core_Model_Theme
     */
    protected $_theme;

    /**
     * Initialize dependencies
     *
     * @param Magento_Filesystem $filesystem
     * @param Mage_Core_Model_Image_Factory $imageFactory
     * @param Mage_Core_Model_Theme_Image_UploaderProxy $uploader
     * @param Mage_Core_Model_Theme_Image_Path $themeImagePath
     * @param Mage_Core_Model_Logger $logger
     * @param Mage_Core_Model_Theme $theme
     */
    public function __construct(
        Magento_Filesystem $filesystem,
        Mage_Core_Model_Image_Factory $imageFactory,
        Mage_Core_Model_Theme_Image_UploaderProxy $uploader,
        Mage_Core_Model_Theme_Image_Path $themeImagePath,
        Mage_Core_Model_Logger $logger,
        Mage_Core_Model_Theme $theme = null
    ) {
        $this->_filesystem = $filesystem;
        $this->_imageFactory = $imageFactory;
        $this->_uploader = $uploader;
        $this->_themeImagePath = $themeImagePath;
        $this->_logger = $logger;
        $this->_theme = $theme;
    }

    /**
     * Create preview image
     *
     * @param string $imagePath
     * @return $this
     */
    public function createPreviewImage($imagePath)
    {
        $image = $this->_imageFactory->create($imagePath);
        $image->keepTransparency(true);
        $image->constrainOnly(true);
        $image->keepFrame(true);
        $image->keepAspectRatio(true);
        $image->backgroundColor(array(255, 255, 255));
        $image->resize(self::PREVIEW_IMAGE_WIDTH, self::PREVIEW_IMAGE_HEIGHT);

        $imageName = uniqid('preview_image_') . image_type_to_extension($image->getMimeType());
        $image->save($this->_themeImagePath->getImagePreviewDirectory(), $imageName);
        $this->_theme->setPreviewImage($imageName);
        return $this;
    }

    /**
     * Create preview image duplicate
     *
     * @return $this
     */
    public function createPreviewImageCopy()
    {
        $previewDir = $this->_themeImagePath->getImagePreviewDirectory();
        $destinationFileName = Varien_File_Uploader::getNewFileName(
            $previewDir . DIRECTORY_SEPARATOR . $this->_theme->getPreviewImage()
        );
        try {
            $this->_filesystem->copy(
                $previewDir . DIRECTORY_SEPARATOR . $this->_theme->getPreviewImage(),
                $previewDir . DIRECTORY_SEPARATOR . $destinationFileName
            );
        } catch (Exception $e) {
            $this->_logger->logException($e);
        }
        $this->_theme->setPreviewImage($destinationFileName);
        return $this;
    }

    /**
     * Delete preview image
     *
     * @return bool
     */
    public function removePreviewImage()
    {
        $previewImage = $this->_theme->getPreviewImage();
        $this->_theme->setPreviewImage(null);
        if ($previewImage) {
            return $this->_filesystem->delete(
                $this->_themeImagePath->getImagePreviewDirectory() . DIRECTORY_SEPARATOR . $previewImage
            );
        }
        return false;
    }

    /**
     * Upload and create preview image
     *
     * @param string $scope the request key for file
     * @return $this
     */
    public function uploadPreviewImage($scope)
    {
        if ($this->_theme->getPreviewImage()) {
            $this->removePreviewImage();
        }
        $tmpDirPath = $this->_themeImagePath->getTemporaryDirectory();
        $tmpFilePath = $this->_uploader->uploadPreviewImage($scope, $tmpDirPath);
        if ($tmpFilePath) {
            $this->createPreviewImage($tmpFilePath);
            $this->_filesystem->delete($tmpFilePath);
        }
        return $this;
    }

    /**
     * Get url for themes preview image
     *
     * @return string
     */
    public function getPreviewImageUrl()
    {
        $previewImage = $this->_theme->getPreviewImage();
        if ($previewImage) {
            return $this->_themeImagePath->getPreviewImageDirectoryUrl() . $previewImage;
        }
        return $this->_themeImagePath->getPreviewImageDefaultUrl();
    }
}
