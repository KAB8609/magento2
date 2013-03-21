<?php
/**
 * Primary application config (app/etc/*.xml)
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Core_Model_Config_Primary extends Mage_Core_Model_Config_Base implements Magento_ObjectManager_Configuration
{
    /**
     * Install date xpath
     */
    const XML_PATH_INSTALL_DATE = 'global/install/date';

    /**
     * Configuration template for the application installation date
     */
    const CONFIG_TEMPLATE_INSTALL_DATE = '<config><global><install><date>%s</date></install></global></config>';

    /**
     * Application installation timestamp
     *
     * @var int|null
     */
    protected $_installDate;

    /**
     * @var Mage_Core_Model_Config_Loader_Primary
     */
    protected $_loader;

    /**
     * Application parameter list
     *
     * @var array
     */
    protected $_params;

    /**
     * Directory list
     *
     * @var Mage_Core_Model_Dir
     */
    protected $_dir;

    /**
     * @param string $baseDir
     * @param array $params
     */
    public function __construct($baseDir, array $params)
    {
        parent::__construct('<config/>');
        $this->_params = $params;
        $this->_dir = new Mage_Core_Model_Dir(
            new Magento_Filesystem(new Magento_Filesystem_Adapter_Local()),
            $baseDir,
            $this->getParam(Mage::PARAM_APP_URIS, array()),
            $this->getParam(Mage::PARAM_APP_DIRS, array())
        );
        $this->_loader = new Mage_Core_Model_Config_Loader_Primary(
            new Mage_Core_Model_Config_Loader_Local(
                $this->_dir->getDir(Mage_Core_Model_Dir::CONFIG),
                $this->getParam(Mage::PARAM_CUSTOM_LOCAL_CONFIG),
                $this->getParam(Mage::PARAM_CUSTOM_LOCAL_FILE)
            ),
            $this->_dir->getDir(Mage_Core_Model_Dir::CONFIG)
        );
        $this->_loader->load($this);
        $this->_loadInstallDate();
    }

    /**
     * Get init param
     *
     * @param string $name
     * @param mixed $defaultValue
     * @return mixed
     */
    public function getParam($name, $defaultValue = null)
    {
        return isset($this->_params[$name]) ? $this->_params[$name] : $defaultValue;
    }

    /**
     * Load application installation date
     */
    protected function _loadInstallDate()
    {
        $installDateNode = $this->getNode(self::XML_PATH_INSTALL_DATE);
        if ($installDateNode) {
            $this->_installDate = strtotime((string)$installDateNode);
        }
    }

    /**
     * Retrieve application installation date as a timestamp or NULL, if it has not been installed yet
     *
     * @return int|null
     */
    public function getInstallDate()
    {
        return $this->_installDate;
    }

    /**
     * Retrieve directories
     *
     * @return Mage_Core_Model_Dir
     */
    public function getDirectories()
    {
        return $this->_dir;
    }

    /**
     * Reinitialize primary configuration
     */
    public function reinit()
    {
        $this->loadString('<config/>');
        $this->_loader->load($this);
        $this->_loadInstallDate();
    }

    /**
     * Retrieve class definition config
     *
     * @return string
     */
    public function getDefinitionPath()
    {
        $pathInfo = (array) $this->getNode('global/di/definitions');
        if (isset($pathInfo['path'])) {
            return $pathInfo['path'];
        } else if (isset($pathInfo['relativePath'])) {
            return $this->_dir->getDir(Mage_Core_Model_Dir::ROOT) . DIRECTORY_SEPARATOR . $pathInfo['relativePath'];
        } else {
            return $this->_dir->getDir(Mage_Core_Model_Dir::DI) . DIRECTORY_SEPARATOR . 'definitions.php';
        }
    }

    /**
     * Retrieve definition format
     *
     * @return string
     */
    public function getDefinitionFormat()
    {
        return (string) $this->getNode('global/di/definitions/format');
    }

    /**
     * Configure object manager
     *
     * @param Magento_ObjectManager $objectManager
     */
    public function configure(Magento_ObjectManager $objectManager)
    {
        Magento_Profiler::start('initial');
        $objectManager->configure(array(
            'Mage_Core_Model_Config_Loader_Local' => array(
                'parameters' => array(
                    'customFile' => $this->getParam(Mage::PARAM_CUSTOM_LOCAL_FILE),
                    'customConfig' => $this->getParam(Mage::PARAM_CUSTOM_LOCAL_CONFIG)
                )
            ),
            'Mage_Core_Model_Config_Loader_Modules' => array(
                'parameters' => array(
                    'allowedModules' => $this->getParam(Mage::PARAM_ALLOWED_MODULES, array())
                )
            ),
            'Mage_Core_Model_Cache_Frontend_Factory' => array(
                'parameters' => array(
                    'enforcedOptions' => $this->getParam(Mage::PARAM_CACHE_OPTIONS, array()),
                    'decorators' => $this->_getCacheFrontendDecorators(),
                )
            ),
            'Mage_Core_Model_Cache_Types' => array(
                'parameters' => array(
                    'banAll' => $this->getParam(Mage::PARAM_BAN_CACHE, false),
                )
            ),
            'Mage_Core_Model_StoreManager' => array(
                'parameters' => array(
                    'scopeCode' => $this->getParam(Mage::PARAM_RUN_CODE, ''),
                    'scopeType' => $this->getParam(Mage::PARAM_RUN_TYPE, 'store'),
                )
            )
        ));

        $configurators = $this->getNode('global/configurators');
        if ($configurators) {
            $configurators = $configurators->asArray();
            if (count($configurators)) {
                foreach ($configurators as $configuratorClass) {
                    /** @var $configurator  Magento_ObjectManager_Configuration*/
                    $configurator = $objectManager->create($configuratorClass, array('params' => $this->_params));
                    $configurator->configure($objectManager);
                }
            }
        }
        Magento_Profiler::stop('initial');
        Magento_Profiler::start('global_primary');
        $diConfig = $this->getNode('global/di');
        if ($diConfig) {
            $objectManager->configure($diConfig->asArray());
        }

        Magento_Profiler::stop('global_primary');
    }

    /**
     * Retrieve cache frontend decorators configuration
     *
     * @return array
     */
    protected function _getCacheFrontendDecorators()
    {
        $result = array();
        // mark all cache entries with a special tag to be able to clean only cache belonging to the application
        $result[] = array(
            'class' => 'Magento_Cache_Frontend_Decorator_TagMarker',
            'parameters' => array('tag' => Mage_Core_Model_AppInterface::CACHE_TAG),
        );
        if (Magento_Profiler::isEnabled()) {
            $result[] = array(
                'class' => 'Magento_Cache_Frontend_Decorator_Profiler',
                'parameters' => array('backendPrefixes' => array('Zend_Cache_Backend_', 'Varien_Cache_Backend_')),
            );
        }
        return $result;
    }
}
