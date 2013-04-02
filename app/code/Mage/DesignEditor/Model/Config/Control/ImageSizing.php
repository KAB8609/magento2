<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_DesignEditor
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Image Sizing configuration
 */
class Mage_DesignEditor_Model_Config_Control_ImageSizing extends Mage_DesignEditor_Model_Config_Control_Abstract
{
    /**
     * Keys of layout params attributes
     *
     * @var array
     */
    protected $_controlAttributes = array('title');

    /**
     * @var Mage_Core_Model_Config_Modules_Reader
     */
    protected $_moduleReader;

    /**
     * @param Mage_Core_Model_Config_Modules_Reader $moduleReader
     * @param array $configFiles
     */
    public function __construct(Mage_Core_Model_Config_Modules_Reader $moduleReader, array $configFiles)
    {
        $this->_moduleReader = $moduleReader;
        parent::__construct($configFiles);
    }

    /**
     * Path to quick_styles.xsd
     *
     * @return string
     */
    public function getSchemaFile()
    {
        return $this->_moduleReader->getModuleDir('etc', 'Mage_DesignEditor') . DIRECTORY_SEPARATOR
            . 'image_sizing.xsd';
    }
}
