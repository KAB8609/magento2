<?php
/**
 * Local Application configuration loader (app/etc/local.xml)
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\App\Config;

use Magento\Filesystem\DirectoryList;

class Loader
{
    /**
     * Local configuration file
     */
    const PARAM_CUSTOM_FILE = 'custom.options.file';

    /**
     * Local configuration file
     */
    const LOCAL_CONFIG_FILE = 'local.xml';

    /**
     * Directory registry
     *
     * @var string
     */
    protected $_dir;

    /**
     * Custom config file
     *
     * @var string
     */
    protected $_customFile;

    /**
     * Configuration identifier attributes
     *
     * @var array
     */
    protected $_idAttributes = array('/config/resource' => 'name', '/config/connection' => 'name');

    /**
     * @param DirectoryList $dirs
     * @param string $customFile
     */
    public function __construct(DirectoryList $dirList, $customFile = null)
    {
        $this->_dir = $dirList->getDir(DirectoryList::CONFIG);
        $this->_customFile = $customFile;
    }

    /**
     * Load configuration
     *
     * @return array
     */
    public function load()
    {
        $localConfig = new \Magento\Config\Dom('<config/>', $this->_idAttributes);

        $localConfigFile = $this->_dir . DIRECTORY_SEPARATOR . self::LOCAL_CONFIG_FILE;
        if (file_exists($localConfigFile)) {
            // 1. app/etc/local.xml
            $localConfig->merge(file_get_contents($localConfigFile));

            // 2. app/etc/<dir>/<file>.xml
            if (preg_match('/^[a-z\d_-]+(\/|\\\)+[a-z\d_-]+\.xml$/', $this->_customFile)) {
                $localConfigExtraFile = $this->_dir . DIRECTORY_SEPARATOR . $this->_customFile;
                $localConfig->merge(file_get_contents($localConfigExtraFile));
            }
        }

        $converter = new \Magento\Config\Converter\Dom\Flat($this->_idAttributes);

        $result = $converter->convert($localConfig->getDom());
        return !empty($result['config']) ? $result['config'] : array();
    }
}
