<?php
/**
 * The class, which verifies existence and write access to the needed application directories
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\Model\Dir;

class Verification
{
    /**
     * Codes of directories to create and verify in production mode
     *
     * @var array
     */
    protected static $_productionDirs = array(
        \Magento\Core\Model\Dir::MEDIA,
        \Magento\Core\Model\Dir::VAR_DIR,
        \Magento\Core\Model\Dir::TMP,
        \Magento\Core\Model\Dir::CACHE,
        \Magento\Core\Model\Dir::LOG,
        \Magento\Core\Model\Dir::SESSION,
    );

    /**
     * Codes of directories to create and verify in non-production mode
     *
     * @var array
     */
    protected static $_nonProductionDirs = array(
        \Magento\Core\Model\Dir::MEDIA,
        \Magento\Core\Model\Dir::STATIC_VIEW,
        \Magento\Core\Model\Dir::VAR_DIR,
        \Magento\Core\Model\Dir::TMP,
        \Magento\Core\Model\Dir::CACHE,
        \Magento\Core\Model\Dir::LOG,
        \Magento\Core\Model\Dir::SESSION,
    );

    /**
     * @var \Magento\Filesystem
     */
    protected $_filesystem;

    /**
     * @var \Magento\Core\Model\Dir
     */
    protected $_dirs;

    /**
     * Cached list of directories to create and verify write access
     *
     * @var array
     */
    protected $_dirsToVerify = array();

    /**
     * Constructor - initialize object with required dependencies, determine application state
     *
     * @param \Magento\Filesystem $filesystem
     * @param \Magento\Core\Model\Dir $dirs
     * @param \Magento\Core\Model\App\State $appState
     */
    public function __construct(
        \Magento\Filesystem $filesystem,
        \Magento\Core\Model\Dir $dirs,
        \Magento\Core\Model\App\State $appState
    ) {
        $this->_filesystem = $filesystem;
        $this->_dirs = $dirs;
        $this->_dirsToVerify = $this->_getDirsToVerify($appState);
    }

    /**
     * Return list of directories, that must be verified according to the application mode
     *
     * @param \Magento\Core\Model\App\State $appState
     * @return array
     */
    protected function _getDirsToVerify(\Magento\Core\Model\App\State $appState)
    {
        $result = array();
        $codes = ($appState->getMode() == \Magento\Core\Model\App\State::MODE_PRODUCTION)
            ? self::$_productionDirs
            : self::$_nonProductionDirs;
        foreach ($codes as $code) {
            $result[] = str_replace(DIRECTORY_SEPARATOR, '/', $this->_dirs->getDir($code));
        }
        return $result;
    }

    /**
     * Create the required directories, if they don't exist, and verify write access for existing directories
     */
    public function createAndVerifyDirectories()
    {
        $fails = array();
        foreach ($this->_dirsToVerify as $dir) {
            if ($this->_filesystem->isDirectory($dir)) {
                if (!$this->_filesystem->isWritable($dir)) {
                    $fails[] = $dir;
                }
            } else {
                try {
                    $this->_filesystem->createDirectory($dir);
                } catch (\Magento\Filesystem\FilesystemException $e) {
                    $fails[] = $dir;
                }
            }
        }

        if ($fails) {
            $dirList = implode(', ', $fails);
            throw new \Magento\BootstrapException(
                "Cannot create or verify write access: {$dirList}"
            );
        }
    }
}
