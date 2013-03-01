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
 * System Configuration Dependencies Mapper
 */
class Mage_Backend_Model_Config_Structure_Mapper_Dependencies extends Mage_Backend_Model_Config_Structure_MapperAbstract
{
    /**
     * Class that can convert relative paths from "depends" node to absolute
     *
     * @var Mage_Backend_Model_Config_Structure_Mapper_Helper_RelativePathConverter
     */
    protected $_pathConverter;

    /**
     * @param Mage_Backend_Model_Config_Structure_Mapper_Helper_RelativePathConverter $pathConverted
     */
    public function __construct(Mage_Backend_Model_Config_Structure_Mapper_Helper_RelativePathConverter $pathConverted)
    {
        $this->_pathConverter = $pathConverted;
    }

    /**
     * Apply map
     *
     * @param array $data
     * @return array
     */
    public function map(array $data)
    {
        if ($this->_hasValue('config/system/sections', $data)) {
            foreach ($data['config']['system']['sections'] as &$sectionConfig) {
                $sectionConfig = $this->_processConfig($sectionConfig);
            }
        }
        return $data;
    }

    /**
     * Process configuration
     *
     * @param array $config
     * @return array
     */
    protected function _processConfig($config)
    {
        $config = $this->_processDepends($config);

        if ($this->_hasValue('children', $config)) {
            foreach ($config['children'] as &$subConfig) {
                $subConfig = $this->_processConfig($subConfig);
            }
        }
        return $config;
    }

    /**
     * Process dependencies configuration
     *
     * @param array $config
     * @return array
     */
    protected function _processDepends($config)
    {
        if ($this->_hasValue('depends/fields', $config)) {
            foreach ($config['depends']['fields'] as &$field) {
                $dependPath = $this->_getDependPath($field, $config);
                $field['dependPath'] = $dependPath;
                $field['id'] = implode('/', $dependPath);
            }
        }
        return $config;
    }

    /**
     * Get depend path
     *
     * @param array $field
     * @param array $config
     * @return array
     * @throws InvalidArgumentException
     */
    protected function _getDependPath($field, $config)
    {
        $dependPath = $field['id'];
        if (strpos($dependPath, '/') === false) {
            $dependPath = '*/*/' .  $dependPath;
        }
        $elementPath = $config['path'] . '/' . $config['id'];

        return explode('/', $this->_pathConverter->convert($elementPath, $dependPath));
    }
}
