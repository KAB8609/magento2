<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Base test case class
 *
 * @category    Magento
 * @package     Magento_Test
 * @author      Magento Api Team <api-team@magento.com>
 */
// TODO: Consider removal of this class
class Magento_TestCase extends PHPUnit_Framework_TestCase
{
    /**#@+
     * Auto tear down options in setFixture
     */
    const AUTO_TEAR_DOWN_DISABLED = 0;
    const AUTO_TEAR_DOWN = 1;
    const AUTO_TEAR_DOWN_AFTER_CLASS = 2;
    /**#@-*/

    /**
     * Application cache model
     *
     * This model worked with cache of application
     *
     * @var Mage_Core_Model_Cache
     */
    protected $_appCache;

    /**
     * Delete models list
     *
     * @var array
     */
    protected $_modelsToDelete = array();

    /**
     * Namespace for fixtures is different for each test case class
     *
     * @var string
     */
    protected static $_fixturesNamespace;

    /**
     * Fixtures registry
     *
     * @var array
     */
    protected static $_fixtures = array();

    /**
     * Fixtures to be deleted in tear down registry
     *
     * @var array
     */
    protected static $_tearDownFixtures = array();

    /**
     * Fixtures to be deleted in tear down after class registry
     *
     * @var array
     */
    protected static $_tearDownAfterClassFixtures = array();

    /**
     * Default admin user model
     *
     * @var Mage_User_Model_User
     */
    static protected $_defaultAdmin;

    /**
     * Default admin user model
     *
     * @var Mage_Customer_Model_Customer
     */
    static protected $_defaultCustomer;

    /**
     * Original Magento config values.
     *
     * @var array
     */
    protected $_origConfigValues = array();

