<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Core\Model\Resource\File\Storage;

/**
 * Class File
 */
class File
{
    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_mediaBaseDirectory = null;

    /**
     * @var \Magento\Filesystem
     */
    protected $_filesystem;

    /**
     * @var \Magento\Core\Helper\File\Storage\Database
     */
    protected $_dbHelper;

    /**
     * @var \Magento\Core\Model\Logger
     */
    protected $_logger;

    /**
     * @param \Magento\Filesystem $filesystem
     * @param \Magento\Core\Helper\File\Storage\Database $dbHelper
     * @param \Magento\Core\Model\Logger $log
     */
    public function __construct(
        \Magento\Filesystem $filesystem,
        \Magento\Core\Helper\File\Storage\Database $dbHelper,
        \Magento\Core\Model\Logger $log
    ) {
        $this->_dbHelper = $dbHelper;
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
     * @return \Magento\Core\Model\Resource\File\Storage\File
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
     * @throws \Magento\Core\Exception
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
        } catch (\Exception $e) {
            $this->_logger->log($e->getMessage());
            throw new \Magento\Core\Exception(__('Unable to create directory: %1', $path));
        }

        return true;
    }

    /**
     * Save file to storage
     *
     * @param string $filePath
     * @param string $content
     * @param bool $overwrite
     * @throws \Magento\Core\Exception
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
        } catch (\Magento\Filesystem\Exception $e) {
            $this->_logger->log($e->getMessage());
            throw new \Magento\Core\Exception(__('Unable to save file: %1', $filePath));
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
        return ltrim(str_replace($this->getMediaBaseDirectory(), '', $path), \Magento\Filesystem::DIRECTORY_SEPARATOR);
    }
}
