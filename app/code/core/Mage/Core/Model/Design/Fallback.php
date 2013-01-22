<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Class for managing fallback of files
 */
class Mage_Core_Model_Design_Fallback implements Mage_Core_Model_Design_FallbackInterface
{
    /**
     * @var string
     */
    protected $_area;

    /**
     * @var Mage_Core_Model_Theme
     */
    protected $_theme;

    /**
     * @var string|null
     */
    protected $_locale;

    /**
     * @var Mage_Core_Model_Config
     */
    protected $_appConfig;

    /**
     * Constructor.
     * Following entries in $params are required: 'area', 'package', 'theme', 'locale'. The 'appConfig' and
     * 'themeConfig' may contain application config and theme config, respectively. If these these entries are not
     * present or null, then they will be retrieved from global application instance.
     *
     * @param Magento_Filesystem $filesystem
     * @param array $data
     */
    public function __construct(Magento_Filesystem $filesystem, $data)
    {
        $this->_filesystem = $filesystem;
        $this->_area = $data['area'];
        $this->_locale = $data['locale'];
        $this->_theme = $data['themeModel'];
        $this->_appConfig = isset($data['appConfig']) ? $data['appConfig'] : Mage::getConfig();
    }

    /**
     * Get existing file name, using fallback mechanism
     *
     * @param string $file
     * @param string|null $module
     * @return string
     */
    public function getFile($file, $module = null)
    {
        $dir = $this->_appConfig->getOptions()->getDesignDir();
        $dirs = array();
        $themeModel = $this->_theme;
        while ($themeModel) {
            $themePath = $themeModel->getThemePath();
            if ($themePath) {
                $dirs[] = "{$dir}/{$this->_area}/{$themePath}";
            }
            $themeModel = $themeModel->getParentTheme();
        }

        $moduleDir = $module ? array($this->_appConfig->getModuleDir('view', $module) . "/{$this->_area}") : array();
        return $this->_fallback($file, $dirs, $module, $moduleDir);
    }

    /**
     * Get locale file name, using fallback mechanism
     *
     * @param string $file
     * @return string
     */
    public function getLocaleFile($file)
    {
        $dir = $this->_appConfig->getOptions()->getDesignDir();
        $dirs = array();
        $themeModel = $this->_theme;
        while ($themeModel) {
            $themePath = $themeModel->getThemePath();
            if ($themePath) {
                $dirs[] = "{$dir}/{$this->_area}/{$themePath}/locale/{$this->_locale}";
            }
            $themeModel = $themeModel->getParentTheme();
        }

        return $this->_fallback($file, $dirs);
    }

    /**
     * Get theme file name, using fallback mechanism
     *
     * @param string $file
     * @param string|null $module
     * @return string
     */
    public function getViewFile($file, $module = null)
    {
        $dir = $this->_appConfig->getOptions()->getDesignDir();
        $moduleDir = $module ? $this->_appConfig->getModuleDir('view', $module) : '';

        $dirs = array();
        $themeModel = $this->_theme;
        while ($themeModel) {
            $themePath = $themeModel->getThemePath();
            if ($themePath) {
                $dirs[] = "{$dir}/{$this->_area}/{$themePath}/locale/{$this->_locale}";
                $dirs[] = "{$dir}/{$this->_area}/{$themePath}";
            }
            $themeModel = $themeModel->getParentTheme();
        }

        return $this->_fallback(
            $file,
            $dirs,
            $module,
            array("{$moduleDir}/{$this->_area}/locale/{$this->_locale}", "{$moduleDir}/{$this->_area}"),
            array($this->_appConfig->getOptions()->getJsDir())
        );
    }

    /**
     * Go through specified directories and try to locate the file
     *
     * Returns the first found file or last file from the list as absolute path
     *
     * @param string $file relative file name
     * @param array $themeDirs theme directories (absolute paths) - must not be empty
     * @param string|bool $module module context
     * @param array $moduleDirs module directories (absolute paths, makes sense with previous parameter only)
     * @param array $extraDirs additional lookup directories (absolute paths)
     * @return string
     */
    protected function _fallback($file, $themeDirs, $module = false, $moduleDirs = array(), $extraDirs = array())
    {
        // add customization path
        $dirs = array();
        if ($this->_theme->getCustomizationPath()) {
            $dirs[] = $this->_theme->getCustomizationPath();
        }

        // add modules to lookup
        $dirs = array_merge($dirs, $themeDirs);
        if ($module) {
            array_walk($themeDirs, function (&$dir) use ($module) {
                $dir = "{$dir}/{$module}";
            });
            $dirs = array_merge($themeDirs, $moduleDirs);
        }
        $dirs = array_merge($dirs, $extraDirs);

        // look for files
        $tryFile = '';
        foreach ($dirs as $dir) {
            $tryFile = str_replace('/', DIRECTORY_SEPARATOR, "{$dir}/{$file}");
            if ($this->_filesystem->has($tryFile)) {
                break;
            }
        }
        return $tryFile;
    }

    /**
     * Object notified, that theme file was published, thus it can return published file name on next calls
     *
     * @param string $publicFilePath
     * @param string $file
     * @param string|null $module
     * @return Mage_Core_Model_Design_FallbackInterface
     */
    public function notifyViewFilePublished($publicFilePath, $file, $module = null)
    {
        // Do nothing - we don't cache file paths in real fallback
        return $this;
    }
}
