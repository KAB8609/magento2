<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Core
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Core configuration class
 *
 * Used to retrieve core configuration values
 *
 * @category   Mage
 * @package    Mage_Core
 * @link        http://var-dev.varien.com/wiki/doku.php?id=magento:api:mage:core:config
 * @author      Moshe Gurvich <moshe@varien.com>
 */

class Mage_Core_Model_Config extends Mage_Core_Model_Config_Base
{
    protected $_classNameCache = array();

    protected $_blockClassNameCache = array();

    protected $_secureUrlCache = array();

    protected $_customEtcDir = null;

    protected $_isInstalled	= true;

    /**
     * Constructor
     *
     */
    function __construct()
    {
        $this->setCacheId('config_global');
        parent::__construct();
    }


    /**
     * Return installed flag
     *
     * @return boolean
     */
    public function getIsInstalled()
    {
    	return $this->_isInstalled;
    }

    /**
     * Initialization of core config
     *
     */
    public function init($etcDir=null)
    {
        #return $this->initLive();
        $this->setCacheChecksum(null);
        $saveCache = true;

        Varien_Profiler::start('config/load-cache');
        if ($this->loadCache()) {
            if (!Mage::useCache('config')) {
                $this->getCache()->remove($this->getCacheId());
                $saveCache = false;
            } else {
                Varien_Profiler::stop('config/load-cache');
                return $this;
            }
        }
        Varien_Profiler::stop('config/load-cache');

        $this->_customEtcDir = $etcDir;

        $mergeConfig = new Mage_Core_Model_Config_Base();

        // load base config
        Varien_Profiler::start('config/load-base');
        $configFile = Mage::getBaseDir('etc').DS.'config.xml';
        #$this->setCacheChecksum(filemtime($configFile));
        $this->loadFile($configFile);
        Varien_Profiler::stop('config/load-base');

        Varien_Profiler::start('config/load-local');
        $configFile = Mage::getBaseDir('etc').DS.'local.xml';
        if (is_readable($configFile)) {
            #$this->updateCacheChecksum(filemtime($configFile));
            $mergeConfig->loadFile($configFile);
            $this->extend($mergeConfig);
        } else {
        	$this->_isInstalled = false;
        }
        Varien_Profiler::stop('config/load-local');

        if (!$this->getNode('global/install/date') || !$this->_isInstalled) {
            Varien_Profiler::start('config/load-distro');
            $mergeConfig->loadString($this->loadDistroConfig());

            $this->extend($mergeConfig, true);
            Varien_Profiler::stop('config/load-distro');
            $saveCache = false;
        }
/*
        Varien_Profiler::start('load-modules-checksum');
        $modules = $this->getNode('modules')->children();
        foreach ($modules as $modName=>$module) {
            if ($module->is('active')) {
                $configFile = $this->getModuleDir('etc', $modName).DS.'config.xml';
                if (is_readable($configFile)) {
                    $this->updateCacheChecksum(filemtime($configFile));
                }
            }
        }
        Varien_Profiler::stop('load-modules-checksum');

        if($this->_isInstalled) {
	        Varien_Profiler::start('load-db-checksum');
	        $dbConf = Mage::getResourceModel('core/config');
	        $this->updateCacheChecksum($dbConf->getChecksum('config_data,website,store,resource'));
	        Varien_Profiler::stop('load-db-checksum');
        }
*/


        Varien_Profiler::start('config/load-modules');
        $modules = $this->getNode('modules')->children();
        foreach ($modules as $modName=>$module) {
            if ($module->is('active')) {
                $configFile = $this->getModuleDir('etc', $modName).DS.'config.xml';
                if ($mergeConfig->loadFile($configFile)) {
                    $this->extend($mergeConfig, true);
                }
            }
        }
        Varien_Profiler::stop('config/load-modules');

        Varien_Profiler::start('config/apply-extends');
        $this->applyExtends();
        Varien_Profiler::stop('config/apply-extends');

        if($this->_isInstalled) {
	        Varien_Profiler::start('dbUpdates');
	        Mage_Core_Model_Resource_Setup::applyAllUpdates();
	        Varien_Profiler::stop('dbUpdates');

	        Varien_Profiler::start('config/load-db');
	        $dbConf = Mage::getResourceModel('core/config');
	        $dbConf->loadToXml($this);
	        Varien_Profiler::stop('config/load-db');
        }

        if ($saveCache) {
            Varien_Profiler::start('config/save-cache');
            $this->saveCache();
            Varien_Profiler::stop('config/save-cache');
        }

        return $this;
    }

    public function initLive()
    {
        $this->setCacheChecksum(null);

        Varien_Profiler::start('load-cache');
        if ($this->loadCache()) {
            Varien_Profiler::stop('load-cache');
            return true;
        } else {
            Varien_Profiler::stop('load-cache');
        }

        return $this;
    }

