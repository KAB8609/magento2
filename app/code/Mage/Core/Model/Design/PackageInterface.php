<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

interface Mage_Core_Model_Design_PackageInterface
{
    /**
     * Default design area
     */
    const DEFAULT_AREA = 'frontend';

    /**
     * Scope separator
     */
    const SCOPE_SEPARATOR = '::';

    /**#@+
     * Public directories prefix group
     */
    const PUBLIC_MERGE_DIR  = '_merged';
    const PUBLIC_MODULE_DIR = '_module';
    const PUBLIC_VIEW_DIR   = '_view';
    const PUBLIC_THEME_DIR  = '_theme';
    /**#@-*/

    /**
     * Public directory which contain theme files
     */
    const PUBLIC_BASE_THEME_DIR = 'static';

    /**#@+
     * Extensions group for static files
     */
    const CONTENT_TYPE_CSS = 'css';
    const CONTENT_TYPE_JS  = 'js';
    /**#@-*/

    /**#@+
     * Protected extensions group for publication mechanism
     */
    const CONTENT_TYPE_PHP   = 'php';
    const CONTENT_TYPE_PHTML = 'phtml';
    const CONTENT_TYPE_XML   = 'xml';
    /**#@-*/

    /**
     * The name of the default theme in the context of a package
     */
    const DEFAULT_THEME_NAME = 'default';

    /**
     * Published file cache storage tag
     */
    const PUBLIC_CACHE_TAG = 'design_public';

    /**#@+
     * Common node path to theme design configuration
     */
    const XML_PATH_THEME    = 'design/theme/full_name';
    const XML_PATH_THEME_ID = 'design/theme/theme_id';
    /**#@-*/

    /**
     * Path to configuration node that indicates how to materialize view files: with or without "duplication"
     */
    const XML_PATH_ALLOW_DUPLICATION = 'global/design/theme/allow_view_files_duplication';

    /**
     * Path to config node that allows automatically updating map files in runtime
     */
    const XML_PATH_ALLOW_MAP_UPDATE = 'global/dev/design_fallback/allow_map_update';

    /**
     * Sub-directory where to store maps of view files fallback (if used)
     */
    const FALLBACK_MAP_DIR = 'maps/fallback';

    /**
     * PCRE that matches non-absolute URLs in CSS content
     */
    const REGEX_CSS_RELATIVE_URLS
        = '#url\s*\(\s*(?(?=\'|").)(?!http\://|https\://|/|data\:)(.+?)(?:[\#\?].*?|[\'"])?\s*\)#';

    /**
     * Filename of view configuration
     */
    const FILENAME_VIEW_CONFIG = 'view.xml';

    /**
     * Set package area
     *
     * @param string $area
     * @return Mage_Core_Model_Design_PackageInterface
     */
    public function setArea($area);

    /**
     * Retrieve package area
     *
     * @return string
     */
    public function getArea();


    /**
     * Set theme path
     *
     * @param Mage_Core_Model_Theme|int|string $theme
     * @param string $area
     * @return Mage_Core_Model_Design_PackageInterface
     */
    public function setDesignTheme($theme, $area = null);


    /**
     * Get default theme which declared in configuration
     *
     * @param string $area
     * @param array $params
     * @return string|int
     */
    public function getConfigurationDesignTheme($area = null, array $params = array());

    /**
     * Set default design theme
     *
     * @return Mage_Core_Model_Design_PackageInterface
     */
    public function setDefaultDesignTheme();

    /**
     * Design theme model getter
     *
     * @return Mage_Core_Model_Theme
     */
    public function getDesignTheme();

    /**
     * Get existing file name with fallback to default
     *
     * @param string $file
     * @param array $params
     * @return string
     */
    public function getFilename($file, array $params = array());

    /**
     * Get a locale file
     *
     * @param string $file
     * @param array $params
     * @return string
     */
    public function getLocaleFileName($file, array $params = array());

    /**
     * Find a view file using fallback mechanism
     *
     * @param string $file
     * @param array $params
     * @return string
     */
    public function getViewFile($file, array $params = array());

    /**
     * Remove all merged js/css files
     *
     * @return bool
     */
    public function cleanMergedJsCss();

    /**
     * Get url to file base on theme file identifier.
     * Publishes file there, if needed.
     *
     * @param string $file
     * @param array $params
     * @return string
     */
    public function getViewFileUrl($file, array $params = array());

    /**
     * Get URLs to CSS files optimized based on configuration settings
     *
     * @param array $files
     * @return array
     */
    public function getOptimalCssUrls($files);

    /**
     * Get URLs to JS files optimized based on configuration settings
     *
     * @param array $files
     * @return array
     */
    public function getOptimalJsUrls($files);

    /**
     * Return directory for theme files publication
     *
     * @return string
     */
    public function getPublicDir();

    /**
     * Render view config object for current package and theme
     *
     * @return Magento_Config_View
     */
    public function getViewConfig();
}
