<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Menu configuration files handler
 */
class Mage_Backend_Model_Config_Menu extends Magento_Config_XmlAbstract
{
    /**
     * Path to menu.xsd
     *
     * @return string
     */
    public function getSchemaFile()
    {
        return __DIR__ . '/menu.xsd';
    }

    /**
     * Extract configuration data from the DOM structure
     *
     * @param DOMDocument $dom
     * @return array
     */
    protected function _extractData(DOMDocument $dom)
    {
        return array();
    }

    /**
     * Getter for initial menu.xml contents
     *
     * @return string
     */
    protected function _getInitialXml()
    {
        return '<?xml version="1.0" encoding="utf-8"?><config><menu></menu></config>';
    }

    /**
     * Variables are identified by module and name
     *
     * @return array
     */
    protected function _getIdAttributes()
    {
        return array();
    }

    /**
     * Get merged configuration
     * @return DOMDocument
     */
    public function getMergedConfig()
    {
        return $this->_getDomConfigModel()->getDom();
    }

    /**
     * Get Dom configuration model
     * @return Mage_Backend_Model_Config_Menu_Dom
     */
    protected function _getDomConfigModel()
    {
        if (is_null($this->_domConfig)) {
            $this->_domConfig = new Mage_Backend_Model_Config_Menu_Dom(
                $this->_getInitialXml(),
                $this->_getIdAttributes()
            );
        }
        return $this->_domConfig;
    }

    /**
     * Perform xml validation
     * @return Magento_Config_XmlAbstract
     * @throws Magento_Exception if invalid XML-file passed
     */
    public function validate()
    {
        return $this->_performValidate();
    }

    /**
     * Get if xml files must be runtime validated
     * @return boolean
     */
    protected function _isRuntimeValidated()
    {
        return false;
    }
}
