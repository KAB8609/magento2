<?php
/**
 * Initialize application object manager.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\App;

use Magento\Filesystem\DirectoryList,
    Magento\App\Config,
    Magento\ObjectManager\Factory\Factory,
    Magento\Profiler;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * Class ObjectManagerFactory
 * @package Magento\App
 */
class ObjectManagerFactory
{
    /**
     * Locator class name
     *
     * @var string
     */
    protected $_locatorClassName = '\Magento\ObjectManager\ObjectManager';

    /**
     * Config class name
     *
     * @var string
     */
    protected $_configClassName = '\Magento\ObjectManager\Config\Config';

    /**
     * Create object manager
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     *
     * @param string $rootDir
     * @param array $arguments
     * @return \Magento\ObjectManager\ObjectManager
     * @throws \Magento\BootstrapException
     */
    public function create($rootDir, array $arguments)
    {
        $directories = new \Magento\Filesystem\DirectoryList(
            $rootDir,
            isset($arguments[\Magento\Filesystem\DirectoryList::PARAM_APP_URIS])
                ? $arguments[\Magento\Filesystem\DirectoryList::PARAM_APP_URIS]
                : array(),
            isset($arguments[\Magento\Filesystem\DirectoryList::PARAM_APP_DIRS])
                ? $arguments[\Magento\Filesystem\DirectoryList::PARAM_APP_DIRS]
                : array()
        );

        \Magento\Autoload\IncludePath::addIncludePath(array($directories->getDir(\Magento\Filesystem::GENERATION)));

        $options = new Config(
            $arguments,
            new Config\Loader(
                $directories,
                isset($arguments[Config\Loader::PARAM_CUSTOM_FILE])
                    ? $arguments[Config\Loader::PARAM_CUSTOM_FILE]
                    : null
            )
        );

        $definitionFactory = new \Magento\ObjectManager\DefinitionFactory(
            new \Magento\Filesystem\Driver\Base(),
            $directories->getDir(\Magento\Filesystem::DI),
            $directories->getDir(\Magento\Filesystem::GENERATION),
            $options->get('definition.format', 'serialized')
        );

        $definitions = $definitionFactory->createClassDefinition($options->get('definitions'));
        $relations = $definitionFactory->createRelations();
        $configClass = $this->_configClassName;
        /** @var \Magento\ObjectManager\Config\Config $diConfig */
        $diConfig = new $configClass($relations, $definitions);
        $appMode = $options->get(State::PARAM_MODE, State::MODE_DEFAULT);

//        $configDirectoryPath = $directories->getDir(DIR::CONFIG);
        $configData = $this->_loadPrimaryConfig($directories->getDir(\Magento\Filesystem::ROOT), $appMode);

        if ($configData) {
            $diConfig->extend($configData);
        }

        $factory = new Factory($diConfig, null, $definitions, $options->get());

        $className = $this->_locatorClassName;
        /** @var \Magento\ObjectManager $locator */
        $locator = new $className($factory, $diConfig, array(
            'Magento\App\Config' => $options,
            'Magento\Filesystem\DirectoryList' => $directories
        ));

        \Magento\App\ObjectManager::setInstance($locator);

        /** @var \Magento\Filesystem\DirectoryList\Verification $verification */
        $verification = $locator->get('Magento\Filesystem\DirectoryList\Verification');
        $verification->createAndVerifyDirectories();

        $diConfig->setCache($locator->get('Magento\App\ObjectManager\ConfigCache'));
        $locator->configure(
            $locator->get('Magento\App\ObjectManager\ConfigLoader')->load('global')
        );
        $locator->get('Magento\Config\ScopeInterface')->setCurrentScope('global');
        $locator->get('Magento\App\Resource')->setCache($locator->get('Magento\App\CacheInterface'));

        $relations = $definitionFactory->createRelations();

        $interceptionConfig = $locator->create('Magento\Interception\Config\Config', array(
            'relations' => $relations,
            'omConfig' => $diConfig,
            'classDefinitions' => $definitions instanceof \Magento\ObjectManager\Definition\Compiled
                ? $definitions
                : null,
        ));

        $pluginList = $locator->create('Magento\Interception\PluginList\PluginList', array(
            'relations' => $relations,
            'definitions' => $definitionFactory->createPluginDefinition(),
            'omConfig' => $diConfig,
            'classDefinitions' => $definitions instanceof \Magento\ObjectManager\Definition\Compiled
                ? $definitions
                : null,
        ));
        $factory = $locator->create('Magento\Interception\FactoryDecorator', array(
            'factory' => $factory,
            'config' => $interceptionConfig,
            'pluginList' => $pluginList
        ));
        $locator->setFactory($factory);

        $directoryListConfig = $locator->get('Magento\Filesystem\DirectoryList\Configuration');
        $directoryListConfig->configure($directories);

        return $locator;
    }

    /**
     * Load primary config data
     *
     * @param string $configDirectoryPath
     * @param string $appMode
     * @return array
     * @throws \Magento\BootstrapException
     */
    protected function _loadPrimaryConfig($configDirectoryPath, $appMode)
    {
        $configData = null;
        $primaryLoader = new \Magento\App\ObjectManager\ConfigLoader\Primary($configDirectoryPath, $appMode);
        try {
            $configData = $primaryLoader->load();
        } catch (\Exception $e) {
            throw new \Magento\BootstrapException($e->getMessage());
        }
        return $configData;
    }
}