    /**
     * Retrieve cache object
     *
     * @return Zend_Cache_Frontend_File
     */
    public function getCache()
    {
        if (!$this->_cache) {
            $this->_cache = Zend_Cache::factory('Core', 'File', array(), array(
                'cache_dir'=>Mage::getBaseDir('cache_config')
            ));
        }
        return $this->_cache;
    }

    public function getTempVarDir()
    {
        $dir = dirname(Mage::getRoot()).DS.'var';
        if (!is_writable($dir)) {
            $dir = (!empty($_ENV['TMP']) ? $_ENV['TMP'] : DS.'tmp').DS.'magento'.DS.'var';
        }
        return $dir;
    }

    public function loadDistroConfig()
    {
        $data = $this->getDistroServerVars();
        $template = file_get_contents($this->getBaseDir('etc').DS.'distro.xml');
        foreach ($data as $index=>$value) {
            $template = str_replace('{{'.$index.'}}', '<![CDATA['.$value.']]>', $template);
        }
        return $template;
    }

    public function getLocalDist($data)
    {
        $template = file_get_contents($this->getBaseDir('etc').DS.'local.xml.template');
        foreach ($data as $index=>$value) {
            $template = str_replace('{{'.$index.'}}', '<![CDATA['.$value.']]>', $template);
        }

        return $template;
    }

    public function getDistroServerVars()
    {
        $basePath = dirname($_SERVER['SCRIPT_NAME']);
        if ("\\"==$basePath || "/"==$basePath) {
            $basePath = '/';
        } else {
            $basePath .= '/';
        }
        $host = explode(':', $_SERVER['HTTP_HOST']);
        $serverName = $host[0];
        $serverPort = isset($host[1]) ? $host[1] : (isset($_SERVER['HTTPS']) ? '443' : '80');

        $arr = array(
            'root_dir'  => dirname(Mage::getRoot()),
            'app_dir'   => dirname(Mage::getRoot()).DS.'app',
            'var_dir'   => $this->getTempVarDir(),
            'protocol'  => isset($_SERVER['HTTPS']) ? 'https' : 'http',
            'host'      => $serverName,
            'port'      => $serverPort,
            'base_path' => $basePath,
        );
        return $arr;
    }

    /**
     * Get module config node
     *
     * @param string $moduleName
     * @return Varien_Simplexml_Object
     */
    function getModuleConfig($moduleName='')
    {
        $modules = $this->getNode('modules');
        if (''===$moduleName) {
            return $modules;
        } else {
            return $modules->$moduleName;
        }
    }

    /**
     * Get module setup class instance.
     *
     * Defaults to Mage_Core_Setup
     *
     * @param string|Varien_Simplexml_Object $module
     * @return object
     */
    function getModuleSetup($module='')
    {
        $className = 'Mage_Core_Setup';
        if (''!==$module) {
            if (is_string($module)) {
                $module = $this->getModuleConfig($module);
            }
            if (isset($module->setup)) {
                $moduleClassName = $module->setup->getClassName();
                if (!empty($moduleClassName)) {
                    $className = $moduleClassName;
                }
            }
        }
        return new $className($module);
    }

    /**
     * Get base filesystem directory. depends on $type
     *
     * If $moduleName is specified retrieves specific value for the module.
     *
     * @todo get global dir config
     * @param string $type
     * @return string
     */
    public function getBaseDir($type)
    {
    	if ($type==='etc' && !is_null($this->_customEtcDir)) {
    		return $this->_customEtcDir;
    	}

        $dir = (string)$this->getNode('stores/default/system/filesystem/'.$type);
        if (!$dir) {
            $dir = $this->getDefaultBaseDir($type);
        }
        if (!$dir) {
            throw Mage::exception('Mage_Core', __('Invalid base dir type specified: %s', $type));
        }
        switch ($type) {
            case 'var':
            case 'session':
            case 'cache':
            case 'cache_config':
            case 'cache_layout':
            case 'cache_block':
            case 'cache_locale':
            case 'cache_translate':
            case 'cache_db':
                if (!file_exists($dir)) {
                    mkdir($dir, 0777, true);
                }
                break;
        }

        $dir = str_replace('/', DS, $dir);

        return $dir;
    }

