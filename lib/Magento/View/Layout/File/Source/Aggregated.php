<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Layout\File\Source;

use Magento\View\Layout\File\SourceInterface;
use Magento\View\Design\ThemeInterface;
use Magento\View\Layout\File\FileList\Factory;

/**
 * Source of layout files aggregated from a theme and its parents according to merging and overriding conventions
 */
class Aggregated implements SourceInterface
{
    /**
     * @var Factory
     */
    private $fileListFactory;

    /**
     * @var SourceInterface
     */
    private $baseFiles;

    /**
     * @var SourceInterface
     */
    private $themeFiles;

    /**
     * @var SourceInterface
     */
    private $overrideBaseFiles;

    /**
     * @var SourceInterface
     */
    private $overrideThemeFiles;

    /**
     * @param Factory $fileListFactory
     * @param SourceInterface $baseFiles
     * @param SourceInterface $themeFiles
     * @param SourceInterface $overrideBaseFiles
     * @param SourceInterface $overrideThemeFiles
     */
    public function __construct(
        Factory $fileListFactory,
        SourceInterface $baseFiles,
        SourceInterface $themeFiles,
        SourceInterface $overrideBaseFiles,
        SourceInterface $overrideThemeFiles
    ) {
        $this->fileListFactory = $fileListFactory;
        $this->baseFiles = $baseFiles;
        $this->themeFiles = $themeFiles;
        $this->overrideBaseFiles = $overrideBaseFiles;
        $this->overrideThemeFiles = $overrideThemeFiles;
    }

    /**
     * Retrieve files
     *
     * Aggregate layout files from modules and a theme and its ancestors
     *
     * @param ThemeInterface $theme
     * @param string $filePath
     * @return \Magento\View\Layout\File[]
     */
    public function getFiles(ThemeInterface $theme, $filePath = '*')
    {
        $list = $this->fileListFactory->create();
        $list->add($this->baseFiles->getFiles($theme, $filePath));

        foreach ($this->getInheritedThemes($theme) as $currentTheme) {
            $list->add($this->themeFiles->getFiles($currentTheme, $filePath));
            $list->replace($this->overrideBaseFiles->getFiles($currentTheme, $filePath));
            $list->replace($this->overrideThemeFiles->getFiles($currentTheme, $filePath));
        }
        return $list->getAll();
    }

    /**
     * Return the full theme inheritance sequence, from the root theme till a specified one
     *
     * @param ThemeInterface $theme
     * @return Theme[] Format: array([<root_theme>, ..., <parent_theme>,] <current_theme>)
     */
    protected function getInheritedThemes(ThemeInterface $theme)
    {
        $result = array();
        while ($theme) {
            $result[] = $theme;
            $theme = $theme->getParentTheme();
        }
        return array_reverse($result);
    }
}