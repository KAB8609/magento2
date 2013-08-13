<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Model for synchronization from DB to filesystem
 *
 * @category    Magento
 * @package     Magento_Core
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Core_Model_Resource_File_Storage_File
{
    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_mediaBaseDirectory = null;

    /**
     * @var Magento_Filesystem
     */
    protected $_filesystem;

    /**
     * @var Magento_Core_Helper_File_Storage_Database
     */
    protected $_dbHelper;

    /**
     * @var Magento_Core_Helper_Data
     */
    protected $_helper;

    /**
     * @var Magento_Core_Model_Logger
     */
    protected $_logger;

    /**
     * @param Magento_Filesystem $filesystem
     * @param Magento_Core_Helper_File_Storage_Database $dbHelper
     * @param Magento_Core_Helper_Data $helper
     * @param Magento_Core_Model_Logger $log
     */
    public function __construct(
        Magento_Filesystem $filesystem,
        Magento_Core_Helper_File_Storage_Database $dbHelper,
        Magento_Core_Helper_Data $helper,
        Magento_Core_Model_Logger $log
    ) {
        $this->_dbHelper = $dbHelper;
        $this->_helper = $helper;
        $this->_logger = $log;

        $this->_filesystem = $filesystem;
        $this->_filesystem->setIsAllowCreateDirectories(true);
        $this->_filesystem->ensureDirectoryExists($this->getMediaBaseDirectory());
        $this->_filesystem->setWorkingDirectory($this->getMediaBaseDirectory());
    }

    /**
     * Files at storage
     *
     * @return string
     */
    public function getMediaBaseDirectory()
    {
        if (is_null($this->_mediaBaseDirectory)) {
            $this->_mediaBaseDirectory = $this->_dbHelper->getMediaBaseDir();
        }

        return $this->_mediaBaseDirectory;
    }

    /**
     * Collect files and directories recursively
     *
     * @param string $dir
     * @return array
     */
    public function getStorageData($dir = '')
    {
        $files          = array();
        $directories    = array();
        $currentDir     = $this->getMediaBaseDirectory() . $dir;

        if ($this->_filesystem->isDirectory($currentDir)) {
            foreach ($this->_filesystem->getNestedKeys($currentDir) as $fullPath) {
                $itemName = basename($fullPath);
                if ($itemName == '.svn' || $itemName == '.htaccess') {
                    continue;
                }

                $relativePath = $this->_getRelativePath($fullPath);
                if ($this->_filesystem->isDirectory($fullPath)) {
                    $directories[] = array(
                        'name' => $itemName,
                        'path' => dirname($relativePath)
                    );
                } else {
                    $files[] = $relativePath;
                }
            }
        }

        return array('files' => $files, 'directories' => $directories);
    }

    /**
     * Clear files and directories in storage
     *
     * @param string $dir
     * @return Magento_Core_Model_Resource_File_Storage_File
     */
    public function clear($dir = '')
    {
        if (strpos($dir, $this->getMediaBaseDirectory()) !== 0) {
            $dir = $this->getMediaBaseDirectory() . $dir;
        }

        if ($this->_filesystem->isDirectory($dir)) {
            foreach ($this->_filesystem->getNestedKeys($dir) as $path) {
                $this->_filesystem->delete($path);
            }
        }

        return $this;
    }

    /**
     * Save directory to storage
     *
     * @param array $dir
     * @return bool
     */
    public function saveDir($dir)
    {
        if (!isset($dir['name']) || !strlen($dir['name']) || !isset($dir['path'])) {
            return false;
        }

        $path = (strlen($dir['path']))
            ? $dir['path'] . DS . $dir['name']
            : $dir['name'];
        $path = $this->getMediaBaseDirectory() . DS . $path;

        try {
            $this->_filesystem->ensureDirectoryExists($path);
        } catch (Exception $e) {
            $this->_logger->log($e->getMessage());
            Mage::throwException($this->_helper->__('Unable to create directory: %s', $path));
        }

        return true;
    }

    /**
     * Save file to storage
     *
     * @param string $filePath
     * @param string $content
     * @param bool $overwrite
     * @return bool
     */
    public function saveFile($filePath, $content, $overwrite = false)
    {
        if (strpos($filePath, $this->getMediaBaseDirectory()) !== 0) {
            $filePath = $this->getMediaBaseDirectory() . DS . $filePath;
        }

        try {
            if (!$this->_filesystem->isFile($filePath) || ($overwrite && $this->_filesystem->delete($filePath))) {
                $this->_filesystem->write($filePath, $content);
                return true;
            }
        } catch (Magento_Filesystem_Exception $e) {
            $this->_logger->log($e->getMessage());
            Mage::throwException($this->_helper->__('Unable to save file: %s', $filePath));
        }

        return false;
    }

    /**
     * Get path relative to media base directory
     *
     * @param string $path
     * @return string
     */
    protected function _getRelativePath($path)
    {
        return ltrim(str_replace($this->getMediaBaseDirectory(), '', $path), Magento_Filesystem::DIRECTORY_SEPARATOR);
    }
}