    public function getDefaultBaseDir($type)
    {
        $dir = Mage::getRoot();
        switch ($type) {
        	case 'app':
        		$dir = Mage::getRoot();
        		break;

            case 'etc':
                $dir = Mage::getRoot().DS.'etc';
                break;

            case 'code':
                $dir = Mage::getRoot().DS.'code';
                break;

        	case 'design':
        		$dir = Mage::getRoot().DS.'design';
        		break;

        	case 'locale':
        	    $dir = Mage::getRoot().DS.'locale';
        	    break;

            case 'var':
                $dir = $this->getTempVarDir();
                break;

            case 'session':
                $dir = $this->getBaseDir('var').DS.'session';
                break;
            
            case 'cache':
                $dir = $this->getBaseDir('var').DS.'cache';
                break;

            case 'cache_config':
                $dir = $this->getBaseDir('var').DS.'cache'.DS.'config';
                break;

            case 'cache_layout':
                $dir = $this->getBaseDir('var').DS.'cache'.DS.'layout';
                break;

            case 'cache_block':
                $dir = $this->getBaseDir('var').DS.'cache'.DS.'block';
                break;

            case 'cache_locale':
                $dir = $this->getBaseDir('var').DS.'cache'.DS.'locale';
                break;

            case 'cache_translate':
                $dir = $this->getBaseDir('var').DS.'cache'.DS.'translate';
                break;

            case 'cache_db':
                $dir = $this->getBaseDir('var').DS.'cache'.DS.'db';
                break;
        }
        return $dir;
    }

    public function getModuleDir($type, $moduleName)
    {
        $codePool = (string)$this->getModuleConfig($moduleName)->codePool;
        $dir = $this->getBaseDir('code').DS.$codePool.DS.uc_words($moduleName, DS);

        switch ($type) {
            case 'etc':
                $dir .= DS.'etc';
                break;

            case 'controllers':
                $dir .= DS.'controllers';
                break;

            case 'sql':
                $dir .= DS.'sql';
                break;

            case 'locale':
                $dir .= DS.'locale';
                break;
        }

        $dir = str_replace('/', DS, $dir);

        return $dir;
    }

    public function getRouterInstance($routerName='', $singleton=true)
    {
        $routers = $this->getNode('front/routers');
        if (!empty($routerName)) {
            $routerConfig = $routers->$routerName;
        } else {
            foreach ($routers as $routerConfig) {
                if ($routerConfig->is('default')) {
                    break;
                }
            }
        }
        $className = $routerConfig->getClassName();
        $constructArgs = $routerConfig->args;
        if (!$className) {
            $className = 'Mage_Core_Controller_Front_Router';
        }
        if ($singleton) {
            $regKey = '_singleton_router/'.$routerName;
            if (!Mage::registry($regKey)) {
                Mage::register($regKey, new $className($constructArgs));
            }
            return Mage::registry($regKey);
        } else {
            return new $className($constructArgs);
        }
    }

    /**
     * Load event observers for an area (front, admin)
     *
     * @param string $area
     * @return boolean
     */
    public function loadEventObservers($area)
    {
        if ($events = $this->getNode("$area/events")) {
            $events = $events->children();
        }
        else {
            return false;
        }

        foreach ($events as $event) {
            $eventName = $event->getName();
            $observers = $event->observers->children();
            foreach ($observers as $observer) {
                switch ((string)$observer->type) {
                    case 'singleton':
                        $callback = array(
                            Mage::getSingleton((string)$observer->class),
                            (string)$observer->method
                        );
                        break;
                    case 'object':
                    case 'model':
                        $callback = array(
                            Mage::getModel((string)$observer->class),
                            (string)$observer->method
                        );
                        break;
                    default:
                        $callback = array($observer->getClassName(), (string)$observer->method);
                        break;
                }
                #$args = array_values((array)$observer->args);
                $args = (array)$observer->args;
                $observerClass = $observer->observer_class ? (string)$observer->observer_class : '';
                Mage::addObserver($eventName, $callback, $args, $observer->getName(), $observerClass);
            }
        }
        return true;
    }

    /**
     * Get standard path variables.
     *
     * To be used in blocks, templates, etc.
     *
     * @param array|string $args Module name if string
     * @return array
     */
    public function getPathVars($args=null)
    {
        $path = array();

        $path['baseUrl'] = Mage::getBaseUrl();
        $path['baseSecureUrl'] = Mage::getBaseUrl(array('_secure'=>true));

        return $path;
    }

    public function getGrouppedClassName($groupRootNode, $class)
    {
        if (isset($this->_classNameCache[$groupRootNode][$class])) {
            return $this->_classNameCache[$groupRootNode][$class];
        }

        $config = $this->getNode($groupRootNode);
        if (empty($config)) {
            return false;
        }

        if (isset($config->rewrite->$class)) {
            $className = (string)$config->rewrite->$class;
        } else {
#echo $groupRootNode.', '.$class.'<hr>';
            $className = $config->getClassName();
#if (stripos($class, 'manufacturer')!==false) echo "TEST:".$className;

            if (''!==$class) {
                $className .= '_'.uc_words($class);
            }
        }

        $this->_classNameCache[$groupRootNode][$class] = $className;

        return $className;
    }

