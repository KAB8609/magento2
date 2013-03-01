<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_DesignEditor
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Quick style file uploader
 */
class Mage_DesignEditor_Model_Editor_Tools_QuickStyles_ImageUploader extends Varien_Object
{
    /**
     * Quick style images path prefix
     */
    const PATH_PREFIX_QUICK_STYLE = 'quick_style_images';

    /**
     * Storage path
     *
     * @var string
     */
    protected $_storagePath;

    /**
     * @var Magento_Filesystem
     */
    protected $_filesystem;

    /**
     * @var Magento_ObjectManager
     */
    protected $_objectManager;

    /**
     * Allowed extensions
     *
     * @var array
     */
    protected $_allowedExtensions = array('jpg','jpeg','gif','png');

    /**
     * Generic constructor of change instance
     *
     * @param Magento_ObjectManager $objectManager
     * @param Magento_Filesystem $filesystem
     * @param array $data
     */
    public function __construct(
        Magento_ObjectManager $objectManager,
        Magento_Filesystem $filesystem,
        array $data = array()
    ) {
        $this->_objectManager = $objectManager;
        $this->_filesystem = $filesystem;
        parent::__construct($data);
    }

    /**
     * Get storage folder
     *
     * @return string
     */
    public function getStoragePath()
    {
        if (null === $this->_storagePath) {
            $this->_storagePath = implode(Magento_Filesystem::DIRECTORY_SEPARATOR, array(
                Magento_Filesystem::fixSeparator($this->_getTheme()->getCustomizationPath()),
                Mage_Core_Model_Theme_Files::PATH_PREFIX_CUSTOMIZED,
                self::PATH_PREFIX_QUICK_STYLE,
            ));
        }
        return $this->_storagePath;
    }

    /**
     * Set storage path
     *
     * @param string $path
     * @return Mage_DesignEditor_Model_Editor_Tools_QuickStyles_ImageUploader
     */
    public function setStoragePath($path)
    {
        $this->_storagePath = $path;
        return $this;
    }

    /**
     * Get theme
     *
     * @return Mage_Core_Model_Theme
     * @throws InvalidArgumentException
     */
    protected function _getTheme()
    {
        /** @var $theme Mage_Core_Model_Theme */
        $theme = $this->getTheme();
        if (!$theme->getId()) {
            throw new InvalidArgumentException('Theme was not found.');
        }
        return $theme;
    }

    /**
     * Upload image file
     *
     * @param string $key
     * @return array
     */
    public function uploadFile($key)
    {
        $result = array();
        /** @var $uploader Mage_Core_Model_File_Uploader */
        $uploader = $this->_objectManager->create('Mage_Core_Model_File_Uploader', array('fileId' => $key));
        $uploader->setAllowedExtensions($this->_allowedExtensions);
        $uploader->setAllowRenameFiles(true);
        $uploader->setAllowCreateFolders(true);

        if (!$uploader->save($this->getStoragePath())) {
            /** @todo add translator */
            Mage::throwException('Cannot upload file.');
        }
        $result['css_path'] = implode(
            '/', array('..', self::PATH_PREFIX_QUICK_STYLE, $uploader->getUploadedFileName())
        );
        $result['name'] = $uploader->getUploadedFileName();
        return $result;
    }

    /**
     * Remove file
     *
     * @param string $file
     * @return Mage_DesignEditor_Model_Editor_Tools_QuickStyles_ImageUploader
     */
    public function removeFile($file)
    {
        $path = $this->getStoragePath();
        $_filePath = $this->_filesystem->getAbsolutePath($path . Magento_Filesystem::DIRECTORY_SEPARATOR . $file);

        if ($this->_filesystem->isPathInDirectory($_filePath, $path)
            && $this->_filesystem->isPathInDirectory($_filePath, $this->getStoragePath())
        ) {
            $this->_filesystem->delete($_filePath);
        }

        return $this;
    }
}
