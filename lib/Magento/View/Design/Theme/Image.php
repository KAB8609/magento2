<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Design\Theme;

/**
 * Theme Image model class
 */
class Image
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
     * @var \Magento\Filesystem
     */
    protected $_filesystem;

    /**
     * @var \Magento\Image\Factory
     */
    protected $_imageFactory;

    /**
     * @var Image\Uploader
     */
    protected $_uploader;

    /**
     * @var Image\PathInterface
     */
    protected $_themeImagePath;

    /**
     * @var \Magento\Logger
     */
    protected $_logger;

    /**
     * @var \Magento\View\Design\ThemeInterface
     */
    protected $_theme;

    /**
     * @param \Magento\Filesystem $filesystem
     * @param \Magento\Image\Factory $imageFactory
     * @param Image\Uploader $uploader
     * @param Image\PathInterface $themeImagePath
     * @param \Magento\Logger $logger
     * @param \Magento\View\Design\ThemeInterface $theme
     */
    public function __construct(
        \Magento\Filesystem $filesystem,
        \Magento\Image\Factory $imageFactory,
        Image\Uploader $uploader,
        Image\PathInterface $themeImagePath,
        \Magento\Logger $logger,
        \Magento\View\Design\ThemeInterface $theme = null
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
     * @return Image
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
     * @param string $previewImagePath
     * @return bool
     */
    public function createPreviewImageCopy($previewImagePath)
    {
        $previewDir = $this->_themeImagePath->getImagePreviewDirectory();
        $destinationFilePath = $previewDir . DIRECTORY_SEPARATOR . $previewImagePath;
        if (empty($previewImagePath) && !$this->_filesystem->has($destinationFilePath)) {
            return false;
        }

        $isCopied = false;
        try {
            $destinationFileName = \Magento\File\Uploader::getNewFileName($destinationFilePath);
            $isCopied = $this->_filesystem->copy(
                $destinationFilePath,
                $previewDir . DIRECTORY_SEPARATOR . $destinationFileName
            );
            $this->_theme->setPreviewImage($destinationFileName);
        } catch (\Exception $e) {
            $this->_logger->logException($e);
        }
        return $isCopied;
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
     * @return Image
     */
    public function uploadPreviewImage($scope)
    {
        $tmpDirPath = $this->_themeImagePath->getTemporaryDirectory();
        $tmpFilePath = $this->_uploader->uploadPreviewImage($scope, $tmpDirPath);
        if ($tmpFilePath) {
            if ($this->_theme->getPreviewImage()) {
                $this->removePreviewImage();
            }
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