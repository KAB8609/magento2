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
 * Theme filesystem collection
 */
class Magento_Core_Model_Theme_Collection extends Magento_Data_Collection
{
    /**
     * @var Magento_Filesystem
     */
    protected $_filesystem;

    /**
     * Model of collection item
     *
     * @var string
     */
    protected $_itemObjectClass = 'Magento_Core_Model_Theme';

    /**
     * Base directory with design
     *
     * @var string
     */
    protected $_baseDir;

    /**
     * Target directory
     *
     * @var array
     */
    protected $_targetDirs = array();

    /**
     * @param Magento_Filesystem $filesystem
     * @param Magento_Core_Model_Dir $dirs
     */
    public function __construct(
        Magento_Filesystem $filesystem,
        Magento_Core_Model_Dir $dirs
    ) {
        parent::__construct();
        $this->_filesystem = $filesystem;
        $this->setBaseDir($dirs->getDir(Magento_Core_Model_Dir::THEMES));
    }

    /**
     * Set base directory path of design
     *
     * @param string $path
     * @return Magento_Core_Model_Theme_Collection
     */
    public function setBaseDir($path)
    {
        if ($this->isLoaded() && $this->_baseDir) {
            $this->clearTargetPatterns()->clear();
        }
        $this->_baseDir = rtrim($path, DIRECTORY_SEPARATOR);
        return $this;
    }

    /**
     * Get base directory path
     *
     * @return string
     */
    public function getBaseDir()
    {
        return $this->_baseDir;
    }

    /**
     * Add default pattern to themes configuration
     *
     * @param string $area
     * @return Magento_Core_Model_Theme_Collection
     */
    public function addDefaultPattern($area = Magento_Core_Model_App_Area::AREA_FRONTEND)
    {
        $this->addTargetPattern(implode(DIRECTORY_SEPARATOR, array($area, '*', 'theme.xml')));
        return $this;
    }

    /**
     * Target directory setter. Adds directory to be scanned
     *
     * @param string $relativeTarget
     * @return Magento_Core_Model_Theme_Collection
     */
    public function addTargetPattern($relativeTarget)
    {
        if ($this->isLoaded()) {
            $this->clear();
        }
        $this->_targetDirs[] = $relativeTarget;
        return $this;
    }

    /**
     * Clear target patterns
     *
     * @return Magento_Core_Model_Theme_Collection
     */
    public function clearTargetPatterns()
    {
        $this->_targetDirs = array();
        return $this;
    }

    /**
     * Return target dir for themes with theme configuration file
     *
     * @throws Magento_Exception
     * @return array|string
     */
    public function getTargetPatterns()
    {
        if (empty($this->_targetDirs)) {
            throw new Magento_Exception('Please specify at least one target pattern to theme config file.');
        }
        return $this->_targetDirs;
    }

    /**
     * Fill collection with theme model loaded from filesystem
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     *
     * @param bool $printQuery
     * @param bool $logQuery
     * @return Magento_Core_Model_Theme_Collection
     */
    public function loadData($printQuery = false, $logQuery = false)
    {
        if ($this->isLoaded()) {
            return $this;
        }

        $pathsToThemeConfig = array();
        foreach ($this->getTargetPatterns() as $directoryPath) {
            $themeConfigs = $this->_filesystem->searchKeys($this->getBaseDir(), $directoryPath);
            $themeConfigs = str_replace('/', DIRECTORY_SEPARATOR, $themeConfigs);
            $pathsToThemeConfig = array_merge($pathsToThemeConfig, $themeConfigs);
        }

        $this->_loadFromFilesystem($pathsToThemeConfig)
            ->clearTargetPatterns()
            ->_updateRelations()
            ->_renderFilters()
            ->_clearFilters();

        return $this;
    }

    /**
     * Set all parent themes
     *
     * @return Magento_Core_Model_Theme_Collection
     */
    protected function _updateRelations()
    {
        $themeItems = $this->getItems();
        /** @var $theme Magento_Object|Magento_Core_Model_ThemeInterface */
        foreach ($themeItems as $theme) {
            $parentThemePath = $theme->getData('parent_theme_path');
            if ($parentThemePath) {
                $themePath = $theme->getArea() . Magento_Core_Model_ThemeInterface::PATH_SEPARATOR . $parentThemePath;
                if (isset($themeItems[$themePath])) {
                    $theme->setParentTheme($themeItems[$themePath]);
                }
            }
        }
        return $this;
    }

