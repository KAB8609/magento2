<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Framework
 * @subpackage  Config
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Configuration XML-files merger
 */
namespace Magento\Config;

abstract class AbstractXml
{
    /**
     * Data extracted from the merged configuration files
     *
     * @var array
     */
    protected $_data;

    /**
     * Dom configuration model
     * @var \Magento\Config\Dom
     */
    protected $_domConfig = null;

    /**
     * Instantiate with the list of files to merge
     *
     * @param $configFiles
     * @throws \InvalidArgumentException
     */
    public function __construct($configFiles)
    {
        if (empty($configFiles)) {
            throw new \InvalidArgumentException('There must be at least one configuration file specified.');
        }
        $this->_data = $this->_extractData($this->_merge($configFiles));
    }

    /**
     * Get absolute path to the XML-schema file
     *
     * @return string
     */
    abstract public function getSchemaFile();

    /**
     * Get absolute path to per-file XML-schema file
     *
     * @return string
     */
    public function getPerFileSchemaFile()
    {
        return null;
    }

    /**
     * Extract configuration data from the DOM structure
     *
     * @param \DOMDocument $dom
     * @return array
     */
    abstract protected function _extractData(\DOMDocument $dom);

    /**
     * Merge the config XML-files
     *
     * @param array $configFiles
     * @return \DOMDocument
     * @throws \Magento\Exception if a non-existing or invalid XML-file passed
     */
    protected function _merge($configFiles)
    {
        foreach ($configFiles as $key => $content) {
            try {
                $this->_getDomConfigModel()->merge($content);
            } catch (\Magento\Config\Dom\ValidationException $e) {
                throw new \Magento\Exception("Invalid XML in file " . $key . ":\n" . $e->getMessage());
            }
        }
        if ($this->_isRuntimeValidated()) {
            $this->_performValidate();
        }
        return $this->_getDomConfigModel()->getDom();
    }

    /**
     * Perform xml validation
     *
     * @param string $file
     * @return \Magento\Config\AbstractXml
     * @throws \Magento\Exception if invalid XML-file passed
     */
    protected function _performValidate($file = null)
    {
        if (!$this->_getDomConfigModel()->validate($this->getSchemaFile(), $errors)) {
            $message = is_null($file) ?  "Invalid Document \n" : "Invalid XML-file: {$file}\n";
            throw new \Magento\Exception($message . implode("\n", $errors));
        }
        return $this;
    }

    /**
     * Get if xml files must be runtime validated
     *
     * @return boolean
     */
    protected function _isRuntimeValidated()
    {
        return true;
    }

    /**
     * Get Dom configuration model
     *
     * @return \Magento\Config\Dom
     * @throws \Magento\Config\Dom\ValidationException
     */
    protected function _getDomConfigModel()
    {
        if (is_null($this->_domConfig)) {
            $schemaFile = $this->getPerFileSchemaFile() && $this->_isRuntimeValidated()
                ? $this->getPerFileSchemaFile()
                : null;
            $this->_domConfig =
                new \Magento\Config\Dom($this->_getInitialXml(), $this->_getIdAttributes(), $schemaFile);
        }
        return $this->_domConfig;
    }

    /**
     * Get XML-contents, initial for merging
     *
     * @return string
     */
    abstract protected function _getInitialXml();

    /**
     * Get list of paths to identifiable nodes
     *
     * @return array
     */
    abstract protected function _getIdAttributes();
}
