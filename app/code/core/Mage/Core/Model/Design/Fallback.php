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
     * @var string
     */
    protected $_package;

    /**
     * @var string
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
     * @var Magento_Config_Theme
     */
    protected $_themeConfig;

    /**
     * Constructor.
     * Following entries in $params are required: 'area', 'package', 'theme', 'locale'. The 'appConfig' and
     * 'themeConfig' may contain application config and theme config, respectively. If these these entries are not
     * present or null, then they will be retrieved from global application instance.
     *
     * @param array $data
     */
    public function __construct($data)
    {
        $this->_area = $data['area'];
        $this->_package = $data['package'];
        $this->_theme = $data['theme'];
        $this->_locale = $data['locale'];
        $this->_appConfig = isset($data['appConfig']) ? $data['appConfig'] : Mage::getConfig();
        $this->_themeConfig = isset($data['themeConfig']) ? $data['themeConfig']
            : Mage::getDesign()->getThemeConfig($this->_area);
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
        $theme = $this->_theme;
        $package = $this->_package;
        while ($theme) {
            $dirs[] = "{$dir}/{$this->_area}/{$package}/{$theme}";
            list($package, $theme) = $this->_getInheritedTheme($package, $theme);
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
        $package = $this->_package;
        $theme = $this->_theme;
        while ($theme) {
            $dirs[] = "{$dir}/{$this->_area}/{$package}/{$theme}/locale/{$this->_locale}";
            list($package, $theme) = $this->_getInheritedTheme($package, $theme);
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
        $theme = $this->_theme;
        $package = $this->_package;
        while ($theme) {
            $dirs[] = "{$dir}/{$this->_area}/{$package}/{$theme}/locale/{$this->_locale}";
            $dirs[] = "{$dir}/{$this->_area}/{$package}/{$theme}";
            list($package, $theme) = $this->_getInheritedTheme($package, $theme);
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
     * @param string|false $module module context
     * @param array $moduleDirs module directories (absolute paths, makes sense with previous parameter only)
     * @param array $extraDirs additional lookup directories (absolute paths)
     * @return string
     */
    protected function _fallback($file, $themeDirs, $module = false, $moduleDirs = array(), $extraDirs = array())
    {
        // add modules to lookup
        $dirs = $themeDirs;
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
            if (file_exists($tryFile)) {
                break;
            }
        }
        return $tryFile;
    }

    /**
     * Get the name of the inherited theme
     *
     * If the specified theme inherits other theme the result is the name of inherited theme.
     * If the specified theme does not inherit other theme the result is null.
     *
     * @param string $package
     * @param string $theme
     * @return string|null
     */
    protected function _getInheritedTheme($package, $theme)
    {
        return $this->_themeConfig ? $this->_themeConfig->getParentTheme($package, $theme) : null;
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
