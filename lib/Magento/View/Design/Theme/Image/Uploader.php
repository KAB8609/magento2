<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Theme Image Uploader
 */
namespace Magento\View\Design\Theme\Image;

class Uploader
{
    /**
     * Allowed file extensions to upload
     *
     * @var array
     */
    protected  $_allowedExtensions = array('jpg', 'jpeg', 'gif', 'png', 'xbm', 'wbmp');

    /**
     * @var \Magento\Filesystem
     */
    protected $_filesystem;

    /**
     * @var \Zend_File_Transfer_Adapter_Http
     */
    protected $_transferAdapter;

    /**
     * @var \Magento\File\UploaderFactory
     */
    protected $_uploaderFactory;


    /**
     * @param \Magento\Filesystem $filesystem
     * @param \Magento\HTTP\Adapter\FileTransferFactory $transferAdapterFactory
     * @param \Magento\File\UploaderFactory $uploaderFactory
     */
    public function __construct(
        \Magento\Filesystem $filesystem,
        \Magento\HTTP\Adapter\FileTransferFactory $transferAdapterFactory,
        \Magento\File\UploaderFactory $uploaderFactory
    ) {
        $this->_filesystem = $filesystem;
        $this->_transferAdapter = $transferAdapterFactory->create();
        $this->_uploaderFactory = $uploaderFactory;
    }

    /**
     * Upload preview image
     *
     * @param string $scope the request key for file
     * @param string $destinationPath path to upload directory
     * @return bool
     * @throws \Magento\Exception
     */
    public function uploadPreviewImage($scope, $destinationPath)
    {
        if (!$this->_transferAdapter->isUploaded($scope)) {
            return false;
        }
        if (!$this->_transferAdapter->isValid($scope)) {
            throw new \Magento\Exception(__('Uploaded image is not valid'));
        }
        $upload = $this->_uploaderFactory->create(array('fileId' => $scope));
        $upload->setAllowCreateFolders(true);
        $upload->setAllowedExtensions($this->_allowedExtensions);
        $upload->setAllowRenameFiles(true);
        $upload->setFilesDispersion(false);

        if (!$upload->checkAllowedExtension($upload->getFileExtension())) {
            throw new \Magento\Exception(__('Invalid image file type.'));
        }
        if (!$upload->save($destinationPath)) {
            throw new \Magento\Exception(__('Image can not be saved.'));
        }
        return $destinationPath . DIRECTORY_SEPARATOR . $upload->getUploadedFileName();
    }
}