    public function getBlockClassName($blockType)
    {
        $typeArr = explode('/', $blockType);
        if (!empty($typeArr[1])) {
            return $this->getGrouppedClassName('global/blocks/'.$typeArr[0], $typeArr[1]);
        } else {
            return $blockType;

            if (isset($this->_blockClassNameCache[$blockType])) {
                return $this->_blockClassNameCache[$blockType];
            }
            $className = $this->getNode('global/block/types/'.$typeArr[0])->getClassName();
            $this->_blockClassNameCache[$blockType] = $className;
            return $className;
        }
    }
    
    /**
     * Retrieve helper class name
     *
     * @param   string $name
     * @return  string
     */
    public function getHelperClassName($name)
    {
        $name = str_replace('/', '_Helper_', $name);
        return 'Mage_' . uc_words($name);
    }

    public function getModelClassName($modelClass)
    {
        $classArr = explode('/', $modelClass);
        if (!isset($classArr[1])) {
            return $modelClass;
        }
        return $this->getGrouppedClassName('global/models/'.$classArr[0], $classArr[1]);
    }

    /**
     * Get model class instance.
     *
     * Example:
     * $config->getModelInstance('catalog/product')
     *
     * Will instantiate Mage_Catalog_Model_Mysql4_Product
     *
     * @param string $modelClass
     * @param array|object $constructArguments
     * @return Mage_Core_Model_Abstract
     */
    public function getModelInstance($modelClass='', $constructArguments=array())
    {
        $className = $this->getModelClassName($modelClass);
        //echo "Class name: {$className}<br>\n";
        if (class_exists($className)) {
            $model = new $className($constructArguments);
        } else {
            #throw Mage::exception('Mage_Core', __('Model class does not exist: %s', $modelClass));
            return false;
        }
        return $model;
    }

    public function getNodeClassInstance($path)
    {
        $config = Mage::getConfig()->getNode($path);
        if (!$config) {
            return false;
        } else {
            $className = $config->getClassName();
            return new $className();
        }
    }

    public function getResourceModelInstance($modelClass='', $constructArguments=array())
    {

        $classArr = explode('/', $modelClass);
        $resourceModel = (string)$this->getNode('global/models/'.$classArr[0].'/resourceModel');
        if (!$resourceModel) {
            return false;
        }
        return $this->getModelInstance($resourceModel.'/'.$classArr[1], $constructArguments);
    }

    /**
     * Get resource configuration for resource name
     *
     * @param string $name
     * @return Varien_Simplexml_Object
     */
    public function getResourceConfig($name)
    {
        return $this->getNode("global/resources/$name");
    }

    public function getResourceConnectionConfig($name)
    {
        $config = $this->getResourceConfig($name);
        if ($config) {
            $conn = $config->connection;
            if (!empty($conn->use)) {
                return $this->getResourceConnectionConfig((string)$conn->use);
            } else {
                return $conn;
            }
        }
        return false;
    }

    /**
     * Retrieve resource type configuration for resource name
     *
     * @param string $type
     * @return Varien_Simplexml_Object
     */
    public function getResourceTypeConfig($type)
    {
        return $this->getNode("global/resource/connection/types/$type");
    }

    /**
     * Retrieve store Ids for $path with checking
     *
     * if empty $allowValues then retrieve all stores values
     *
     * return array($storeId=>$pathValue)
     *
     * @param   string $path
     * @param   array  $allowValues
     * @return  array
     */
    public function getStoresByPath($path, $allowValues = array())
    {
        $storeIds = array();
        $stores = $this->getNode('stores');
        foreach ($stores->children() as $core => $store) {
        	$storeId   = (int) $store->descend('system/store/id');
        	if ($storeId === false) {
        	    continue;
        	}

        	$pathValue = (string) $store->descend($path);

        	if (empty($allowValues)) {
        	    $storeIds[$storeId] = $pathValue;
        	}
        	elseif(in_array($pathValue, $allowValues)) {
        	    $storeIds[$storeId] = $pathValue;
        	}
        }
        return $storeIds;
    }

    public function isUrlSecure($url)
    {
        Varien_Profiler::start(__METHOD__);
        if (!isset($this->_secureUrlCache[$url])) {
            $this->_secureUrlCache[$url] = false;
            $secureUrls = $this->getNode('frontend/secure_url');
            foreach ($secureUrls->children() as $match) {
                if (strpos($url, (string)$match)===0) {
                    $this->_secureUrlCache[$url] = true;
                    break;
                }
            }
        }
        Varien_Profiler::stop(__METHOD__);

        return $this->_secureUrlCache[$url];
    }
}