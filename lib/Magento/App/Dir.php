<?php
/**
 * Application file system directories dictionary
 *
 * Provides information about what directories are available in the application
 * Serves as customization point to specify different directories or add own
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\App;

class Dir
{
    /**
     * Custom application dirs
     */
    const PARAM_APP_DIRS = 'app_dirs';

    /**
     * Custom application uris
     */
    const PARAM_APP_URIS = 'app_uris';

    /**
     * Code base root
     */
    const ROOT = 'base';

    /**
     * Most of entire application
     */
    const APP = 'app';

    /**
     * Modules
     */
    const MODULES = 'code';

    /**
     * Themes
     */
    const THEMES = 'design';

    /**
     * Initial configuration of the application
     */
    const CONFIG = 'etc';

    /**
     * Libraries or third-party components
     */
    const LIB = 'lib';

    /**
     * Files with translation of system labels and messages from en_US to other languages
     */
    const LOCALE = 'i18n';

    /**
     * \Directory within document root of a web-server to access static view files publicly
     */
    const PUB = 'pub';

    /**
     * Libraries/components that need to be accessible publicly through web-server (such as various DHTML components)
     */
    const PUB_LIB = 'pub_lib';

    /**
     * Storage of files entered or generated by the end-user
     */
    const MEDIA = 'media';

    /**
     * Storage of static view files that are needed on HTML-pages, emails or similar content
     */
    const STATIC_VIEW = 'static';

    /**
     * Public view files, stored to avoid repetitive run-time calculation, and can be re-generated any time
     */
    const PUB_VIEW_CACHE = 'view_cache';

    /**
     * Various files generated by the system in runtime
     */
    const VAR_DIR = 'var';

    /**
     * Temporary files
     */
    const TMP = 'tmp';

    /**
     * File system caching directory (if file system caching is used)
     */
    const CACHE = 'cache';

    /**
     * Logs of system messages and errors
     */
    const LOG = 'log';

    /**
     * File system session directory (if file system session storage is used)
     */
    const SESSION = 'session';

    /**
     * Dependency injection related file directory
     *
     */
    const DI = 'di';

    /**
     * Relative directory key for generated code
     */
    const GENERATION = 'generation';

    /**
     * Temporary directory for uploading files by end-user
     */
    const UPLOAD = 'upload';

    /**
     * Default values for directories (and URIs)
     *
     * Format: array(<code> => <relative_path>)
     *
     * @var array
     */
    private static $_defaults = array(
        self::ROOT          => '',
        self::APP           => 'app',
        self::MODULES       => 'app/code',
        self::THEMES        => 'app/design',
        self::CONFIG        => 'app/etc',
        self::LIB           => 'lib',
        self::VAR_DIR       => 'var',
        self::TMP           => 'var/tmp',
        self::CACHE         => 'var/cache',
        self::LOG           => 'var/log',
        self::SESSION       => 'var/session',
        self::DI            => 'var/di',
        self::GENERATION    => 'var/generation',
        self::PUB           => 'pub',
        self::PUB_LIB       => 'pub/lib',
        self::MEDIA         => 'pub/media',
        self::UPLOAD        => 'pub/media/upload',
        self::STATIC_VIEW   => 'pub/static',
        self::PUB_VIEW_CACHE => 'pub/cache',
    );

    /**
     * Paths of URIs designed for building URLs
     *
     * Values are to be initialized in constructor.
     * They are declared like this here for convenience of distinguishing which directories are intended to be URIs.
     *
     * @var array
     */
    private $_uris = array(
        self::PUB           => '',
        self::PUB_LIB       => '',
        self::MEDIA         => '',
        self::STATIC_VIEW   => '',
        self::PUB_VIEW_CACHE => '',
        self::UPLOAD        => '',
    );

    /**
     * Absolute paths to directories
     *
     * @var array
     */
    private $_dirs = array();

    /**
     * Initialize URIs and paths
     *
     * @param string $baseDir
     * @param array $uris custom URIs
     * @param array $dirs custom directories (full system paths)
     */
    public function __construct($baseDir, array $uris = array(), array $dirs = array())
    {
        // uris
        foreach (array_keys($this->_uris) as $code) {
            $this->_uris[$code] = self::$_defaults[$code];
        }
        foreach ($uris as $code => $uri) {
            $this->_setUri($code, $uri);
        }
        foreach ($this->_getDefaultReplacements($uris) as $code => $replacement) {
            $this->_setUri($code, $replacement);
        }

        // dirs
        foreach (self::$_defaults as $code => $path) {
            $this->_setDir($code, $baseDir . ($path ? DIRECTORY_SEPARATOR . $path : ''));
        }
        foreach ($dirs as $code => $path) {
            $this->_setDir($code, $path);
        }
        foreach ($this->_getDefaultReplacements($dirs) as $code => $replacement) {
            $this->_setDir($code, $replacement);
        }
    }

    /**
     * URI getter
     *
     * @param string $code
     * @return string|bool
     */
    public function getUri($code)
    {
        return isset($this->_uris[$code]) ? $this->_uris[$code] : false;
    }

    /**
     * Set URI
     *
     * The method is private on purpose: it must be used only in constructor. Users of this object must not be able
     * to alter its state, otherwise it may compromise application integrity.
     * Path must be usable as a fragment of a URL path.
     * For interoperability and security purposes, no uppercase or "upper directory" paths like "." or ".."
     *
     * @param $code
     * @param $uri
     * @throws \InvalidArgumentException
     */
    private function _setUri($code, $uri)
    {
        if (!preg_match('/^([a-z0-9_]+[a-z0-9\._]*(\/[a-z0-9_]+[a-z0-9\._]*)*)?$/', $uri)) {
            throw new \InvalidArgumentException(
                "Must be relative directory path in lowercase with '/' directory separator: '{$uri}'"
            );
        }
        $this->_uris[$code] = $uri;
    }

    /**
     * \Directory path getter
     *
     * @param string $code One of self const
     * @return string|bool
     */
    public function getDir($code = self::ROOT)
    {
        return isset($this->_dirs[$code]) ? $this->_dirs[$code] : false;
    }

    /**
     * Set directory
     *
     * @param string $code
     * @param string $path
     */
    private function _setDir($code, $path)
    {
        $this->_dirs[$code] = str_replace(array('\\', '/'), DIRECTORY_SEPARATOR, $path);
    }

    /**
     * Using default relations, find replacements for child directories if their parent has changed
     *
     * For example, "var" has children "var/tmp" and "var/cache". If "var" is customized as "var.test", and its children
     * are not, then they will be automatically replaced to "var.test/tmp" and "var.test/cache"
     *
     * @param array $source
     * @return array
     */
    private function _getDefaultReplacements(array $source)
    {
        $result = array();
        foreach ($source as $parentCode => $parent) {
            foreach ($this->_getChildren($parentCode) as $childCode) {
                if (!isset($source[$childCode])) {
                    if (empty(self::$_defaults[$parentCode])) {
                        $fragment = self::$_defaults[$childCode];
                    } else {
                        $fragment = str_replace(self::$_defaults[$parentCode], '', self::$_defaults[$childCode]);
                    }
                    $fragment = ltrim($fragment, '/');
                    if (!empty($parent)) {
                        $fragment = '/' . $fragment;
                    }
                    $result[$childCode] = $parent . $fragment;
                }
            }
        }
        return $result;
    }

    /**
     * Analyze defaults and determine child codes of specified element
     *
     * @param string $code
     * @return array
     */
    private function _getChildren($code)
    {
        $result = array();
        if (!isset(self::$_defaults[$code])) {
            return $result;
        }
        $parent = self::$_defaults[$code];
        foreach (self::$_defaults as $childCode => $child) {
            if ($code != $childCode) {
                if ($parent && $child && 0 === strpos($child, $parent)) {
                    $result[] = $childCode;
                } elseif (empty($parent)) {
                    $result[] = $childCode;
                }
            }
        }
        return $result;
    }
}
