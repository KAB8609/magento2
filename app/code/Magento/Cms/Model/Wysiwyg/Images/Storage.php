<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Cms
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Wysiwyg Images model
 *
 * @category    Magento
 * @package     Magento_Cms
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Cms_Model_Wysiwyg_Images_Storage extends \Magento\Object
{
    const DIRECTORY_NAME_REGEXP = '/^[a-z0-9\-\_]+$/si';
    const THUMBS_DIRECTORY_NAME = '.thumbs';
    const THUMB_PLACEHOLDER_PATH_SUFFIX = 'Magento_Cms::images/placeholder_thumbnail.jpg';

    /**
     * Config object
     *
     * @var Magento_Core_Model_Config_Element
     */
    protected $_config;

    /**
     * Config object as array
     *
     * @var array
     */
    protected $_configAsArray;

    /**
     * @var \Magento\Filesystem
     */
    protected $_filesystem;

    /**
     * @var Magento_Core_Model_Image_AdapterFactory
     */
    protected $_imageFactory;

    /**
     * @var Magento_Core_Model_View_Url
     */
    protected $_viewUrl;

    /**
     * Constructor
     *
     * @param \Magento\Filesystem $filesystem
     * @param Magento_Core_Model_Image_AdapterFactory $imageFactory
     * @param Magento_Core_Model_View_Url $viewUrl
     * @param array $data
     */
    public function __construct(
        \Magento\Filesystem $filesystem,
        Magento_Core_Model_Image_AdapterFactory $imageFactory,
        Magento_Core_Model_View_Url $viewUrl,
        array $data = array()
    ) {
        $this->_filesystem = $filesystem;
        $this->_filesystem->setIsAllowCreateDirectories(true);
        $this->_filesystem->setWorkingDirectory($this->getHelper()->getStorageRoot());
        $this->_imageFactory = $imageFactory;
        $this->_viewUrl = $viewUrl;
        parent::__construct($data);
    }

    /**
     * Return one-level child directories for specified path
     *
     * @param string $path Parent directory path
     * @return \Magento\Data\Collection\Filesystem
     */
    public function getDirsCollection($path)
    {
        if (Mage::helper('Magento_Core_Helper_File_Storage_Database')->checkDbUsage()) {
            $subDirectories = Mage::getModel('Magento_Core_Model_File_Storage_Directory_Database')
                ->getSubdirectories($path);
            foreach ($subDirectories as $directory) {
                $fullPath = rtrim($path, DS) . DS . $directory['name'];
                $this->_filesystem->ensureDirectoryExists($fullPath, 0777, $path);
            }
        }

        $conditions = array('reg_exp' => array(), 'plain' => array());

        foreach ($this->getConfig()->dirs->exclude->children() as $dir) {
            $conditions[$dir->getAttribute('regexp') ? 'reg_exp' : 'plain'][(string) $dir] = true;
        }
        // "include" section takes precedence and can revoke directory exclusion
        foreach ($this->getConfig()->dirs->include->children() as $dir) {
            unset($conditions['regexp'][(string) $dir], $conditions['plain'][(string) $dir]);
        }

        $regExp = $conditions['reg_exp'] ? ('~' . implode('|', array_keys($conditions['reg_exp'])) . '~i') : null;
        $collection = $this->getCollection($path)
            ->setCollectDirs(true)
            ->setCollectFiles(false)
            ->setCollectRecursively(false);
        $storageRootLength = strlen($this->getHelper()->getStorageRoot());

        foreach ($collection as $key => $value) {
            $rootChildParts = explode(DIRECTORY_SEPARATOR, substr($value->getFilename(), $storageRootLength));

            if (array_key_exists($rootChildParts[0], $conditions['plain'])
                || ($regExp && preg_match($regExp, $value->getFilename()))) {
                $collection->removeItemByKey($key);
            }
        }

        return $collection;
    }

    /**
     * Return files
     *
     * @param string $path Parent directory path
     * @param string $type Type of storage, e.g. image, media etc.
     * @return \Magento\Data\Collection\Filesystem
     */
    public function getFilesCollection($path, $type = null)
    {
        if (Mage::helper('Magento_Core_Helper_File_Storage_Database')->checkDbUsage()) {
            $files = Mage::getModel('Magento_Core_Model_File_Storage_Database')->getDirectoryFiles($path);

            $fileStorageModel = Mage::getModel('Magento_Core_Model_File_Storage_File');
            foreach ($files as $file) {
                $fileStorageModel->saveFile($file);
            }
        }

        $collection = $this->getCollection($path)
            ->setCollectDirs(false)
            ->setCollectFiles(true)
            ->setCollectRecursively(false)
            ->setOrder('mtime', \Magento\Data\Collection::SORT_ORDER_ASC);

        // Add files extension filter
        if ($allowed = $this->getAllowedExtensions($type)) {
            $collection->setFilesFilter('/\.(' . implode('|', $allowed). ')$/i');
        }

        $helper = $this->getHelper();

        // prepare items
        foreach ($collection as $item) {
            $item->setId($helper->idEncode($item->getBasename()));
            $item->setName($item->getBasename());
            $item->setShortName($helper->getShortFilename($item->getBasename()));
            $item->setUrl($helper->getCurrentUrl() . $item->getBasename());

            if ($this->isImage($item->getBasename())) {
                $thumbUrl = $this->getThumbnailUrl($item->getFilename(), true);
                // generate thumbnail "on the fly" if it does not exists
                if (!$thumbUrl) {
                    $thumbUrl = Mage::getSingleton('Magento_Backend_Model_Url')
                        ->getUrl('*/*/thumbnail', array('file' => $item->getId()));
                }

                $size = @getimagesize($item->getFilename());

                if (is_array($size)) {
                    $item->setWidth($size[0]);
                    $item->setHeight($size[1]);
                }
            } else {
                $thumbUrl = $this->_viewUrl->getViewFileUrl(self::THUMB_PLACEHOLDER_PATH_SUFFIX);
            }

            $item->setThumbUrl($thumbUrl);
        }

        return $collection;
    }

    /**
     * Storage collection
     *
     * @param string $path Path to the directory
     * @return \Magento\Data\Collection\Filesystem
     */
    public function getCollection($path = null)
    {
        $collection = Mage::getModel('Magento_Cms_Model_Wysiwyg_Images_Storage_Collection');
        if ($path !== null) {
            $collection->addTargetDir($path);
        }
        return $collection;
    }

    /**
     * Create new directory in storage
     *
     * @param string $name New directory name
     * @param string $path Parent directory path
     * @throws Magento_Core_Exception
     * @return array New directory info
     */
    public function createDirectory($name, $path)
    {
        if (!preg_match(self::DIRECTORY_NAME_REGEXP, $name)) {
            Mage::throwException(__('Please correct the folder name. Use only letters, numbers, underscores and dashes.'));
        }
        if (!$this->_filesystem->isDirectory($path) || !$this->_filesystem->isWritable($path)) {
            $path = $this->getHelper()->getStorageRoot();
        }

        $newPath = $path . DS . $name;

        if ($this->_filesystem->isDirectory($newPath, $path)) {
            Mage::throwException(__('We found a directory with the same name. Please try another folder name.'));
        }

        $this->_filesystem->createDirectory($newPath);
        try {
            if (Mage::helper('Magento_Core_Helper_File_Storage_Database')->checkDbUsage()) {
                $relativePath = Mage::helper('Magento_Core_Helper_File_Storage_Database')->getMediaRelativePath($newPath);
                Mage::getModel('Magento_Core_Model_File_Storage_Directory_Database')->createRecursive($relativePath);
            }

            $result = array(
                'name'          => $name,
                'short_name'    => $this->getHelper()->getShortFilename($name),
                'path'          => $newPath,
                'id'            => $this->getHelper()->convertPathToId($newPath)
            );
            return $result;
        } Catch (\Magento\Filesystem\FilesystemException $e) {
            Mage::throwException(__('We cannot create a new directory.'));
        }
    }

    /**
     * Recursively delete directory from storage
     *
     * @param string $path Target dir
     * @return void
     */
    public function deleteDirectory($path)
    {
        // prevent accidental root directory deleting
        $rootCmp = rtrim($this->getHelper()->getStorageRoot(), DS);
        $pathCmp = rtrim($path, DS);

        if ($rootCmp == $pathCmp) {
            Mage::throwException(
                __('We cannot delete root directory %1.', $path)
            );
        }


        if (Mage::helper('Magento_Core_Helper_File_Storage_Database')->checkDbUsage()) {
            Mage::getModel('Magento_Core_Model_File_Storage_Directory_Database')->deleteDirectory($path);
        }
        try {
            $this->_filesystem->delete($path);
        } catch (\Magento\Filesystem\FilesystemException $e) {
            Mage::throwException(__('We cannot delete directory %1.', $path));
        }

        if (strpos($pathCmp, $rootCmp) === 0) {
            $this->_filesystem->delete(
                $this->getThumbnailRoot() . DS . ltrim(substr($pathCmp, strlen($rootCmp)), '\\/')
            );
        }
    }

    /**
     * Delete file (and its thumbnail if exists) from storage
     *
     * @param string $target File path to be deleted
     * @return Magento_Cms_Model_Wysiwyg_Images_Storage
     */
    public function deleteFile($target)
    {
        if ($this->_filesystem->isFile($target)) {
            $this->_filesystem->delete($target);
        }
        Mage::helper('Magento_Core_Helper_File_Storage_Database')->deleteFile($target);

        $thumb = $this->getThumbnailPath($target, true);
        if ($thumb) {
            if ($this->_filesystem->isFile($thumb)) {
                $this->_filesystem->delete($thumb);
            }
            Mage::helper('Magento_Core_Helper_File_Storage_Database')->deleteFile($thumb);
        }
        return $this;
    }


    /**
     * Upload and resize new file
     *
     * @param string $targetPath Target directory
     * @param string $type Type of storage, e.g. image, media etc.
     * @throws Magento_Core_Exception
     * @return array File info Array
     */
    public function uploadFile($targetPath, $type = null)
    {
        $uploader = new Magento_Core_Model_File_Uploader('image');
        $allowed = $this->getAllowedExtensions($type);
        if ($allowed) {
            $uploader->setAllowedExtensions($allowed);
        }
        $uploader->setAllowRenameFiles(true);
        $uploader->setFilesDispersion(false);
        $result = $uploader->save($targetPath);

        if (!$result) {
            Mage::throwException(__('We cannot upload the file.') );
        }

        // create thumbnail
        $this->resizeFile($targetPath . DS . $uploader->getUploadedFileName(), true);

        $result['cookie'] = array(
            'name'     => session_name(),
            'value'    => $this->getSession()->getSessionId(),
            'lifetime' => $this->getSession()->getCookieLifetime(),
            'path'     => $this->getSession()->getCookiePath(),
            'domain'   => $this->getSession()->getCookieDomain()
        );

        return $result;
    }

    /**
     * Thumbnail path getter
     *
     * @param  string $filePath original file path
     * @param  boolean $checkFile OPTIONAL is it necessary to check file availability
     * @return string | false
     */
    public function getThumbnailPath($filePath, $checkFile = false)
    {
        $mediaRootDir = $this->getHelper()->getStorageRoot();

        if (strpos($filePath, $mediaRootDir) === 0) {
            $thumbPath = $this->getThumbnailRoot() . DS . substr($filePath, strlen($mediaRootDir));

            if (!$checkFile || $this->_filesystem->isReadable($thumbPath)) {
                return $thumbPath;
            }
        }

        return false;
    }

    /**
     * Thumbnail URL getter
     *
     * @param  string $filePath original file path
     * @param  boolean $checkFile OPTIONAL is it necessary to check file availability
     * @return string | false
     */
    public function getThumbnailUrl($filePath, $checkFile = false)
    {
        $mediaRootDir = $this->getHelper()->getStorageRoot();

        if (strpos($filePath, $mediaRootDir) === 0) {
            $thumbSuffix = self::THUMBS_DIRECTORY_NAME . DS . substr($filePath, strlen($mediaRootDir));

            if (! $checkFile || $this->_filesystem->isReadable($mediaRootDir . $thumbSuffix)) {
                $randomIndex = '?rand=' . time();
                return str_replace('\\', '/', $this->getHelper()->getBaseUrl() . $thumbSuffix) . $randomIndex;
            }
        }

        return false;
    }

    /**
     * Create thumbnail for image and save it to thumbnails directory
     *
     * @param string $source Image path to be resized
     * @param bool $keepRation Keep aspect ratio or not
     * @return bool|string Resized filepath or false if errors were occurred
     */
    public function resizeFile($source, $keepRation = true)
    {
        if (!$this->_filesystem->isFile($source)
            || !$this->_filesystem->isReadable($source)
        ) {
            return false;
        }

        $targetDir = $this->getThumbsPath($source);
        if (!$this->_filesystem->isWritable($targetDir)) {
            $this->_filesystem->createDirectory($targetDir);
        }
        if (!$this->_filesystem->isWritable($targetDir)) {
            return false;
        }
        $image = $this->_imageFactory->create();
        $image->open($source);
        $width = $this->getConfigData('resize_width');
        $height = $this->getConfigData('resize_height');
        $image->keepAspectRatio($keepRation);
        $image->resize($width, $height);
        $dest = $targetDir . DS . pathinfo($source, PATHINFO_BASENAME);
        $image->save($dest);
        if ($this->_filesystem->isFile($dest)) {
            return $dest;
        }
        return false;
    }

    /**
     * Resize images on the fly in controller action
     *
     * @param string File basename
     * @return bool|string Thumbnail path or false for errors
     */
    public function resizeOnTheFly($filename)
    {
        $path = $this->getSession()->getCurrentPath();
        if (!$path) {
            $path = $this->getHelper()->getCurrentPath();
        }
        return $this->resizeFile($path . DS . $filename);
    }

    /**
     * Return thumbnails directory path for file/current directory
     *
     * @param bool|string $filePath Path to the file
     * @return string
     */
    public function getThumbsPath($filePath = false)
    {
        $mediaRootDir = Mage::getBaseDir(Magento_Core_Model_Dir::MEDIA);
        $thumbnailDir = $this->getThumbnailRoot();

        if ($filePath && strpos($filePath, $mediaRootDir) === 0) {
            $thumbnailDir .= DS . dirname(substr($filePath, strlen($mediaRootDir)));
        }

        return $thumbnailDir;
    }

    /**
     * Media Storage Helper getter
     * @return Magento_Cms_Helper_Wysiwyg_Images
     */
    public function getHelper()
    {
        return Mage::helper('Magento_Cms_Helper_Wysiwyg_Images');
    }

    /**
     * Storage session
     *
     * @return Magento_Adminhtml_Model_Session
     */
    public function getSession()
    {
        return Mage::getSingleton('Magento_Adminhtml_Model_Session');
    }

    /**
     * Config object getter
     *
     * @return Magento_Core_Model_Config_Element
     */
    public function getConfig()
    {
        if (! $this->_config) {
            $this->_config = Mage::getConfig()->getNode('cms/browser', 'adminhtml');
        }

        return $this->_config;
    }

    /**
     * Config object as array getter
     *
     * @return array
     */
    public function getConfigAsArray()
    {
        if (!$this->_configAsArray) {
            $this->_configAsArray = $this->getConfig()->asCanonicalArray();
        }

        return $this->_configAsArray;
    }

    /**
     * Wysiwyg Config reader
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function getConfigData($key, $default=false)
    {
        $configArray = $this->getConfigAsArray();
        $key = (string)$key;

        return array_key_exists($key, $configArray) ? $configArray[$key] : $default;
    }

    /**
     * Prepare allowed_extensions config settings
     *
     * @param string $type Type of storage, e.g. image, media etc.
     * @return array Array of allowed file extensions
     */
    public function getAllowedExtensions($type = null)
    {
        $extensions = $this->getConfigData('extensions');

        if (is_string($type) && array_key_exists("{$type}_allowed", $extensions)) {
            $allowed = $extensions["{$type}_allowed"];
        } else {
            $allowed = $extensions['allowed'];
        }

        return array_keys(array_filter($allowed));
    }

    /**
     * Thumbnail root directory getter
     *
     * @return string
     */
    public function getThumbnailRoot()
    {
        return $this->getHelper()->getStorageRoot() . self::THUMBS_DIRECTORY_NAME;
    }

    /**
     * Simple way to check whether file is image or not based on extension
     *
     * @param string $filename
     * @return bool
     */
    public function isImage($filename)
    {
        if (!$this->hasData('_image_extensions')) {
            $this->setData('_image_extensions', $this->getAllowedExtensions('image'));
        }
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        return in_array($ext, $this->_getData('_image_extensions'));
    }
}