    /**
     * Set fixtures namespace value
     */
    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();
        self::_setFixtureNamespace();
    }

    /**
     * Run garbage collector for cleaning memory
     *
     * @return void
     */
    public static function tearDownAfterClass()
    {
        //clear garbage in memory
        if (version_compare(PHP_VERSION, '5.3', '>=')) {
            gc_collect_cycles();
        }

        $fixtureNamespace = self::_getFixtureNamespace();
        if (isset(self::$_tearDownAfterClassFixtures[$fixtureNamespace])
            && count(self::$_tearDownAfterClassFixtures[$fixtureNamespace])) {
            self::_deleteFixtures(self::$_tearDownAfterClassFixtures[$fixtureNamespace]);
        }

        //ever disable secure area on class down
        self::enableSecureArea(false);
        self::_unsetFixtureNamespace();
        parent::tearDownAfterClass();
    }

    /**
     * Set fixtures namespace
     * @throws Magento_Test_Exception
     */
    protected static function _setFixtureNamespace()
    {
        if (!is_null(self::$_fixturesNamespace)) {
            throw new Magento_Test_Exception('Fixture namespace is already set.');
        }
        self::$_fixturesNamespace = uniqid();
    }

    /**
     * Unset fixtures namespace
     */
    protected static function _unsetFixtureNamespace()
    {
        $fixturesNamespace = self::_getFixtureNamespace();
        unset(self::$_fixtures[$fixturesNamespace]);
        self::$_fixturesNamespace = null;
    }

    /**
     * Get fixtures namespace
     * @throws Magento_Test_Exception
     * @return string
     */
    protected static function _getFixtureNamespace()
    {
        $fixtureNamespace = self::$_fixturesNamespace;
        if (is_null($fixtureNamespace)) {
            throw new Magento_Test_Exception('Fixture namespace must be set.');
        }
        return $fixtureNamespace;
    }

    /**
     * Replace object which will be returned on Mage::getSingleton() call
     *
     * @param string $name
     * @param object $mock
     * @return Magento_TestCase
     */
    protected function _replaceSingleton($name, $mock)
    {
        $registryKey = '_singleton/'.$name;
        Mage::unregister($registryKey);
        Mage::register($registryKey, $mock);

        return $this;
    }

    /**
     * Restore original object which will be returned on Mage::getSingleton() call
     *
     * @param string $name
     * @return Magento_TestCase
     */
    protected function _restoreSingleton($name)
    {
        $registryKey = '_singleton/'.$name;
        Mage::unregister($registryKey);

        return $this;
    }

    /**
     * Replace object which will be returned on Mage::getSingleton() call with mock
     *
     * @param string $name
     * @param array $methods
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    protected function _replaceSingletonWithMock($name, $methods=array())
    {
        $class = get_class(Mage::getSingleton($name));
        $mock = $this->getMock($class, $methods);

        $this->_replaceSingleton($name, $mock);

        return $mock;
    }

    /**
     * Replace object which will be returned on Mage::helper() call
     *
     * @param string $name
     * @param object $mock
     * @return Magento_TestCase
     */
    protected function _replaceHelper($name, $mock)
    {
        if (strpos($name, '/') === false) {
            $newRegistryKey = '_helper/' . $name;
            Mage::unregister($newRegistryKey);
            Mage::register($newRegistryKey, $mock);

            $name .= '/data';
        }

        $registryKey = '_helper/' . $name;
        Mage::unregister($registryKey);
        Mage::register($registryKey, $mock);

        return $this;
    }

    /**
     * Restore original object which will be returned on Mage::helper() call
     *
     * @param string $name
     * @return Magento_TestCase
     */
    protected function _restoreHelper($name)
    {
        if (strpos($name, '/') === false) {
            Mage::unregister('_helper/' . $name);
            $name .= '/data';
        }

        $registryKey = '_helper/' . $name;
        Mage::unregister($registryKey);

        return $this;
    }

    /**
     * Replace object which will be returned on Mage::helper() call with mock
     *
     * @param string $name
     * @param array $methods
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    protected function _replaceHelperWithMock($name, $methods=array())
    {
        $class = get_class(Mage::helper($name));
        $mock = $this->getMock($class, $methods);

        $this->_replaceHelper($name, $mock);

        return $mock;
    }

    /**
     * Enable secure/admin area
     *
     * @param bool $flag
     * @return void
     */
    static public function enableSecureArea($flag = true)
    {
        Mage::unregister('isSecureArea');
        if ($flag) {
            Mage::register('isSecureArea', $flag);
        }
    }

    /**
     * Call safe delete for model
     *
     * @param Mage_Core_Model_Abstract $model
     * @param bool $secure
     * @return Magento_TestCase
     */
    static public function callModelDelete($model, $secure = false)
    {
        if ($model instanceof Mage_Core_Model_Abstract && $model->getId()) {
            if ($secure) {
                self::enableSecureArea();
            }
            $model->delete();
            if ($secure) {
                self::enableSecureArea(false);
            }
        }
    }

    /**
     * Call safe delete for model
     *
     * @param Mage_Core_Model_Abstract $model
     * @param bool $secure
     * @return Magento_TestCase
     */
    public function addModelToDelete($model, $secure = false)
    {
        $this->_modelsToDelete[] = array(
            'model' => $model,
            'secure' => $secure
        );
        return $this;
    }

    /**
     * Call delete models from list
     *
     * @return Magento_TestCase
     */
    protected function _callModelsDelete()
    {
        if ($this->_modelsToDelete) {
            foreach ($this->_modelsToDelete as $key => $modelData) {
                /** @var $model Mage_Core_Model_Abstract */
                $model = $modelData['model'];
                $this->callModelDelete($model, $modelData['secure']);
                unset($this->_modelsToDelete[$key]);
            }
        }
        return $this;
    }

    /**
     * Call safe delete for models which added to delete list
     * Restore config values changed during the test
     *
     * @return void
     */
    protected function tearDown()
    {
        $fixtureNamespace = self::_getFixtureNamespace();
        if (isset(self::$_tearDownFixtures[$fixtureNamespace]) && count(self::$_tearDownFixtures[$fixtureNamespace])) {
            self::_deleteFixtures(self::$_tearDownFixtures[$fixtureNamespace]);
        }
        $this->_callModelsDelete();
        $this->_restoreAppConfig();
        parent::tearDown();
    }

    /**
     * Get application cache model
     *
     * @return Mage_Core_Model_Cache
     */
    protected function _getAppCache()
    {
        if (null === $this->_appCache) {
            //set application path
            $options = Mage::getConfig()->getOptions();
            $currentCacheDir = $options->getCacheDir();
            $currentEtcDir = $options->getEtcDir();

            $options->setCacheDir(Magento_Test_Bootstrap::getInstance()->getMagentoDir() . DS . 'var' . DS . 'cache');
            $options->setEtcDir(Magento_Test_Bootstrap::getInstance()->getMagentoDir() . DS . 'app' . DS . 'etc');

            $this->_appCache = new Mage_Core_Model_Cache(array(
                'request_processors' => array(
                    'ee' => 'Enterprise_PageCache_Model_Processor'
                )
            ));

            //revert paths options
            $options->setCacheDir($currentCacheDir);
            $options->setEtcDir($currentEtcDir);
        }
        return $this->_appCache;
    }

    /**
     * Clean config cache of application
     *
     * @return bool
     */
    protected function _cleanAppConfigCache()
    {
        return $this->_getAppCache()->clean(Mage_Core_Model_Config::CACHE_TAG);
    }

    /**
     * Update application config data
     *
     * @param string $path              Config path with the form "section/group/node"
     * @param string|int|null $value    Value of config item
     * @param bool $cleanAppCache       If TRUE application cache will be refreshed
     * @param bool $updateLocalConfig   If TRUE local config object will be updated too
     * @param bool $restore             If TRUE config value will be restored after test run
     * @return Magento_TestCase
     * @throws Magento_Test_Exception
     */
    protected function _updateAppConfig($path, $value, $cleanAppCache = true, $updateLocalConfig = false,
        $restore = false
    ) {
        list($section, $group, $node) = explode('/', $path);

        if (!$section || !$group || !$node) {
            throw new Magento_Test_Exception(sprintf(
                'Config path must have view as "section/group/node" but now it "%s"',
                $path));
        }

        /** @var $config Mage_Backend_Model_Config */
        $config = Mage::getModel('Mage_Backend_Model_Config');
        $data[$group]['fields'][$node]['value'] = $value;
        $config->setSection($section)
                ->setGroups($data)
                ->save();

        if ($restore && !isset($this->_origConfigValues[$path])) {
            $this->_origConfigValues[$path] = (string) Mage::getConfig()->getNode($path, 'default');
        }

        //refresh local cache
        if ($cleanAppCache) {
            if ($updateLocalConfig) {
                Mage::getConfig()->reinit();
                Mage::app()->reinitStores();
            }

            if (!$this->_cleanAppConfigCache()) {
                throw new Magento_Test_Exception('Application configuration cache cannot be cleaned.');
            }
        }

        return $this;
    }

    /**
     * Restore config values changed during tests
     */
    protected function _restoreAppConfig()
    {
        foreach ($this->_origConfigValues as $configPath => $origValue) {
            $this->_updateAppConfig($configPath, $origValue, true, true);
        }
    }

    /**
     * Get admin user model
     *
     * @return Mage_User_Model_User
     */
    static public function getDefaultAdminUser()
    {
        if (null === self::$_defaultAdmin) {
            /** @var $user Mage_User_Model_User */
            $user = Mage::getModel('Mage_User_Model_User');
            $user->login(TESTS_ADMIN_USERNAME, TESTS_ADMIN_PASSWORD);
            if (!$user->getId()) {
                throw new Magento_Test_Exception('Admin user not found. Check credentials from config file.');
            }
            self::$_defaultAdmin = $user;
        }
        return self::$_defaultAdmin;
    }

    /**
     * Get customer model
     *
     * @return Mage_Customer_Model_Customer
     */
    static public function getDefaultCustomer()
    {
        if (null === self::$_defaultCustomer) {
            /** @var $user Mage_Customer_Model_Customer */
            $user = Mage::getModel('Mage_Customer_Model_Customer');
            $user->setWebsiteId(Mage::app()->getStore()->getWebsiteId())
                ->authenticate(TESTS_CUSTOMER_EMAIL, TESTS_CUSTOMER_PASSWORD);
            if (!$user->getId()) {
                throw new Magento_Test_Exception('Customer user not found. Check credentials from config file.');
            }
            self::$_defaultCustomer = $user;
        }
        return self::$_defaultCustomer;
    }

    /**
     * Set fixture to registry
     *
     * @param string $key
     * @param mixed $fixture
     * @param int $tearDown
     * @return void
     */
    public static function setFixture($key, $fixture, $tearDown = self::AUTO_TEAR_DOWN)
    {
        $fixturesNamespace = self::_getFixtureNamespace();
        if (!isset(self::$_fixtures[$fixturesNamespace])) {
            self::$_fixtures[$fixturesNamespace] = array();
        }
        self::$_fixtures[$fixturesNamespace][$key] = $fixture;
        if ($tearDown == self::AUTO_TEAR_DOWN) {
            if (!isset(self::$_tearDownFixtures[$fixturesNamespace])) {
                self::$_tearDownFixtures[$fixturesNamespace] = array();
            }
            self::$_tearDownFixtures[$fixturesNamespace][] = $key;
        } else if ($tearDown == self::AUTO_TEAR_DOWN_AFTER_CLASS) {
            if (!isset(self::$_tearDownAfterClassFixtures[$fixturesNamespace])) {
                self::$_tearDownAfterClassFixtures[$fixturesNamespace] = array();
            }
            self::$_tearDownAfterClassFixtures[$fixturesNamespace][] = $key;
        }

    }

    /**
     * Get fixture by key
     *
     * @param string $key
     * @return mixed
     */
    public static function getFixture($key)
    {
        $fixturesNamespace = self::_getFixtureNamespace();
        if (array_key_exists($key, self::$_fixtures[$fixturesNamespace])) {
            return self::$_fixtures[$fixturesNamespace][$key];
        }
        return null;
    }

    /**
     * Delete array of fixtures
     *
     * @param array $fixtures
     */
    protected static function _deleteFixtures($fixtures)
    {
        foreach ($fixtures as $fixture) {
            self::deleteFixture($fixture, true);
        }
    }

    /**
     * Delete fixture by key
     *
     * @param string $key
     * @param bool $secure
     * @return void
     */
    public static function deleteFixture($key, $secure = false)
    {
        $fixturesNamespace = self::_getFixtureNamespace();
        if (array_key_exists($key, self::$_fixtures[$fixturesNamespace])) {
            self::callModelDelete(self::$_fixtures[$fixturesNamespace][$key], $secure);
            unset(self::$_fixtures[$fixturesNamespace][$key]);
        }
    }
}
