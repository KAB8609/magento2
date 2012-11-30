<?php
/**
 * {license_notice}
 *
 * @category   Magento
 * @package    tools
 * @copyright  {copyright}
 * @license    {license_link}
 */


class Tools_Migration_System_Configuration_Generator
{
    /**
     * @var Tools_Migration_System_FileManager
     */
    protected $_fileManager;

    /**
     * @var Tools_Migration_System_Configuration_Formatter
     */
    protected $_xmlFormatter;

    /**
     * @var Tools_Migration_System_Configuration_LoggerAbstract
     */
    protected $_logger;

    /**
     * Base directory path
     *
     * @var string
     */
    protected $_basePath;

    /**
     * @var Tools_Migration_System_Configuration_LoggerAbstract
     */
    protected $_fileSchemaPath;

    public function __construct(
        Tools_Migration_System_Configuration_Formatter $xmlFormatter,
        Tools_Migration_System_FileManager $fileManager,
        Tools_Migration_System_Configuration_LoggerAbstract $logger
    ) {
        $this->_fileManager = $fileManager;
        $this->_xmlFormatter = $xmlFormatter;
        $this->_logger = $logger;


        $this->_basePath = realpath(dirname(__FILE__) . '/../../../../../');
        $this->_fileSchemaPath = $this->_basePath
            . '/app/code/core/Mage/Backend/Model/Config/Structure/system_file.xsd';
    }

    /**
     * Create configuration array from xml file
     *
     * @param string $fileName
     * @param array $configuration
     */
    public function createConfiguration($fileName, array $configuration)
    {
        $domDocument = $this->_createDOMDocument($configuration);
        if (@!$domDocument->schemaValidate($this->_fileSchemaPath)) {
            $this->_logger->add(
                $this->_removeBasePath($fileName),
                Tools_Migration_System_Configuration_LoggerAbstract::FILE_KEY_INVALID
            );
        } else {
            $this->_logger->add(
                $this->_removeBasePath($fileName),
                Tools_Migration_System_Configuration_LoggerAbstract::FILE_KEY_VALID
            );
        }

        $output = $this->_xmlFormatter->parseString($domDocument->saveXml(), array(
            'indent' => true,
            'input-xml' => true,
            'output-xml' => true,
            'add-xml-space' => false,
            'indent-spaces' => 4,
            'wrap' => 1000
        ));
        $newFileName = $this->_getPathToSave($fileName);
        $this->_fileManager->write($newFileName, $output);
    }

    /**
     *  Create DOM document based on configuration
     *
     * @param array $configuration
     * @return DOMDocument
     */
    protected function _createDOMDocument(array $configuration)
    {
        $dom = new DOMDocument();
        $configElement = $dom->createElement('config');
        $systemElement = $dom->createElement('system');
        $configElement->appendChild($systemElement);
        $dom->appendChild($configElement);

        foreach ($configuration['nodes'] as $config) {
            $element = $this->_createElement($config, $dom);
            $systemElement->appendChild($element);
        }
        return $dom;
    }

    /**
     * Create element
     *
     * @param array $config
     * @param DOMDocument $dom
     * @return DOMElement
     */
    protected function _createElement($config, DOMDocument $dom)
    {
        $element = $dom->createElement($this->_getValue($config, 'nodeName'), $this->_getValue($config, '#text', ''));
        if ($this->_getValue($config, '#cdata-section')) {
            $cdataSection = $dom->createCDATASection($this->_getValue($config, '#cdata-section', ''));
            $element->appendChild($cdataSection);
        }

        foreach ($this->_getValue($config, '@attributes', array()) as $attributeName => $attributeValue) {
            $element->setAttribute($attributeName, $attributeValue);
        }

        foreach ($this->_getValue($config, 'parameters', array()) as $paramConfig) {
            if ($this->_getValue($paramConfig, 'name') == '#text') {
                $element->nodeValue = $this->_getValue($paramConfig, 'value', '');
                continue;
            }

            $childElement = $dom->createElement($paramConfig['name'], $this->_getValue($paramConfig, '#text', ''));

            if ($this->_getValue($paramConfig, '#cdata-section')) {
                $childCDataSection = $dom->createCDATASection($this->_getValue($paramConfig, '#cdata-section'));
                $childElement->appendChild($childCDataSection);
            }

            foreach ($this->_getValue($paramConfig, '@attributes', array()) as $attributeName => $attributeValue) {
                $childElement->setAttribute($attributeName, $attributeValue);
            }

            foreach ($this->_getValue($paramConfig, 'subConfig', array()) as $subConfig) {
                $childElement->appendChild($this->_createElement($subConfig, $dom));
            }

            $element->appendChild($childElement);
        }

        foreach ($this->_getValue($config, 'subConfig', array()) as $subConfig) {
            $element->appendChild($this->_createElement($subConfig, $dom));
        }

        return $element;
    }

    /**
     * Get value from array by key
     *
     * @param array $source
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    protected function _getValue($source, $key, $default = null)
    {
        return array_key_exists($key, $source) ? $source[$key] : $default;
    }

    /**
     * Get new path to system configuration file
     *
     * @param $fileName
     * @return string
     */
    protected function _getPathToSave($fileName)
    {
        return dirname($fileName) . DIRECTORY_SEPARATOR . 'adminhtml' . DIRECTORY_SEPARATOR . 'system.xml';
    }

    /**
     * Remove path to magento application
     *
     * @param $filename
     * @return string
     */
    protected function _removeBasePath($filename)
    {
        return str_replace($this->_basePath . DIRECTORY_SEPARATOR, '', $filename);
    }
}
