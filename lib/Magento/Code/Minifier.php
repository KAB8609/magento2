<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Code;

class Minifier
{
    /**
     * @var \Magento\Code\Minifier\StrategyInterface
     */
    private $_strategy;

    /**
     * @var \Magento\Filesystem\Directory\Read
     */
    private $pubViewCacheDir;

    /**
     * @var string directory name where minified files are saved
     */
    private $directoryName;

    /**
     * @param \Magento\Code\Minifier\StrategyInterface $strategy
     * @param \Magento\Filesystem $filesystem
     * @param string $directoryName
     */
    public function __construct(
        \Magento\Code\Minifier\StrategyInterface $strategy,
        \Magento\Filesystem $filesystem,
        $directoryName
    ) {
        $this->_strategy = $strategy;
        $this->pubViewCacheDir = $filesystem->getDirectoryRead(\Magento\Filesystem\DirectoryList::PUB_VIEW_CACHE);
        $this->directoryName = $directoryName;
    }

    /**
     * Get path to minified file
     *
     * @param string $originalFile
     * @return bool|string
     */
    public function getMinifiedFile($originalFile)
    {
        if ($this->_isFileMinified($originalFile)) {
            return $originalFile;
        }
        $minifiedFile = $this->_findOriginalMinifiedFile($originalFile);
        if (!$minifiedFile) {
            $minifiedFile = $this->pubViewCacheDir
                ->getAbsolutePath($this->directoryName . '/' . $this->_generateMinifiedFileName($originalFile));
            $this->_strategy->minifyFile($originalFile, $minifiedFile);
        }

        return $minifiedFile;
    }

    /**
     * Check if file is minified
     *
     * @param string $fileName
     * @return bool
     */
    protected function _isFileMinified($fileName)
    {
        return (bool)preg_match('#.min.\w+$#', $fileName);
    }

    /**
     * Generate name of the minified file
     *
     * @param string $originalFile
     * @return string
     */
    protected function _generateMinifiedFileName($originalFile)
    {
        $fileInfo = pathinfo($originalFile);
        $minifiedName = md5($originalFile) . '_' . $fileInfo['filename'] . '.min.' . $fileInfo['extension'];

        return $minifiedName;
    }

    /**
     * Search for minified file provided along with the original file in the code base
     *
     * @param string $originalFile
     * @return bool|string
     */
    protected function _findOriginalMinifiedFile($originalFile)
    {
        $fileInfo = pathinfo($originalFile);
        $minifiedFile = $fileInfo['dirname'] . '/' . $fileInfo['filename'] . '.min.' . $fileInfo['extension'];
        if ($this->pubViewCacheDir->isExist($this->pubViewCacheDir->getRelativePath($minifiedFile))) {
            return $minifiedFile;
        }
        return false;
    }
}
