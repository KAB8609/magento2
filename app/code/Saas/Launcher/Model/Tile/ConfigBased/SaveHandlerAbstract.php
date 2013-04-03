<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Saas_Launcher
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Configuration data save handler
 *
 * Classes that extend this class are responsible for saving of a particular part of configuration
 *
 * @category   Mage
 * @package    Saas_Launcher
 * @author     Magento Core Team <core@magentocommerce.com>
 */
abstract class Saas_Launcher_Model_Tile_ConfigBased_SaveHandlerAbstract
    implements Saas_Launcher_Model_Tile_SaveHandler
{
    /**
     * Config sections that the tile have to save
     *
     * @var array
     */
    protected $_sections = array();

    /**
     * @var Mage_Core_Model_Config
     */
    protected $_config;

    /**
     * @var Mage_Backend_Model_Config
     */
    protected $_backendConfigModel;

    /**
     * @param Mage_Core_Model_Config $config
     * @param Mage_Backend_Model_Config $backendConfigModel
     */
    public function __construct(
        Mage_Core_Model_Config $config,
        Mage_Backend_Model_Config $backendConfigModel
    ) {
        $this->_config = $config;
        $this->_backendConfigModel = $backendConfigModel;
    }


    /**
     * Save configuration data
     *
     * @param array $data
     * @return null
     * @throws Saas_Launcher_Exception
     */
    public function save(array $data)
    {
        $preparedData = $this->prepareData($data);
        foreach ($this->getRelatedConfigSections() as $sectionName) {
            if (isset($preparedData[$sectionName])) {
                $this->_backendConfigModel->setSection($sectionName)
                    ->setGroups($preparedData[$sectionName])
                    ->save();
            }
        }

        $this->_config->reinit();
    }

    /**
     * Retrieve the list of names of the related configuration sections
     *
     * @return array
     */
    abstract public function getRelatedConfigSections();
}