    /**
     * Load themes collection from file system by file list
     *
     * @param array $themeConfigPaths
     * @return Magento_Core_Model_Theme_Collection
     */
    protected function _loadFromFilesystem(array $themeConfigPaths)
    {
        foreach ($themeConfigPaths as $themeConfigPath) {
            $theme = $this->getNewEmptyItem()->addData($this->_prepareConfigurationData($themeConfigPath));
            $this->addItem($theme);
        }
        $this->_setIsLoaded();

        return $this;
    }

    /**
     * Return default path related data
     *
     * @param string $configPath
     * @return array
     */
    protected function _preparePathData($configPath)
    {
        $themeDirectory = dirname($configPath);
        $fullPath = trim(substr($themeDirectory, strlen($this->getBaseDir())), DIRECTORY_SEPARATOR);
        $pathPieces = explode(DIRECTORY_SEPARATOR, $fullPath);
        $area = array_shift($pathPieces);
        return array('area' => $area, 'theme_path_pieces' => $pathPieces);
    }

    /**
     * Return default configuration data
     *
     * @param string $configPath
     * @return array
     */
    public function _prepareConfigurationData($configPath)
    {
        $themeConfig = $this->_getConfigModel(array($configPath));
        $pathData = $this->_preparePathData($configPath);
        $media = $themeConfig->getMedia();

        $parentPathPieces = $themeConfig->getParentTheme();
        if (count($parentPathPieces) == 1) {
            $pathPieces = $pathData['theme_path_pieces'];
            array_pop($pathPieces);
            $parentPathPieces = array_merge($pathPieces, $parentPathPieces);
        }

        $themePath = implode(Magento_Core_Model_Theme::PATH_SEPARATOR, $pathData['theme_path_pieces']);
        $themeCode = implode(Magento_Core_Model_Theme::CODE_SEPARATOR, $pathData['theme_path_pieces']);
        $parentPath = $parentPathPieces ? implode(Magento_Core_Model_Theme::PATH_SEPARATOR, $parentPathPieces) : null;

        return array(
            'parent_id'         => null,
            'type'              => Magento_Core_Model_Theme::TYPE_PHYSICAL,
            'area'              => $pathData['area'],
            'theme_path'        => $themePath,
            'code'              => $themeCode,
            'theme_version'     => $themeConfig->getThemeVersion(),
            'theme_title'       => $themeConfig->getThemeTitle(),
            'preview_image'     => $media['preview_image'] ? $media['preview_image'] : null,
            'parent_theme_path' => $parentPath
        );
    }

    /**
     * Apply set field filters
     *
     * @return Magento_Core_Model_Theme_Collection
     */
    protected function _renderFilters()
    {
        $filters = $this->getFilter(array());
        /** @var $theme Magento_Core_Model_Theme */
        foreach ($this->getItems() as $itemKey => $theme) {
            $removeItem = false;
            foreach ($filters as $filter) {
                if ($filter['type'] == 'and' && $theme->getDataUsingMethod($filter['field']) != $filter['value']) {
                    $removeItem = true;
                }
            }
            if ($removeItem) {
                $this->removeItemByKey($itemKey);
            }
        }
        return $this;
    }

    /**
     * Clear all added filters
     *
     * @return Magento_Core_Model_Theme_Collection
     */
    protected function _clearFilters()
    {
        $this->_filters = array();
        return $this;
    }

    /**
     * Return configuration model for themes
     *
     * @param array $configPaths
     * @return Magento_Config_Theme
     */
    protected function _getConfigModel(array $configPaths)
    {
        return new Magento_Config_Theme($configPaths);
    }

    /**
     * Retrieve item id
     *
     * @param Magento_Core_Model_Theme|Magento_Object $item
     * @return string
     */
    protected function _getItemId(Magento_Object $item)
    {
        return $item->getFullPath();
    }

    /**
     * Return array for select field
     *
     * @param bool $addEmptyField
     * @return array
     */
    public function toOptionArray($addEmptyField = false)
    {
        $optionArray = $addEmptyField ? array('' => '') : array();
        return $optionArray + $this->_toOptionArray('theme_id', 'theme_title');
    }

    /**
     * Checks that a theme present in filesystem collection
     *
     * @param Magento_Core_Model_ThemeInterface $theme
     * @return bool
     */
    public function hasTheme(Magento_Core_Model_ThemeInterface $theme)
    {
        $themeItems = $this->getItems();
        return $theme->getThemePath() && isset($themeItems[$theme->getFullPath()]);
    }
}