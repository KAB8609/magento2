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
 * Service of copying customizations from one theme to another
 */
namespace Magento\Core\Model\Theme;

class CopyService
{
    /**
     * @var \Magento\Filesystem
     */
    protected $_filesystem;

    /**
     * @var \Magento\Core\Model\Theme\FileFactory
     */
    protected $_fileFactory;

    /**
     * @var \Magento\Core\Model\Layout\Link
     */
    protected $_link;

    /**
     * @var \Magento\Core\Model\Layout\UpdateFactory
     */
    protected $_updateFactory;

    /**
     * @var \Magento\Core\Model\Event\Manager
     */
    protected $_eventManager;

    /**
     * @var \Magento\Core\Model\Theme\Customization\Path
     */
    protected $_customizationPath;

    /**
     * @param \Magento\Filesystem $filesystem
     * @param \Magento\Core\Model\Theme\FileFactory $fileFactory
     * @param \Magento\Core\Model\Layout\Link $link
     * @param \Magento\Core\Model\Layout\UpdateFactory $updateFactory
     * @param \Magento\Core\Model\Event\Manager $eventManager
     * @param \Magento\Core\Model\Theme\Customization\Path $customization
     */
    public function __construct(
        \Magento\Filesystem $filesystem,
        \Magento\Core\Model\Theme\FileFactory $fileFactory,
        \Magento\Core\Model\Layout\Link $link,
        \Magento\Core\Model\Layout\UpdateFactory $updateFactory,
        \Magento\Core\Model\Event\Manager $eventManager,
        \Magento\Core\Model\Theme\Customization\Path $customization
    ) {
        $this->_filesystem = $filesystem;
        $this->_fileFactory = $fileFactory;
        $this->_link = $link;
        $this->_updateFactory = $updateFactory;
        $this->_eventManager = $eventManager;
        $this->_customizationPath = $customization;
    }

    /**
     * Copy customizations from one theme to another
     *
     * @param \Magento\Core\Model\Theme $source
     * @param \Magento\Core\Model\Theme $target
     */
    public function copy(\Magento\Core\Model\Theme $source, \Magento\Core\Model\Theme $target)
    {
        $this->_copyDatabaseCustomization($source, $target);
        $this->_copyLayoutCustomization($source, $target);
        $this->_copyFilesystemCustomization($source, $target);
    }

    /**
     * Copy customizations stored in a database from one theme to another, overriding existing data
     *
     * @param \Magento\Core\Model\Theme $source
     * @param \Magento\Core\Model\Theme $target
     */
    protected function _copyDatabaseCustomization(\Magento\Core\Model\Theme $source, \Magento\Core\Model\Theme $target)
    {
        /** @var $themeFile \Magento\Core\Model\Theme\File */
        foreach ($target->getCustomization()->getFiles() as $themeFile) {
            $themeFile->delete();
        }
        /** @var $newFile \Magento\Core\Model\Theme\File */
        foreach ($source->getCustomization()->getFiles() as $themeFile) {
            /** @var $newThemeFile \Magento\Core\Model\Theme\File */
            $newThemeFile = $this->_fileFactory->create();
            $newThemeFile->setData(array(
                'theme_id'      => $target->getId(),
                'file_path'     => $themeFile->getFilePath(),
                'file_type'     => $themeFile->getFileType(),
                'content'       => $themeFile->getContent(),
                'sort_order'    => $themeFile->getData('sort_order'),
            ));
            $newThemeFile->save();
        }
    }

    /**
     * Add layout links to general layout updates for themes
     *
     * @param \Magento\Core\Model\Theme $source
     * @param \Magento\Core\Model\Theme $target
     */
    protected function _copyLayoutCustomization(\Magento\Core\Model\Theme $source, \Magento\Core\Model\Theme $target)
    {
        $update = $this->_updateFactory->create();
        /** @var $targetUpdates \Magento\Core\Model\Resource\Layout\Update\Collection */
        $targetUpdates = $update->getCollection();
        $targetUpdates->addThemeFilter($target->getId());
        $targetUpdates->delete();

        /** @var $sourceCollection \Magento\Core\Model\Resource\Layout\Link\Collection */
        $sourceCollection = $this->_link->getCollection();
        $sourceCollection->addThemeFilter($source->getId());
        /** @var $layoutLink \Magento\Core\Model\Layout\Link */
        foreach ($sourceCollection as $layoutLink) {
            /** @var $update \Magento\Core\Model\Layout\Update */
            $update = $this->_updateFactory->create();
            $update->load($layoutLink->getLayoutUpdateId());
            if ($update->getId()) {
                $update->setId(null);
                $update->save();
                $layoutLink->setThemeId($target->getId());
                $layoutLink->setLayoutUpdateId($update->getId());
                $layoutLink->setId(null);
                $layoutLink->save();
            }
        }
    }

    /**
     * Copy customizations stored in a file system from one theme to another, overriding existing data
     *
     * @param \Magento\Core\Model\Theme $source
     * @param \Magento\Core\Model\Theme $target
     */
    protected function _copyFilesystemCustomization(\Magento\Core\Model\Theme $source, \Magento\Core\Model\Theme $target)
    {
        $sourcePath = $this->_customizationPath->getCustomizationPath($source);
        $targetPath = $this->_customizationPath->getCustomizationPath($target);

        if (!$sourcePath || !$targetPath) {
            return;
        }

        $this->_deleteFilesRecursively($targetPath);

        if ($this->_filesystem->isDirectory($sourcePath)) {
            $this->_copyFilesRecursively($sourcePath, $sourcePath, $targetPath);
        }
    }

    /**
     * Copies all files in a directory recursively
     *
     * @param string $baseDir
     * @param string $sourceDir
     * @param string $targetDir
     */
    protected function _copyFilesRecursively($baseDir, $sourceDir, $targetDir)
    {
        $this->_filesystem->setIsAllowCreateDirectories(true);
        foreach ($this->_filesystem->searchKeys($sourceDir, '*') as $path) {
            if ($this->_filesystem->isDirectory($path)) {
                $this->_copyFilesRecursively($baseDir, $path, $targetDir);
            } else {
                $filePath = substr($path, strlen($baseDir) + 1);
                $this->_filesystem->copy($path, $targetDir . '/' . $filePath, $baseDir, $targetDir);
            }
        }
    }

    /**
     * Delete all files in a directory recursively
     *
     * @param string $targetDir
     */
    protected function _deleteFilesRecursively($targetDir)
    {
        foreach ($this->_filesystem->searchKeys($targetDir, '*') as $path) {
            $this->_filesystem->delete($path);
        }
    }
}
