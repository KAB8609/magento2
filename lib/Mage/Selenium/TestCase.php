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
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    tests
 * @package     selenium
 * @subpackage  Mage_Selenium
 * @author      Magento Core Team <core@magentocommerce.com>
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * An extended test case implementation that add usefull helper methods
 *
 * @package     selenium
 * @subpackage  Mage_Selenium
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Mage_Selenium_TestCase extends PHPUnit_Extensions_SeleniumTestCase
{

    /**
     * Testcase error
     *
     * @var boolean
     */
    protected $_error = false;

    /**
     * Data helper instance
     *
     * @var Mage_Selenium_Helper_Data
     */
    protected $_dataHelper = null;

    /**
     * Data generator helper instance
     *
     * @var Mage_Selenium_Helper_DataGenerator
     */
    protected $_dataGenerator = null;

    /**
     * Application helper instance
     *
     * @var Mage_Selenium_Helper_Application
     */
    protected $_applicationHelper = null;

    /**
     * Uimap helper instance
     *
     * @var Mage_Selenium_Helper_Uimap
     */
    protected $_uimapHelper = null;

    /**
     * Page helper instance
     *
     * @var Mage_Selenium_Helper_Page
     */
    protected $_pageHelper = null;

    /**
     * Error and success messages on page
     *
     * @var array
     */
    public $messages = null;

    /**
     * Current application area
     *
     * @var string
     */
    protected static $_area = 'frontend';

    /**
     * Current page
     *
     * @var string
     */
    protected $_currentPage = '';

    /**
     * Configuration object instance
     *
     * @var Mage_Selenium_TestConfiguration
     */
    protected $_testConfig = null;

    /**
     * Parameters helper instance
     *
     * @var Mage_Selenium_Helper_Params
     */
    protected $_paramsHelper = null;

    /**
     * Timeout const
     *
     * @var int
     */
    protected $_browserTimeoutPeriod = 10000;

    /**
     * @var PHPUnit_Framework_TestResult
     */
    protected $result;

    /**
     * @var    array
     */
    protected $dependencies = array();

    /**
     * Whether or not this test is running in a separate PHP process.
     *
     * @var    boolean
     */
    protected $inIsolation = false;

    /**
     * The name of the test case.
     *
     * @var    string
     */
    protected $name = null;

    /**
     * The name of the expected Exception.
     *
     * @var    mixed
     */
    protected $expectedException = null;

    /**
     * The message of the expected Exception.
     *
     * @var    string
     */
    protected $expectedExceptionMessage = '';

    /**
     * @var    array
     */
    protected $data = array();

    /**
     * @var    array
     */
    protected $dependencyInput = array();

    /**
     * @var array
     */
    protected $_testHelpers = array();

    /*
     * @var string
     */
    protected $_firstPageAfterAdminLogin = 'dashboard';

//    protected $captureScreenshotOnFailure = TRUE;

//    protected $screenshotPath = SELENIUM_TESTS_SCREENSHOTDIR;

//    protected $screenshotUrl = SELENIUM_TESTS_SCREENSHOTDIR;

    /**
     * Excluded message about Bundle product
     *
     * @var string
     */
    const excludedBundleMessage =
        'Bundle with dynamic pricing cannot include custom defined options. Options will not be saved.';

    /**
     * Excluded message about Configurable product
     *
     * @var string
     */
    const excludedConfigurableMessage = 'Links with associated products will retain only after saving current product.';

    /**
     * Success message Xpath
     *
     * @var string
     */
    const xpathSuccessMessage = "//*/descendant::*[normalize-space(@class)='success-msg'][string-length(.)>1]";

    /**
     * Error message Xpath
     *
     * @var string
     */
    const xpathErrorMessage = "//*/descendant::*[normalize-space(@class)='error-msg'][string-length(.)>1]";

    /**
     * Error message Xpath
     *
     * @var string
     */
    const xpathValidationMessage = "//*/descendant::*[normalize-space(@class)='validation-advice' and not(contains(@style,'display: none;'))]";

    /**
     * Field Name xpath with ValidationMessage
     *
     *  @var string
     */
    const xpathFieldNameWithValidationMessage ="/ancestor::*[2]//label/descendant-or-self::*[string-length(text())>1]";

    /**
     * Loading holder XPath
     * @var string
     */
    const xpathLoadingHolder = "//div[@id='loading-mask' and not(contains(@style,'display: none'))]";

    /**
     * Log Out link
     * @var string
     */
    const xpathLogOutAdmin = "//div[@class='header-right']//a[@class='link-logout']";

    /**
     * Admin Logo Xpath
     * @var string
     */
    const xpathAdminLogo = "//img[@class='logo' and contains(@src,'logo.gif')]";

    /**
     * Qty elements in Table
     * @var string
     */
    const qtyElementsInTable = "//td[@class='pager']//span[contains(@id,'total-count')]";

    /**
     * @var string
     */
    const FIELD_TYPE_MULTISELECT = 'multiselect';

    /**
     * @var string
     */
    const FIELD_TYPE_DROPDOWN = 'dropdown';

    /**
     * @var string
     */
    const FIELD_TYPE_CHECKBOX = 'checkbox';

    /**
     * @var string
     */
    const FIELD_TYPE_RADIOBUTTON = 'radiobutton';

    /**
     * @var string
     */
    const FIELD_TYPE_INPUT = 'field';

    /**
     * Constructs a test case with the given name and browser to test execution
     *
     * @param  string $name Test case name (by default = null)
     * @param  array  $data Test case data array (PHPUnit ONLY) (by default = array())
     * @param  string $dataName Name of Data set (PHPUnit ONLY) (by default = '')
     * @param  array  $browser Array of browser configuration settings: 'name', 'browser', 'host', 'port', 'timeout',
     * 'httpTimeout' (by default = array())
     *
     * @throws InvalidArgumentException
     */
    public function __construct($name = null, array $data = array(), $dataName = '', array $browser = array())
    {
        $this->_testConfig = Mage_Selenium_TestConfiguration::initInstance();
        $this->_dataHelper = $this->_testConfig->getDataHelper();
        $this->_dataGenerator = $this->_testConfig->getDataGenerator();
        $this->_applicationHelper = $this->_testConfig->getApplicationHelper();
        $this->_pageHelper = $this->_testConfig->getPageHelper($this, $this->_applicationHelper);
        $this->_uimapHelper = $this->_testConfig->getUimapHelper();

        if ($name !== null) {
            $this->name = $name;
        }
        $this->data = $data;
        $this->dataName = $dataName;

        $this->_browserTimeoutPeriod = $this->_testConfig->getConfigValue('browsers/default/browserTimeoutPeriod');

        parent::__construct($name, $data, $dataName, $browser);
    }

    /**
     * Delegate method calls to the driver and overridden to allow load tests helpers
     *
     * @param string $command    Command's (method's) name to call
     * @param array  $arguments  Arguments for send to called command (method)
     *
     * @return mixed
     */
    public function __call($command, $arguments)
    {
        if (version_compare(phpversion(), '5.3.0', '<') === true) {
            $helper = false;
            $pos = strpos($command, 'Helper');
            if ($pos !== false) {
                $helper = substr($command, 0, $pos);
            }
        } else {
            $helper = strstr($command, 'Helper', true);
        }

        if ($helper !== false) {
            $helper = $this->_loadHelper($helper);
            if ($helper) {
                return $helper;
            }
        }
        return parent::__call($command, $arguments);
    }

    /**
     * Allow to access/load helpers from the tests level as a class in view "TestScope_HelperName"
     *
     * @param   string $testScope   Contains part of the helper class name which refers to folder with needed helper
     * @param   string $helperName  Sufix, which described helper's name(default = 'Helper')
     *
     * @return  Mage_Selenium_TestCase
     */
    protected function _loadHelper($testScope, $helperName = 'Helper')
    {
        if (empty($testScope) || empty($helperName)) {
            throw new UnexpectedValueException('Helper name can\'t be empty');
        }

        $helperClassName = $testScope . '_' . $helperName;

        if (!isset($this->_testHelpers[$helperClassName])) {
            if (class_exists($helperClassName)) {
                $this->_testHelpers[$helperClassName] = new $helperClassName;

                if ($this->_testHelpers[$helperClassName] instanceof Mage_Selenium_TestCase) {
                    $this->_testHelpers[$helperClassName]->appendParamsDecorator($this->_paramsHelper);
                }
            } else {
                return false;
            }
        }

        return $this->_testHelpers[$helperClassName];
    }

    /**
     * Implementetion of setUpBeforeClass() method in the object context, called as setUpBeforeTests()<br>
     * Used ONLY one time before execution of each class (tests in test case)
     *
     * @staticvar boolean $_isFirst Internal variable, which described usage count of this one method
     *
     * @return null
     */
    public function setUp()
    {
        static $_isFirst = true;

        if ($_isFirst) {
            $this->setUpBeforeTests();
            $_isFirst = false;
        }
    }

    /**
     * Function is called before all tests in test case and used for do some action(s) as a precondition(s) for all test
     *
     * @return null
     */
    public function setUpBeforeTests()
    {
    }

    /**
     * Append parameters decorator object
     *
     * @param Mage_Selenium_Helper_Params $paramsHelperObject Parameters decorator object
     *
     * @return null
     */
    public function appendParamsDecorator($paramsHelperObject)
    {
        $this->_paramsHelper = $paramsHelperObject;
    }

    /**
     * Set parameter to decorator object instance
     *
     * @param   string $name   Parameter name
     * @param   string $value  Parameter value (null to unset)
     * @return  Mage_Selenium_Helper_Params
     */
    public function addParameter($name, $value)
    {
        if (!$this->_paramsHelper) {
            $this->_paramsHelper = new Mage_Selenium_Helper_Params();
        }
        $this->_paramsHelper->setParameter($name, $value);

        return $this->_paramsHelper;
    }

    /**
     * Loads specific driver for specified browser
     *
     * @param   array $browser Defines what kind of driver, for a what browser will be loaded
     *
     * @since   Method available since Release 3.3.0
     *
     * @return  PHPUnit_Extensions_SeleniumTestCase_Driver
     */
    protected function getDriver(array $browser)
    {
        $driver = $this->_testConfig->driver;
        $driver->setTestCase($this);
        $driver->setTestId($this->testId);
        // @TODO we need separate driver connections if admin url doesn't start with frontend url
        $driver->setBrowserUrl($this->_applicationHelper->getBaseUrl());
        $driver->start();
        $this->drivers[] = $driver;
        return $driver;
    }

    /**
     * Sets the dependencies between a test cases
     *
     * @param  array $dependencies List of a dependencies of the each loaded test
     *
     * @since  Method available since Release 3.4.0
     */
    public function setDependencies(array $dependencies)
    {
        $this->dependencies = $dependencies;
    }

    /**
     * Checks if there was error during last operations
     *
     * @return boolean
     */
    public function hasError()
    {
        return $this->_error;
    }

    /**
     * Data helper methods
     */

    /**
     * Override data with index $key on-fly in the $overrideArray by new value (&$value)
     *
     * @param string $value Value for override
     * @param string $key Index of the target to override
     * @param array $overrideArray Target array, which contains some indexe(s) to override
     *
     * @return null
     */
    function overrideData(&$value, $key, $overrideArray)
    {
        foreach ($overrideArray as $overrideField => $fieldValue) {
            if ($overrideField === $key) {
                $value = $fieldValue;
            }
        }
    }

    /**
     * Randomize data with index $key on-fly in the $randomizeArray by new value (&$value)
     *
     * @param string $value Value for randomization (in this case - value will be as a suffix)
     * @param string $key Index of the target to randomize
     * @param array $randomizeArray Target array, which contains some indexe(s) to randomize
     */
    function randomizeData(&$value, $key, $randomizeArray)
    {
        foreach ($randomizeArray as $randomizeField) {
            if ($randomizeField === $key) {
                $value = $this->generate('string', 5, ':lower:') . '_' . $value;
            }
        }
    }

    /**
     * Get an array of keys from Multidimensional Array
     *
     * @param array $arrayData Array of data
     * @param array $arrayKeys Array of keys
     *
     * @return array
     */
    function arrayKeysRecursion(array $arrayData, &$arrayKeys)
    {
        foreach ($arrayData as $key => $value) {
            if (is_array($value)) {
                $arrayKeys = $this->arrayKeysRecursion($value, $arrayKeys);
            } else {
                $arrayKeys[] = $key;
            }
        }
        return $arrayKeys;
    }

    /**
     * Remove array elements with a value of '%noValue%'
     *
     * @param array $array  Array of data for clearning from '%noValue%' value(s)
     *
     * @return array
     */
    public function arrayEmptyClear($array)
    {
        if (!is_array($array))
            return false;

        foreach ($array as $k => $v) {
            if (is_array($v)) {
                $array[$k] = $this->arrayEmptyClear($v);
                if (count($array[$k]) == false)
                    unset($array[$k]);
            } else {
                if ($v === '%noValue%') {
                    unset($array[$k]);
                }
            }
        }

        return $array;
    }

    /**
     * Loads test data from DataSet, specified in the $dataSource
     *
     * @param string|array $dataSource Data source (e.g. filename in ../data without .yml extension)
     * @param array|null $override Value to override in original data from data source
     * @param string|array|null $randomize Value to randomize
     *
     * @return array
     */
    public function loadData($dataSource, $override=null, $randomize=null)
    {
        $data = $this->_getData($dataSource);

        if (!empty($randomize)) {
            $randomize = (!is_array($randomize)) ? array($randomize) : $randomize;
//            foreach ($randomize as $field) {
//                $data[$field] = $this->generate('string', 5, ':lower:') . '_' . $data[$field];
//            }
            array_walk_recursive($data, array($this, 'randomizeData'), $randomize);
        }

        if (!empty($override) && is_array($override)) {
//            foreach ($override as $field => $value) {
//                $data[$field] = $value;
//            }
            $arrayKeys = array();
            $needAddValues = array();

            $arrayKeys = $this->arrayKeysRecursion($data, $arrayKeys);

            foreach ($override as $key => $value) {
                if (!in_array($key, $arrayKeys)) {
                    $needAddValues[$key] = $value;
                    unset($override[$key]);
                }
            }

            array_walk_recursive($data, array($this, 'overrideData'), $override);

            foreach ($needAddValues as $field => $value) {
                $data[$field] = $value;
            }
        }

        return $data;
    }

    /**
     * Generates random value as a string|text|email $type, with specified $length.<br>
     * Can be used $modifier:
     * <li>if $type = string - alnum|alpha|digit|lower|upper|punct
     * <li>if $type = text - alnum|alpha|digit|lower|upper|punct
     * <li>if $type = email - valid|invalid
     *
     * @param string $type Available types are 'string', 'text', 'email' (by default = 'string')
     * @param integer $length Generated value length (by default = 100)
     * @param string|array|null $modifier Value modifier, e.g. PCRE class (by default = NULL)
     * @param string|null $prefix Prefix to prepend the generated value (by default = NULL)
     *
     * @return mixed
     */
    public function generate($type='string', $length=100, $modifier=null, $prefix=null)
    {
        $result = $this->_dataGenerator->generate($type, $length, $modifier, $prefix);
        return $result;
    }

    /**
     * Navigation methods
     */

    /**
     * Navigate to a specified frontend page<br>
     * Page identifier must be described in the UIMAp. Opens "Home page" by default.
     *
     * @param string $page Page identifier (by default = 'home')
     *
     * @return Mage_Selenium_TestCase
     */
    public function frontend($page='home')
    {
        $this->setArea('frontend');
        $this->navigate($page);
        return $this;
    }

    /**
     * Navigate to a specified admin page.<br>
     * Page identifier must be described in the UIMAp. Opens "Dashboard" page by default.
     *
     * @param string $page Page identifier (by default = 'dashboard')
     *
     * @return Mage_Selenium_TestCase
     */
    public function admin($page='dashboard')
    {
        $this->setArea('admin');
        $this->navigate($page);
        return $this;
    }

    /**
     * Navigates to a specified page in the current area.<br>
     * Page identifier must be described in the UIMAp.
     *
     * @param string $page Page identifier
     *
     * @return Mage_Selenium_TestCase
     */
    public function navigate($page)
    {
        try {
            $clickXpath = $this->getPageClickXpath($page);

            if ($clickXpath && $this->isElementPresent($clickXpath)) {
                $this->click($clickXpath);
                $this->waitForPageToLoad($this->_browserTimeoutPeriod);
            } else {
                $this->open($this->getPageUrl($page));
            }
            $this->assertTextNotPresent('Fatal error:', 'Fatal error on page ');
            $this->assertTextNotPresent('There has been an error processing your request',
                    'There has been an error processing your request');
            $this->_pageHelper->validateCurrentPage();
            $this->_currentPage = $page;
        } catch (PHPUnit_Framework_Exception $e) {
            $this->_error = true;
        }

        return $this;
    }

    /**
     * Navigates to the specified page in the current area and stops current testcase execution if navigation failed<br>
     * Page identifier must be described in the UIMAp.
     *
     * @param string $page Page identifier
     *
     * @return Mage_Selenium_TestCase
     */
    public function navigated($page)
    {
        $this->navigate($page);
        if ($this->_pageHelper->validationFailed()) {
            // @TODO stop further execution of the current test
            $this->_error = true;
        }
        return $this;
    }

    /**
     * Checks the current openned page.<br>
     * Returns TRUE if requested page == current page else returns FALSE and sets up error message:
     * "Opened the wrong page: $currentPage (should be:$page)".<br>
     * Page identifier must be described in the UIMAp.
     *
     * @param string $page Page identifier
     *
     * @return boolean
     */
    public function checkCurrentPage($page)
    {
        $currentPage = $this->_findCurrentPageFromUrl($this->getLocation());
        if ($currentPage != $page) {
            $this->messages['error'][] = "Opened the wrong page: '" . $currentPage . "' (should be: '" . $page . "')";
            return false;
        }
        return true;
    }

    /**
     * Returns URL of a specified page
     *
     * @param string $page Page identifier
     *
     * @return string
     */
    public function getPageUrl($page)
    {
        $pageData = $this->getUimapPage($this->_applicationHelper->getArea(), $page);
        $url = $this->_applicationHelper->getBaseUrl() . $pageData->getMca();

        return $url;
    }

    /**
     * Return click xpath of a specified page
     *
     * @param string $page Page identifier
     *
     * @return string
     */
    public function getPageClickXpath($page)
    {
        return $this->_pageHelper->getPageClickXpath($page);
    }

    /**
     * Returns PageID of current page
     *
     * @return string
     */
    public function getCurrentPage()
    {
        return $this->_currentPage;
    }

    /**
     * Find PageID in UIMap in current area using full page URL
     *
     * @param string  $url Full URL to page
     *
     * @return string|boolean
     */
    protected function _findCurrentPageFromUrl($url)
    {
        $baseUrl = $this->_applicationHelper->getBaseUrl();

        $mca = Mage_Selenium_TestCase::_getMcaFromCurrentUrl($baseUrl, $url);
        $page = $this->_pageHelper->getPageByMca($mca, $this->_paramsHelper);
        if ($page) {
            return $page->getPageId();
        } else {
            $this->fail('Can\'t find page for url: ' . $url);
        }

        return false;
    }

    /**
     * Get MCA-part of page URL
     *
     * @param string $baseUrl Base URL
     * @param string $currentUrl Current URL
     *
     * @return string
     */
    protected static function _getMcaFromCurrentUrl($baseUrl, $currentUrl)
    {
        $mca = '';

        $currentUrl = preg_replace('|^http([s]{0,1})://|', '',
                str_replace('/index.php', '/', str_replace('index.php/', '', $currentUrl)));
        $baseUrl = preg_replace('|^http([s]{0,1})://|', '',
                str_replace('/index.php', '/', str_replace('index.php/', '', $baseUrl)));

        if (strpos($currentUrl, $baseUrl) !== false) {
            $mca = trim(substr($currentUrl, strlen($baseUrl)), " /\\");
        }

        if (self::$_area != 'admin') {
            return $mca;
        }

        $mcaArray = explode('/', $mca);

        //Delete secret key from url
        if (in_array('key', $mcaArray)) {
            $key = array_search('key', $mcaArray);
            if ($mcaArray[$key - 1] == 'index') {
                $key = $key - 1;
//                unset($mcaArray[$key - 1]);
            }
//            unset($mcaArray[$key]);
//            unset($mcaArray[$key + 1]);
            $count = count($mcaArray);
            for ($i = $count; $i >= $key; $i--) {
                unset($mcaArray[$i]);
            }
        }

        //Delete action part of mca if it's index
        if (end($mcaArray) == 'index') {
            unset($mcaArray[count($mcaArray) - 1]);
        }

        return implode('/', $mcaArray);
    }

    /**
     * Gets current area<br>
     * Usage: to definition of area what operates in this time.
     * <li>Possible areas: frontend | admin
     *
     * @return string
     */
    public function getArea()
    {
        return self::$_area;
    }

    /**
     * Sets current area<br>
     * Usage: to setup of area what will operates next time
     * <li>Possible areas: frontend | admin
     *
     * @param string $area Area identifier ('admin'|'frontend')
     *
     * @return Mage_Selenium_TestCase
     */
    public function setArea($area)
    {
        self::$_area = $area;
        $this->_applicationHelper->setArea($area);
        return $this;
    }

    /**
     * Retrieves Page's data from UIMap by $pageKey
     *
     * @param string $area Area identifier ('admin'|'frontend')
     * @param string $pageKey UIMap page key
     *
     * @return Mage_Selenium_Uimap_Page
     */
    public function getUimapPage($area, $pageKey)
    {
        $page = $this->_uimapHelper->getUimapPage($area, $pageKey, $this->_paramsHelper);

        if (!$page) {
            $this->fail('Can\'t find page in area "' . $area . '" for key "' . $pageKey . '"');
        }

        return $page;
    }

    /**
     * Retrieves current Page's data from UIMap
     *
     * @return Mage_Selenium_Uimap_Page|NULL
     */
    public function getCurrentUimapPage()
    {
        return $this->getUimapPage($this->getArea(), $this->_currentPage);
    }

    /**
     * Retrieves current Page's data from UIMap
     *
     * @return Mage_Selenium_Uimap_Page|null
     */
    public function getCurrentLocationUimapPage()
    {
        $mca = Mage_Selenium_TestCase::_getMcaFromCurrentUrl($this->_applicationHelper->getBaseUrl(),
                        $this->getLocation());
        $page = $this->_uimapHelper->getUimapPageByMca($this->getArea(), $mca, $this->_paramsHelper);

        if (!$page) {
            $this->fail('Can\'t find page in area "' . $this->getArea() . '" for mca "' . $mca . '"');
        }

        return $page;
    }

    /**
     * Gets XPath of specified control with specified name
     *
     * @param string $controlType Type of control (e.g. button | link | radiobutton | checkbox)
     * @param string $controlName Name of a control from UIMap
     *
     * @return string
     */
    protected function _getControlXpath($controlType, $controlName)
    {
        $uipage = $this->getCurrentLocationUimapPage();
        if (!$uipage) {
            throw new OutOfRangeException("Can't find specified form in UIMap array '"
                    . $this->getLocation() . "', area['" . $this->getArea() . "']");
        }

        $method = 'find' . ucfirst(strtolower($controlType));

        $xpath = $uipage->$method($controlName);

        if (is_object($xpath) && method_exists($xpath, 'getXPath')) {
            $xpath = $xpath->getXPath();
        }

        return $xpath;
    }

    /**
     * Click on specified control with specified name
     *
     * @param string $controlType Type of control (e.g. button|link|radiobutton|checkbox)
     * @param string $controlName Name of a control from UIMap
     * @param boolean $willChangePage Trigger of page reloading. If click on control doesn't<br>
     * lead to page reload, should be FALSE (by default = TRUE)
     *
     * @return Mage_Selenium_TestCase
     */
    public function clickControl($controlType, $controlName, $willChangePage = true)
    {
        $xpath = $this->_getControlXpath($controlType, $controlName);

        if (empty($xpath)) {
            $this->fail('Xpath for control "' . $controlName . '" is empty');
        }

        if (!$this->isElementPresent($xpath)) {
            $this->fail('Control "' . $controlName . '" is not present on the page "' . $this->_currentPage . '". '
                    . 'Type: ' . $controlType . ', xpath: ' . $xpath);
        }

        try {
            $this->click($xpath);

            if ($willChangePage) {
                $this->waitForPageToLoad($this->_browserTimeoutPeriod);
                $this->assertTextNotPresent('Fatal error:', 'Fatal error on page ');
                $this->assertTextNotPresent('There has been an error processing your request',
                        'There has been an error processing your request');
                $this->addParameter('id', $this->defineIdFromUrl());
                $this->_currentPage = $this->_findCurrentPageFromUrl($this->getLocation());
            }
        } catch (PHPUnit_Framework_Exception $e) {
            $this->fail($e->getMessage());
        }

        return $this;
    }

    /**
     * Click on button with specified name
     *
     * @param string $button Button's identifier (Name of a button from UIMap)
     * @param boolean $willChangePage Trigger of page reloading. If click on control doesn't<br>
     * lead to page reload, should be FALSE (by default = TRUE)
     *
     * @return Mage_Selenium_TestCase
     */
    public function clickButton($button, $willChangePage = true)
    {
        $this->clickControl('button', $button, $willChangePage);

        return $this;
    }

    /**
     * Searches specified control with specified name on the page. If control is present - TRUE, else - FALSE
     *
     * @param string $controlType Type of control (e.g. button | link | radiobutton | checkbox)
     * @param string $controlName Name of a control from UIMap
     *
     * @return boolean
     */
    public function controlIsPresent($controlType, $controlName)
    {
        $xpath = $this->_getControlXpath($controlType, $controlName);

        if ($xpath == null) {
            $this->fail("Can't find control: [$controlType: $controlName]");
        }

        if ($this->isElementPresent($xpath)) {
            return true;
        }

        return false;
    }

    /**
     * Searches specified button on the page. If button is present - TRUE, else - FALSE
     *
     * @param string $button Name of a button from UIMap
     *
     * @return boolean
     */
    public function buttonIsPresent($button)
    {
        return $this->controlIsPresent('button', $button);
    }

    /**
     * Waits of appearing and disappearing of "Please wait" animated gif
     *
     * @param integer $waitAppear Timeout for appearing of loader in seconds (by default = 10)
     * @param integer $waitDisappear Timeout for disappearing of loader in seconds (by default = 30)
     *
     * @return Mage_Selenium_TestCase
     */
    public function pleaseWait($waitAppear = 10, $waitDisappear = 30)
    {
        for ($second = 0; $second < $waitAppear; $second++) {
            if ($this->isElementPresent(Mage_Selenium_TestCase::xpathLoadingHolder)) {
                break;
            }
            sleep(1);
        }

        for ($second = 0; $second < $waitDisappear; $second++) {
            if (!$this->isElementPresent(Mage_Selenium_TestCase::xpathLoadingHolder)) {
                break;
            }
            sleep(1);
        }

        return $this;
    }

    /**
     * Fills any form by source data. Specific Tab can be filled only (if it defined)
     *
     * @param array|string $data Array of data to filling or datasource name
     * @param string $tabId Defines a specific Tab on a page to fill (by default = '')
     *
     * @return Mage_Selenium_TestCase|boolean
     */
    public function fillForm($data, $tabId = '')
    {
        if (is_string($data)) {
            $data = $this->loadData($data);
        }

        if (!is_array($data)) {
            throw new InvalidArgumentException('FillForm argument "data" must be an array!!!');
        }

        $page = $this->getCurrentLocationUimapPage();
        if (!$page) {
            throw new OutOfRangeException("Can't find specified form in UIMap array '"
                    . $this->getLocation() . "', area['" . $this->getArea() . "']");
        }

        $formData = $page->getMainForm();

        if (!$formData) {
            return $this;
        }

        $formData->assignParams($this->_paramsHelper);
        if ($tabId && $formData->getTab($tabId)) {
            $fieldsets = $formData->getTab($tabId)->getAllFieldsets();
        } else {
            $fieldsets = $formData->getAllFieldsets();
        }
        $fieldsets->assignParams($this->_paramsHelper);
        // if we have got empty uimap but not empty dataset
        if (empty($fieldsets) && !empty($data)) {
            return false;
        }

        $formDataMap = $this->_getFormDataMap($fieldsets, $data);

        try {
            foreach ($formDataMap as $formFieldName => $formField) {
                switch ($formField['type']) {
                    case self::FIELD_TYPE_INPUT:
                        $this->_fillFormField($formField);
                        break;
                    case self::FIELD_TYPE_CHECKBOX:
                        $this->_fillFormCheckbox($formField);
                        break;
                    case self::FIELD_TYPE_DROPDOWN:
                        $this->_fillFormDropdown($formField);
                        break;
                    case self::FIELD_TYPE_RADIOBUTTON:
                        $this->_fillFormRadiobutton($formField);
                        break;
                    case self::FIELD_TYPE_MULTISELECT:
                        $this->_fillFormMultiselect($formField);
                        break;
                    default:
                        throw new PHPUnit_Framework_Exception('Unsupported field type');
                }
            }
        } catch (PHPUnit_Framework_Exception $e) {
            $errorMessage = isset($formFieldName)
                    ? 'Problem with field \'' . $formFieldName . '\': ' . $e->getMessage()
                    : $e->getMessage();
            $this->fail($errorMessage);
        }

        return true;
    }

    /**
     * Map data values to UIpage form
     *
     * @param array $fieldsets Array of fieldsets for filling
     * @param array $data Array of data to filling
     *
     * @return array
     */
    protected function _getFormDataMap($fieldsets, $data)
    {
        $dataMap = array();
        $uimapFields = array();

        foreach ($data as $dataFieldName => $dataFieldValue) {
            if ($dataFieldValue == '%noValue%') {
                continue;
            }
            foreach ($fieldsets as $fieldset) {
                $uimapFields[self::FIELD_TYPE_MULTISELECT] = $fieldset->getAllMultiselects();
                $uimapFields[self::FIELD_TYPE_DROPDOWN] = $fieldset->getAllDropdowns();
                $uimapFields[self::FIELD_TYPE_RADIOBUTTON] = $fieldset->getAllRadiobuttons();
                $uimapFields[self::FIELD_TYPE_CHECKBOX] = $fieldset->getAllCheckboxes();
                $uimapFields[self::FIELD_TYPE_INPUT] = $fieldset->getAllFields();
                foreach ($uimapFields as $fieldsType => $fieldsData) {
                    foreach ($fieldsData as $uimapFieldName => $uimapFieldValue) {
                        if ($dataFieldName == $uimapFieldName) {
                            $dataMap[$dataFieldName] = array(
                                'type'  => $fieldsType,
                                'path'  => $fieldset->getXpath() . $uimapFieldValue,
                                'value' => $dataFieldValue
                            );
                            break 3;
                        }
                    }
                }
            }
        }

        return $dataMap;
    }

    /**
     * Fills (typing a value) of 'text field' ('field' | 'input') type control
     *
     * @param array $fieldData Array with PATH to control and VALUE to typing
     *
     * @return null
     */
    protected function _fillFormField($fieldData)
    {
        if ($this->isElementPresent($fieldData['path']) && $this->isEditable($fieldData['path'])) {
            $this->type($fieldData['path'], $fieldData['value']);
            $this->waitForAjax();
        } else {
            throw new PHPUnit_Framework_Exception("Can't fill in the field: {$fieldData['path']}");
        }
    }

    /**
     * Fills (makes selection of value(s)) in 'multiselect' type control
     *
     * @param array $fieldData  Array with PATH to control and VALUE(S) to selecting
     *
     * @return null
     */
    protected function _fillFormMultiselect($fieldData)
    {
        if ($this->waitForElement($fieldData['path'], 5) && $this->isEditable($fieldData['path'])) {
            $this->removeAllSelections($fieldData['path']);
            if (strtolower($fieldData['value']) == 'all') {
                $count = $this->getXpathCount($fieldData['path'] . '//option');
                for ($i = 1; $i <= $count; $i++) {
                    $valuesArray[] = $this->getText($fieldData['path'] . "//option[$i]");
                }
            } else {
                $valuesArray = explode(',', $fieldData['value']);
                $valuesArray = array_map('trim', $valuesArray);
            }
            foreach ($valuesArray as $value) {
                if ($value != null) {
                    $this->addSelection($fieldData['path'], 'regexp:' . preg_quote($value));
                }
            }
        } else {
            throw new PHPUnit_Framework_Exception("Can't fill in the multiselect field: {$fieldData['path']}");
        }
    }

    /**
     * Fills (makes selection of value) in 'dropdown' type control
     *
     * @param array $fieldData Array with PATH to control and VALUE to selecting
     *
     * @return null
     */
    protected function _fillFormDropdown($fieldData)
    {
        $fieldXpath = $fieldData['path'];
        if ($this->isElementPresent($fieldXpath) && $this->isEditable($fieldXpath)) {
            if ($this->getSelectedValue($fieldXpath) != $fieldData['value']) {
                if ($this->isElementPresent($fieldXpath . "//option[text()='" . $fieldData['value'] . "']")) {
                    $this->select($fieldXpath, 'label=' . $fieldData['value']);
                } else {
                    $this->select($fieldXpath, 'regexp:' . preg_quote($fieldData['value']));
                }
                $this->waitForAjax();
            }
        } else {
            throw new PHPUnit_Framework_Exception("Can't fill in the dropdown field: {$fieldData['path']}");
        }
    }

    /**
     * Fills (makes selection of value) in 'checkbox' type control
     *
     * @param array $fieldData  Array with PATH to control and VALUE to selecting
     *
     * @return void
     *
     * @
     */
    protected function _fillFormCheckbox($fieldData)
    {
        if ($this->waitForElement($fieldData['path'], 5) && $this->isEditable($fieldData['path'])) {
            if (strtolower($fieldData['value']) == 'yes') {
                if (($this->getValue($fieldData['path']) == 'off') || ($this->getValue($fieldData['path']) == '0')) {
                    $this->click($fieldData['path']);
                    $this->waitForAjax();
                }
            } elseif (strtolower($fieldData['value']) == 'no') {
                if (($this->getValue($fieldData['path']) == 'on') || ($this->getValue($fieldData['path']) == '1')) {
                    $this->click($fieldData['path']);
                    $this->waitForAjax();
                }
            }
        } else {
            throw new PHPUnit_Framework_Exception("Can't fill in the checkbox field: {$fieldData['path']}");
        }
    }

    /**
     * Fills (makes selection of value(s)) in 'radiobutton' type control
     *
     * @param array $fieldData Array with PATH to control and VALUE(S) to selecting
     *
     * @return null
     */
    protected function _fillFormRadiobutton($fieldData)
    {
        if ($this->waitForElement($fieldData['path'], 5) && $this->isEditable($fieldData['path'])) {
            if (strtolower($fieldData['value']) == 'yes') {
                $this->click($fieldData['path']);
                $this->waitForAjax();
            } else {
                $this->uncheck($fieldData['path']);
                $this->waitForAjax();
            }
        } else {
            throw new PHPUnit_Framework_Exception("Can't fill in the radiobutton field: {$fieldData['path']}");
        }
    }

    /**
     * Perform search specified data in specific grid. Returns NULL or XPath of found data.
     *
     * @param array $data Array of looking up data
     * @param string|null $fieldSetName Name of the fieldset with grid (by default = NULL)
     *
     * @return string|null
     */
    public function search(array $data, $fieldSetName = null)
    {
        $this->_prepareDataForSearch($data);
        if (!$data) {
            return null;
        }

        if ($fieldSetName) {
            $fieldSet = $this->getCurrentLocationUimapPage()->findFieldset($fieldSetName);
            $xpath = $fieldSet->getXpath();
            $this->click($xpath . $fieldSet->findButton('reset_filter'));
        } else {
            $xpath = '';
            $this->clickButton('reset_filter', false);
        }
        $this->pleaseWait(3, 10);

        //Forming xpath that contains string 'Total $number records found' where $number - number of items in table
        $totalCount = intval($this->getText($xpath . self::qtyElementsInTable));
        $xpathPager = $xpath . self::qtyElementsInTable . "[not(text()='" . $totalCount . "')]";

        $xpathTR = $this->formSearchXpath($data);

        if (!$this->isElementPresent($xpath . $xpathTR) && $totalCount > 20) {
            // Fill in search form and click 'Search' button
            $this->fillForm($data);
            $this->clickButton('search', false);
            $this->waitForElement($xpathPager);
        }

        if ($this->isElementPresent($xpath . $xpathTR)) {
            return $xpath . $xpathTR;
        }
        return null;
    }

    /**
     * Forming xpath that contains the lookup data
     *
     * @param array $data Array of looking up data
     *
     * @return string
     */
    public function formSearchXpath(array $data)
    {
        $xpathTR = "//table[@class='data']//tr";
        foreach ($data as $key => $value) {
            if (!preg_match('/_from/', $key) and !preg_match('/_to/', $key) and !is_array($value)) {
                $xpathTR .= "[td[normalize-space(text())='$value']]";
            }
        }
        return $xpathTR;
    }

    /**
     * Perform search specified data in specific grid and open result
     *
     * @param array $data Array of looking up data
     * @param boolean $willChangePage Trigger of page reloading. If click on control doesn't<br>
     * lead to page reload, should be FALSE (by default = TRUE)
     * @param string|null $fieldSetName Name of the fieldset with grid (by default = NULL)
     *
     * @return boolean
     */
    public function searchAndOpen(array $data, $willChangePage = true, $fieldSetName = null)
    {
        $this->_prepareDataForSearch($data);
        $xpathTR = $this->search($data, $fieldSetName);

        if ($xpathTR) {
            if ($willChangePage) {
                $itemId = $this->defineIdFromTitle($xpathTR);
                $this->addParameter('id', $itemId);
                $this->click($xpathTR . "/td[contains(text(),'" . $data[array_rand($data)] . "')]");
                $this->waitForPageToLoad($this->_browserTimeoutPeriod);
                $this->_currentPage = $this->_findCurrentPageFromUrl($this->getLocation());
            } else {
                $this->click($xpathTR . "/td[contains(text(),'" . $data[array_rand($data)] . "')]");
                $this->waitForAjax($this->_browserTimeoutPeriod);
            }
            return true;
        }
        return false;
    }

    /**
     * Perform search specified data in specific grid and choose first element
     *
     * @param array $data Array of looking up data
     * @param string|null $fieldSetName Name of the fieldset with grid (by default = NULL)
     *
     * @return void
     */
    public function searchAndChoose(array $data, $fieldSetName = null)
    {
        $xpathTR = $this->search($data, $fieldSetName);
        if ($xpathTR) {
            $xpathTR .="//input[contains(@class,'checkbox') or contains(@class,'radio')][not(@disabled)]";
            if ($this->getValue($xpathTR) == 'off') {
                $this->click($xpathTR);
            }
        } else {
            $this->fail('Cant\'t find item in grig for data: ' . print_r($data, true));
        }
    }

    /**
     * Prepare data array to search in grid
     *
     * @param array $data Array of looking up data
     *
     * @return @array
     */
    protected function _prepareDataForSearch(array &$data)
    {
        foreach ($data as $key => $val) {
            if ($val == '%noValue%' or empty($val)) {
                unset($data[$key]);
            } elseif (preg_match('/website/', $key)) {
                $xpathField = $this->getCurrentLocationUimapPage()->getMainForm()->findDropdown($key);
                if (!$this->isElementPresent($xpathField)) {
                    unset($data[$key]);
                }
            }
        }

        return $data;
    }

    /**
     * Define parameter %Id% from XPath Title
     *
     * @param string $xpathTR XPath of control with 'title' attribute to retrieve an ID
     *
     * @return integer
     */
    public function defineIdFromTitle($xpathTR)
    {
        // ID definition
        $itemId = 0;
        $title = $this->getValue($xpathTR . '/@title');
        if (is_numeric($title)) {
            $itemId = $title;
        } else {
            $titleArr = explode('/', $title);
            foreach ($titleArr as $key => $value) {
                if (preg_match('/id$/', $value) and isset($titleArr[$key + 1])) {
                    $itemId = $titleArr[$key + 1];
                    break;
                }
            }
        }
        return $itemId;
    }

    /**
     * Define parameter %Id% from URL
     *
     * @return integer
     */
    public function defineIdFromUrl()
    {
        // ID definition
        $item_id = 0;
        $title_arr = explode('/', $this->getLocation());
        $title_arr = array_reverse($title_arr);
        foreach ($title_arr as $key => $value) {
            if (preg_match('/id$/', $value) && isset($title_arr[$key - 1])) {
                $item_id = $title_arr[$key - 1];
                break;
            }
        }
        return $item_id;
    }

    /**
     * Messages helper methods
     */

    /**
     * Adds field ID to Message Xpath (set %fieldId% parameter)
     *
     * @param srting $fieldType Field's type
     * @param srting $fieldName Field's name from UIMap
     *
     * @return null
     */
    public function addFieldIdToMessage($fieldType, $fieldName)
    {
        $fieldXpath = $this->_getControlXpath($fieldType, $fieldName);
        if ($this->isElementPresent($fieldXpath . '/@id')) {
            $fieldId = $this->getAttribute($fieldXpath . '/@id');
        } else {
            $fieldId = $this->getAttribute($fieldXpath . '/@name');
        }
        $this->addParameter('fieldId', $fieldId);
    }

    /**
     * Check if message exists on page
     *
     * @param string $message  Message Id from UIMap
     *
     * @return boolean
     */
    public function checkMessage($message)
    {
        $page = $this->getCurrentLocationUimapPage();
        $messageLocator = $page->findMessage($message);
        return $this->checkMessageByXpath($messageLocator);
    }

    /**
     * Checks if message with specified XPath exists on page
     *
     * @param string $xpath XPath of message to checking
     *
     * @return boolean
     */
    public function checkMessageByXpath($xpath)
    {
        $this->_parseMessages();
        if ($xpath && $this->isElementPresent($xpath)) {
            return true;
        }

        return false;
    }

    /**
     * Check if any 'error' message exists on page
     *
     * @param string $message Error message's ID from UIMap OR XPath of error message (by default = NULL)
     *
     * @return boolean
     */
    public function errorMessage($message = null)
    {
        return (!empty($message))
            ? $this->checkMessage($message)
            : $this->checkMessageByXpath(self::xpathErrorMessage);
    }

    /**
     * Returns all 'error' messages on page
     *
     * @return array
     */
    public function getErrorMessages()
    {
        $this->_parseMessages();

        return $this->messages['error'];
    }

    /**
     * Check if any 'success' message exists on page
     *
     * @param string $message Success message's ID from UIMap OR XPath of success message (by default = NULL)
     *
     * @return boolean
     */
    public function successMessage($message = null)
    {
        return (!empty($message))
            ? $this->checkMessage($message)
            : $this->checkMessageByXpath(self::xpathSuccessMessage);
    }

    /**
     * Returns all 'success' messages on page
     *
     * @return array
     */
    public function getSuccessMessages()
    {
        $this->_parseMessages();

        return $this->messages['success'];
    }

    /**
     * Checks if any 'validation' message exists on page
     *
     * @param string $message Validation message's ID from UIMap OR XPath of validation message (by default = NULL)
     *
     * @return boolean
     */
    public function validationMessage($message = null)
    {
        return (!empty($message))
            ? $this->checkMessage($message)
            : $this->checkMessageByXpath(self::xpathValidationMessage);
    }

    /**
     * Returns all 'validation' messages on page
     *
     * @return array
     */
    public function getValidationMessages()
    {
        $this->_parseMessages();

        return $this->messages['validation'];
    }

    /**
     * Gets all messages on page
     *
     * @return null
     */
    protected function _parseMessages()
    {
        $this->messages['success'] = $this->getElementsByXpath(self::xpathSuccessMessage);
        $this->messages['error'] = $this->getElementsByXpath(self::xpathErrorMessage);
        $this->messages['validation'] = $this->getElementsByXpath(self::xpathValidationMessage, 'text',
                self::xpathFieldNameWithValidationMessage);
    }

    /**
     * Gets all element(s) by XPath
     *
     * @param string $xpath General XPath of looking up element(s)
     * @param string $get What to get. Allowed params: 'text' or 'value' (by defauilt = 'text')
     * @param string $additionalXPath Additional XPath (by defauilt = '')
     *
     * @return array
     */
    public function getElementsByXpath($xpath, $get = 'text', $additionalXPath = '')
    {
        $elements = array();

        if (!empty($xpath)) {
            $totalElements = $this->getXpathCount($xpath);
            $extraXpath = '';
            if ($totalElements > 1 && $xpath != self::xpathValidationMessage) {
                if (substr($xpath, 0, 2) == '//') {
                    $middle = substr($xpath, 2);
                } else {
                    $middle = $xpath;
                }
                $xpathAdditional = $xpath;
                $begin = '//*[';
                $end = ']';
                $additional = '';
                while ($this->isElementPresent($xpathAdditional . '[2]') == FALSE) {
                    $additional .= '*/';
                    $needXpath = $begin . $additional . $middle . $end;
                    $xpathAdditional = $needXpath;
                }
                $extraXpath = $xpathAdditional;
            }
            for ($i = 1; $i < $totalElements + 1; $i++) {
                if (!$extraXpath || $extraXpath == $xpath) {
                    $x = $xpath . '[' . $i . ']';
                } else {
                    $x = $extraXpath . '[' . $i . ']' . $xpath;
                }

                switch ($get) {
                    case 'value' :
                        $element = $this->getValue($x);
                        break;
                    case 'text' :
                        $element = $this->getText($x);
                        break;
                    default :
                        $this->fail('Possible values of the variable $get only "text" and "value"');
                        break;
                }

                if (!empty($element)) {
                    if ($additionalXPath) {
                        if ($this->isElementPresent($x . $additionalXPath)) {
                            $label = trim($this->getText($x . $additionalXPath), " *\t\n\r");
                        } else {
                            $label = $this->getAttribute($x . "@id");
                            $label = strrev($label);
                            $label = strrev(substr($label, 0, strpos($label, "-")));
                        }
                        if ($label) {
                            $element = '"' . $label . '": ' . $element;
                        }
                    }

                    $elements[] = $element;
                }
            }
        }

        return $elements;
    }

    /**
     * Gets element by XPath
     *
     * @param string $xpath XPath of looking up element
     * @param string $get What to get. Allowed params: 'text' or 'value' (by defauilt = 'text')
     *
     * @return array
     */
    public function getElementByXpath($xpath, $get = 'text')
    {
        return array_shift($this->getElementsByXpath($xpath, $get));
    }

    /**
     * Magento helper methods
     */

    /**
     * Performs LogOut customer on front-end
     *
     * @return Mage_Selenium_TestCase
     */
    public function logoutCustomer()
    {
        try {
            $this->frontend('home');
            $xpath = "//a[@title='Log Out']";
            if ($this->isElementPresent($xpath)) {
                $this->clickAndWait($xpath, $this->_browserTimeoutPeriod);
                $this->frontend('home');
            }
            if ($this->getTitle() !== 'Home page') {
                throw new PHPUnit_Framework_Exception('Home page was not opened');
            }
        } catch (PHPUnit_Framework_Exception $e) {
            $this->fail($e->getMessage());
        }
        return $this;
    }

    /**
     * Performs LogIn admin user on back-end
     *
     * @return Mage_Selenium_TestCase
     */
    public function loginAdminUser()
    {
        try {
            $this->admin('log_in_to_admin');

            $currentPage = $this->_findCurrentPageFromUrl($this->getLocation());
            if ($currentPage != $this->_firstPageAfterAdminLogin) {
                if ($currentPage == 'log_in_to_admin') {
                    $loginData = array(
                        'user_name' => $this->_applicationHelper->getDefaultAdminUsername(),
                        'password' => $this->_applicationHelper->getDefaultAdminPassword()
                    );
                    $this->fillForm($loginData);
                    $this->clickButton('login', false);
                    $this->waitForElement(array(self::xpathAdminLogo,
                                                self::xpathErrorMessage,
                                                self::xpathValidationMessage));
                    if (!$this->checkCurrentPage($this->_firstPageAfterAdminLogin)) {
                        throw new PHPUnit_Framework_Exception('Admin was not logged in');
                    }
                    $this->_currentPage = $this->_findCurrentPageFromUrl($this->getLocation());
                } else {
                    throw new PHPUnit_Framework_Exception('Wrong page was opened');
                }
            }
        } catch (PHPUnit_Framework_Exception $e) {
            $this->fail($e->getMessage());
        }
        return $this;
    }

    /**
     * Performs LogOut admin user on back-end
     *
     * @return Mage_Selenium_TestCase
     */
    public function logoutAdminUser()
    {
        try {
            if ($this->isElementPresent(self::xpathLogOutAdmin)) {
                $this->click(self::xpathLogOutAdmin);
                $this->waitForPageToLoad($this->_browserTimeoutPeriod);
                $this->_currentPage = $this->_findCurrentPageFromUrl($this->getLocation());
                if (!$this->checkCurrentPage('log_in_to_admin')) {
                    throw new PHPUnit_Framework_Exception('Admin was not logged out');
                }
            }
        } catch (PHPUnit_Framework_Exception $e) {
            $this->fail($e->getMessage());
        }
        return $this;
    }

    /**
     * Assertions Methods
     */

    /**
     * Asserts $condition and reports an error $message if $condition is FALSE.
     *
     * @param boolean $condition Condition to assert
     * @param string $message Message to report if condition will FALSE (by default = '')
     *
     * @return PHPUnit_Framework_AssertionFailedError
     */
    public static function assertTrue($condition, $message = '')
    {
        if (is_array($message)) {
            $message = implode("\n", call_user_func_array('array_merge', $message));
        }

        if (is_object($condition)) {
            $condition = (false === $condition->hasError());
        }

        self::assertThat($condition, self::isTrue(), $message);

        if (isset($this)) {
            return $this;
        }
    }

    /**
     * Asserts $condition and reports an error $message if $condition is TRUE.
     *
     * @param boolean $condition Condition to assert
     * @param string $message Message to report if condition will TRUE (by default = '')
     *
     * @return PHPUnit_Framework_AssertionFailedError
     */
    public static function assertFalse($condition, $message = '')
    {
        if (is_array($message)) {
            $message = implode("\n", call_user_func_array('array_merge', $message));
        }

        if (is_object($condition)) {
            $condition = (false === $condition->hasError());
        }

        self::assertThat($condition, self::isFalse(), $message);

        if (isset($this)) {
            return $this;
        }
    }

    /**
     * Gets node | value from DataSet by path to data source
     *
     * @param string $path Path to data source (e.g. filename in ../data without .yml extension) (by default = '')
     *
     * @return array|string
     */
    protected function _getData($path='')
    {
        return $this->_testConfig->getDataValue($path);
    }

    /**
     * Performs submit form and confirmation popup
     *
     * @param string $buttonName Name of a button from UIMap
     * @param string $message Message ID from UIMap
     *
     * @return boolean
     */
    public function clickButtonAndConfirm($buttonName, $message)
    {
        $buttonXpath = $this->_getControlXpath('button', $buttonName);
        if ($this->isElementPresent($buttonXpath)) {
            $confirmation = $this->getCurrentLocationUimapPage()->findMessage($message);
            $this->chooseCancelOnNextConfirmation();
            $this->click($buttonXpath);
            if ($this->isConfirmationPresent()) {
                $text = $this->getConfirmation();
                if ($text == $confirmation) {
                    $this->chooseOkOnNextConfirmation();
                    $this->click($buttonXpath);
                    $this->getConfirmation();
                    $this->waitForPageToLoad($this->_browserTimeoutPeriod);
                    $this->_currentPage = $this->_findCurrentPageFromUrl($this->getLocation());

                    return true;
                } else {
                    $this->messages['error'][] = "The confirmation text incorrect: {$text}\n";
                }
            } else {
                $this->messages['error'][] = "The confirmation does not appear\n";
                $this->waitForPageToLoad($this->_browserTimeoutPeriod);
                $this->_currentPage = $this->_findCurrentPageFromUrl($this->getLocation());

                return true;
            }
        } else {
            $this->messages['error'][] = "There is no way to click button(There is no '$buttonName' button)\n";
        }

        return false;
    }

    /**
     * Waiting for element appearance
     *
     * @param string|array $locator XPath locator or array of locators
     * @param integer $timeout Timeout period in seconds (by default = 40)
     *
     * @return boolean
     */
    public function waitForElement($locator, $timeout = 40)
    {
        $iStartTime = time();
        while ($timeout > time() - $iStartTime) {

            if (is_array($locator)) {
                foreach ($locator as $loc) {
                    if ($this->isElementPresent($loc)) {
                        return true;
                    }
                }
            } else {
                if ($this->isElementPresent($locator)) {
                    return true;
                }
            }
            sleep(1);
        }
        return false;
    }

    /**
     * Waiting for AJAX request to continue<br>
     * Method works only if AJAX request was perform with Prototype or JQuery framework
     *
     * @param integer $timeout Timeout period in milliseconds (by default = 30000)
     *
     * @return void
     */
    public function waitForAjax($timeout = 30000)
    {
        $jsCondition = 'var c = function(){if(typeof selenium.browserbot.getCurrentWindow().Ajax != "undefined"){'
                . 'if(selenium.browserbot.getCurrentWindow().Ajax.activeRequestCount){return false;};};'
                . 'if(typeof selenium.browserbot.getCurrentWindow().jQuery != "undefined"){'
                . 'if(selenium.browserbot.getCurrentWindow().jQuery.active){return false;};};return true;};c();';
        $this->waitForCondition($jsCondition, $timeout);
    }

    /**
     * Performs save opened form for submit
     *
     * @param string $buttonName Name of the button, what intended to save (submit) form (from UIMap)
     *
     * @return Mage_Selenium_TestCase
     */
    public function saveForm($buttonName)
    {
        $this->messages = array();
        $this->_parseMessages();
        foreach ($this->messages as $key => $value) {
            $this->messages[$key] = array_unique($value);
        }
        $success = self::xpathSuccessMessage;
        $error = self::xpathErrorMessage;
        $validation = self::xpathValidationMessage;
        $types = array('success', 'error', 'validation');
        foreach ($types as $message) {
            if (array_key_exists($message, $this->messages)) {
                $exclude = '';
                foreach ($this->messages[$message] as $messageText) {
                    $exclude .="[not(..//.='$messageText')]";
                }
                ${$message} .= $exclude;
            }
        }
        $this->clickButton($buttonName, false);
        $this->waitForElement(array($success, $error, $validation));
        $this->addParameter('id', $this->defineIdFromUrl());
        $this->assertTextNotPresent('Fatal error:', 'Fatal error on page');
        $this->assertTextNotPresent('There has been an error processing your request',
                    'There has been an error processing your request');
        $this->_currentPage = $this->_findCurrentPageFromUrl($this->getLocation());
        $this->getCurrentLocationUimapPage()->assignParams($this->_paramsHelper);
//        $this->clickButton($buttonName, false);
//        $this->waitForElement(array(self::xpathErrorMessage,
//                                    self::xpathValidationMessage,
//                                    self::xpathSuccessMessage));
//        $this->addParameter('id', $this->defineIdFromUrl());
//        $this->_currentPage = $this->_findCurrentPageFromUrl($this->getLocation());

        return $this;
    }

    /**
     * Performs verify opened form values
     *
     * @param array|string $data Array of data to verifying or datasource name
     * @param string $tabName Defines a specific Tab on a page with form to verification (by default = '')
     * @param array $skipElements Array of elements, what will skipped during verification (default = array('password'))
     *
     * @return boolean
     */
    public function verifyForm($data, $tabName = '', $skipElements = array('password'))
    {
        if (is_string($data)) {
            $data = $this->loadData($data);
        }
        if (!is_array($data)) {
            throw new InvalidArgumentException('FillForm argument "data" must be an array!!!');
        }

        $page = $this->getCurrentLocationUimapPage();
        if (!$page) {
            throw new OutOfRangeException("Can't find specified form in UIMap array '"
                    . $this->getLocation() . "', area['" . $this->getArea() . "']");
        }

        $formData = $page->getMainForm();
        if (!$formData) {
            throw new OutOfRangeException("Can't find main form in UIMap array '"
                    . $this->getLocation() . "', area['" . $this->getArea() . "']");
        }
        $formData->assignParams($this->_paramsHelper);

        if ($tabName) {
            $fieldsets = $formData->getTab($tabName)->getAllFieldsets();
        } else {
            $fieldsets = $formData->getAllFieldsets();
        }

        if (empty($fieldsets) && !empty($data)) {
            return false;
        }

        $resultFlag = true;
        foreach ($data as $d_key => $d_val) {

            if (in_array($d_key, $skipElements) || $d_val == '%noValue%') {
                continue;
            }

            foreach ($fieldsets as $fieldsetName => $fieldset) {
                // Next fieldset flag
                $stopFlag = false;

                $baseXpath = $fieldset->getXPath();

                // ----------------------------------------------------
                $fields = $fieldset->getAllMultiselects();
                if (!empty($fields)) {
                    foreach ($fields as $fieldKey => $fieldXPath) {
                        if ($fieldKey == $d_key) {

                            $elemXPath = $baseXpath . $fieldXPath;
                            if ($this->isElementPresent($elemXPath)) {
                                $labels = $this->getSelectedLabels($elemXPath);
                                if (!in_array($d_val, $labels)) {
                                    $this->messages['error'][] = "The stored value for '"
                                            . $d_key . "' field is not equal to specified: ('$d_val' != '$labels')";
                                    $resultFlag = false;
                                }
                            } else {
                                $this->messages['error'][] = "Can't find '$d_key' field";
                                $resultFlag = false;
                            }

                            break;
                        }
                    }

                    if ($stopFlag) {
                        continue;
                    }
                }

                // ----------------------------------------------------
                $fields = $fieldset->getAllDropdowns();
                if (!empty($fields)) {
                    foreach ($fields as $fieldKey => $fieldXPath) {
                        if ($fieldKey == $d_key) {

                            $elemXPath = $baseXpath . $fieldXPath;
                            if ($this->isElementPresent($elemXPath)) {
                                $labels = $this->getSelectedLabels($elemXPath);
                                if (!in_array($d_val, $labels)) {
                                    $this->messages['error'][] = "The stored value for '"
                                            . $d_key . "' field is not equal to specified: ('$d_val' != '"
                                            . array_shift($labels) . "')";
                                    $resultFlag = false;
                                }
                            } else {
                                $this->messages['error'][] = "Can't find '$d_key' field";
                                $resultFlag = false;
                            }

                            break;
                        }
                    }

                    if ($stopFlag) {
                        continue;
                    }
                }

                // ----------------------------------------------------
                $fields = $fieldset->getAllRadiobuttons();
                if (!empty($fields)) {
                    foreach ($fields as $fieldKey => $fieldXPath) {
                        if ($fieldKey == $d_key) {

                            $elemXPath = $baseXpath . $fieldXPath;
                            if ($this->isElementPresent($elemXPath)) {
                                $f_val = $this->getValue($elemXPath);
                                if (($f_val == 'on' && strtolower($d_val) != 'yes') ||
                                        ($f_val == 'off' && !(strtolower($d_val) == 'no' || $d_val == ''))) {
                                    $printVal = ($f_val == 'on') ? 'yes' : 'no';
                                    $this->messages['error'][] = "The stored value for '"
                                            . $d_key . "' field is not equal to specified: ('$d_val' != '$printVal')";
                                    $resultFlag = false;
                                }
                            } else {
                                $this->messages['error'][] = "Can't find '$d_key' field";
                                $resultFlag = false;
                            }

                            break;
                        }
                    }

                    if ($stopFlag) {
                        continue;
                    }
                }

                // ----------------------------------------------------
                $fields = $fieldset->getAllCheckboxes();
                if (!empty($fields)) {
                    foreach ($fields as $fieldKey => $fieldXPath) {
                        if ($fieldKey == $d_key) {

                            $elemXPath = $baseXpath . $fieldXPath;
                            if ($this->isElementPresent($elemXPath)) {
                                $f_val = $this->getValue($elemXPath);
                                if (($f_val == 'on' && strtolower($d_val) != 'yes') ||
                                        ($f_val == 'off' && !(strtolower($d_val) == 'no' || $d_val == ''))) {
                                    $printVal = ($f_val == 'on') ? 'yes' : 'no';
                                    $this->messages['error'][] = "The stored value for '"
                                            . $d_key . "' field is not equal to specified: ('$d_val' != '$printVal')";
                                    $resultFlag = false;
                                }
                            } else {
                                $this->messages['error'][] = "Can't find '$d_key' field";
                                $resultFlag = false;
                            }

                            break;
                        }
                    }

                    if ($stopFlag) {
                        continue;
                    }
                }

                // ----------------------------------------------------
                $fields = $fieldset->getAllFields();
                if (!empty($fields)) {
                    foreach ($fields as $fieldKey => $fieldXPath) {
                        if ($fieldKey == $d_key) {
                            $elemXPath = $baseXpath . $fieldXPath;
                            if ($this->isElementPresent($elemXPath)) {
                                if ($this->getValue($elemXPath) != $d_val) {
                                    $this->messages['error'][] = "The stored value for '"
                                            . $d_key . "' field is not equal to specified: ('$d_val' != '"
                                            . $this->getValue($elemXPath) . "')";
                                    $resultFlag = false;
                                }
                            } else {
                                $this->messages['error'][] = "Can't find '$d_key' field";
                                $resultFlag = false;
                            }

                            break;
                        }
                    }

                    if ($stopFlag) {
                        continue;
                    }
                }
            }
        }

        return $resultFlag;
    }

    /**
     * Performs verify of messages count
     *
     * @param integer $count Expected count of message(s) on the page
     * @param string $xpath XPath of a message(s), what should be evaluated
     *
     * @return integer The number of nodes that match the specified $xpath
     */
    public function verifyMessagesCount($count = 1, $xpath = Mage_Selenium_TestCase::xpathValidationMessage)
    {
        $this->_parseMessages();
        return $this->getXpathCount($xpath) == $count;
    }

    /**
     * Performs verify of element presention on a page
     *
     * @param string $xpath XPath of a element, what should be verified
     */
    public function verifyElementPresent($xpath)
    {
        try {
            $this->assertTrue($this->isElementPresent($xpath));
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            $this->verificationErrors[] = $e->toString();
        }
    }

    /**
     * Redefined PHPUnit_Extensions_SeleniumTestCase::suite, make possible to use dependency
     *
     * @param  string $className Name of class what loaded to parsing and execute
     *
     * @return PHPUnit_Framework_TestSuite
     */
    public static function suite($className)
    {

        $suite = new PHPUnit_Framework_TestSuite;
        $suite->setName($className);

        $class = new ReflectionClass($className);
        $classGroups = PHPUnit_Util_Test::getGroups($className);
        $staticProperties = $class->getStaticProperties();

        // Create tests from Selenese/HTML files.
        if (isset($staticProperties['seleneseDirectory']) &&
                is_dir($staticProperties['seleneseDirectory'])) {
            $files = array_merge(
                    self::getSeleneseFiles($staticProperties['seleneseDirectory'], '.htm'),
                    self::getSeleneseFiles($staticProperties['seleneseDirectory'], '.html')
            );

            // Create tests from Selenese/HTML files for multiple browsers.
            if (!empty($staticProperties['browsers'])) {
                foreach ($staticProperties['browsers'] as $browser) {
                    $browserSuite = new PHPUnit_Framework_TestSuite;
                    $browserSuite->setName($className . ': ' . $browser['name']);

                    foreach ($files as $file) {
                        $browserSuite->addTest(
                                //new $className($file, array(), '', $browser),
                                self::addTestDependencies(
                                        new $className($file, array(), '', $browser), $className, $name), $classGroups
                        );
                    }

                    $suite->addTest($browserSuite);
                }
            }

            // Create tests from Selenese/HTML files for single browser.
            else {
                foreach ($files as $file) {
                    $suite->addTest(new $className($file), $classGroups);
                }
            }
        }

        // Create tests from test methods for multiple browsers.
        if (!empty($staticProperties['browsers'])) {
            foreach ($staticProperties['browsers'] as $browser) {
                $browserSuite = new PHPUnit_Framework_TestSuite;
                $browserSuite->setName($className . ': ' . $browser['name']);

                foreach ($class->getMethods() as $method) {
                    if (PHPUnit_Framework_TestSuite::isPublicTestMethod($method)) {
                        $name = $method->getName();
                        $data = PHPUnit_Util_Test::getProvidedData($className, $name);
                        $groups = PHPUnit_Util_Test::getGroups($className, $name);

                        // Test method with @dataProvider.
                        if (is_array($data) || $data instanceof Iterator) {
                            $dataSuite = new PHPUnit_Framework_TestSuite_DataProvider(
                                            $className . '::' . $name
                            );

                            foreach ($data as $_dataName => $_data) {
                                $dataSuite->addTest(
                                        //new $className($name, $_data, $_dataName, $browser),
                                        self::addTestDependencies(
                                                new $className($name, $_data, $_dataName, $browser), $className, $name),
                                        $groups
                                );
                            }

                            $browserSuite->addTest($dataSuite);
                        }

                        // Test method with invalid @dataProvider.
                        else if ($data === false) {
                            $browserSuite->addTest(
                                    new PHPUnit_Framework_Warning(
                                            sprintf(
                                                    'The data provider specified for %s::%s is invalid.', $className,
                                                    $name
                                            )
                                    )
                            );
                        }

                        // Test method without @dataProvider.
                        else {
                            $browserSuite->addTest(
                                    // new $className($name, array(), '', $browser),
                                    self::addTestDependencies(
                                            new $className($name, array(), '', $browser), $className, $name), $groups
                            );
                        }
                    }
                }

                $suite->addTest($browserSuite);
            }
        }

        // Create tests from test methods for single browser.
        else {
            foreach ($class->getMethods() as $method) {
                if (PHPUnit_Framework_TestSuite::isPublicTestMethod($method)) {
                    $name = $method->getName();
                    $data = PHPUnit_Util_Test::getProvidedData($className, $name);
                    $groups = PHPUnit_Util_Test::getGroups($className, $name);

                    // Test method with @dataProvider.
                    if (is_array($data) || $data instanceof Iterator) {
                        $dataSuite = new PHPUnit_Framework_TestSuite_DataProvider(
                                        $className . '::' . $name
                        );

                        foreach ($data as $_dataName => $_data) {
                            $dataSuite->addTest(
                                    //new $className($name, $_data, $_dataName),
                                    self::addTestDependencies(
                                            new $className($name, $_data, $_dataName), $className, $name), $groups
                            );
                        }

                        $suite->addTest($dataSuite);
                    }

                    // Test method with invalid @dataProvider.
                    else if ($data === false) {
                        $suite->addTest(
                                new PHPUnit_Framework_Warning(
                                        sprintf(
                                                'The data provider specified for %s::%s is invalid.', $className, $name
                                        )
                                )
                        );
                    }

                    // Test method without @dataProvider.
                    else {
                        $suite->addTest(
                                // new $className($name),
                                self::addTestDependencies(new $className($name), $className, $name), $groups
                        );
                    }
                }
            }
        }

        return $suite;
    }

    /**
     * Takes a test and adds its dependencies
     *
     * @param PHPUnit_Framework_Test $test Object. A Test can be run and collect its results
     * @param string $className  Name of class what loaded to parsing and execute
     * @param string $methodName Name of method what loaded from class to adding dependencies
     *
     * @return void
     */
    public static function addTestDependencies(PHPUnit_Framework_Test $test, $className, $methodName)
    {
        if ($test instanceof PHPUnit_Framework_TestCase ||
                $test instanceof PHPUnit_Framework_TestSuite_DataProvider) {
            $test->setDependencies(
                    PHPUnit_Util_Test::getDependencies($className, $methodName)
            );
        }
        return $test;
    }

    /**
     * Runs the test case and collects the results in a TestResult object.<br>
     * If no TestResult object is passed a new one will be created.
     *
     * @param  PHPUnit_Framework_TestResult $result Objec to collect of test results (by default = NULL)
     *
     * @return PHPUnit_Framework_TestResult
     *
     * @throws InvalidArgumentException
     */
    public function run(PHPUnit_Framework_TestResult $result = null)
    {
        if ($result === null) {
            $result = $this->createResult();
        }

        //$this->setResult($result);
        $this->result = $result;
        $this->setExpectedExceptionFromAnnotation();
        $this->setUseErrorHandlerFromAnnotation();
        $this->setUseOutputBufferingFromAnnotation();

        $this->collectCodeCoverageInformation = $result->getCollectCodeCoverageInformation();

        foreach ($this->drivers as $driver) {
            $driver->setCollectCodeCoverageInformation(
                    $this->collectCodeCoverageInformation
            );
        }

        if (!$this->handleDependencies()) {
            return;
        }

        $result->run($this);

        if ($this->collectCodeCoverageInformation) {
            $result->getCodeCoverage()->append(
                    $this->getCodeCoverage(), $this
            );
        }

        return $result;
    }

    /**
     * Performs a handling of dependencies between test what currently executing.
     *
     * @return boolean
     *
     * @since Method available since Release 3.5.4
     */
    protected function handleDependencies()
    {
        if (!empty($this->dependencies) && !$this->inIsolation) {
            $className = get_class($this);
            $passed = $this->result->passed();

            $passedKeys = array_keys($passed);
            $numKeys = count($passedKeys);

            for ($i = 0; $i < $numKeys; $i++) {
                $pos = strpos($passedKeys[$i], ' with data set');

                if ($pos !== false) {
                    $passedKeys[$i] = substr($passedKeys[$i], 0, $pos);
                }
            }

            $passedKeys = array_flip(array_unique($passedKeys));

            foreach ($this->dependencies as $dependency) {
                if (strpos($dependency, '::') === false) {
                    $dependency = $className . '::' . $dependency;
                }

                if (!isset($passedKeys[$dependency])) {
                    $this->result->addError(
                            $this,
                            new PHPUnit_Framework_SkippedTestError(
                                    sprintf('This test depends on "%s" to pass.', $dependency)
                            ), 0
                    );

                    return false;
                } else {
                    if (isset($passed[$dependency])) {
                        $this->dependencyInput[] = $passed[$dependency];
                    } else {
                        $this->dependencyInput[] = null;
                    }
                }
            }
        }

        return true;
    }

    /**
     * Override to run the test and assert its state
     *
     * @return mixed
     *
     * @throws RuntimeException
     */
    protected function runTest()
    {
        if ($this->name === null) {
            throw new PHPUnit_Framework_Exception(
                    'PHPUnit_Framework_TestCase::$name must not be null.'
            );
        }

        try {
            $class = new ReflectionClass($this);
            $method = $class->getMethod($this->name);
        } catch (ReflectionException $e) {
            $this->fail($e->getMessage());
        }

        try {

            $testResult = $method->invokeArgs(
                    $this, array_merge($this->data, $this->dependencyInput)
            );
        } catch (Exception $e) {
            if (!$e instanceof PHPUnit_Framework_IncompleteTest &&
                    !$e instanceof PHPUnit_Framework_SkippedTest &&
                    is_string($this->expectedException) &&
                    $e instanceof $this->expectedException) {
                if (is_string($this->expectedExceptionMessage) &&
                        !empty($this->expectedExceptionMessage)) {
                    $this->assertContains(
                            $this->expectedExceptionMessage, $e->getMessage()
                    );
                }

                if (is_int($this->expectedExceptionCode) &&
                        $this->expectedExceptionCode !== 0) {
                    $this->assertEquals(
                            $this->expectedExceptionCode, $e->getCode()
                    );
                }

                $this->numAssertions++;

                return;
            } else {
                throw $e;
            }
        }

        if ($this->expectedException !== null) {
            $this->numAssertions++;

            $this->syntheticFail(
                    'Expected exception ' . $this->expectedException, '', 0, $this->expectedExceptionTrace
            );
        }

        return $testResult;
    }

    /**
     * Performs scrolling to specific element in the specified list(block) with specified name.
     *
     * @param string $elementType Type of the element what should be visible after scrolling
     * @param string $elementName Name of the element what should be visible after scrolling
     * @param string $blockType Type of the block where scroll is using
     * @param string $blockName Name of the block where scroll is using
     *
     * @return null
     */
    public function moveScrollToElement($elementType, $elementName, $blockType, $blockName)
    {
        // getting XPath of the element what should be visible after scrolling
        $specElemantXpath = $this->_getControlXpath($elementType, $elementName);
        // getting @ID of the element what should be visible after scrolling
        $specElementId = $this->getAttribute($specElemantXpath . "/@id");

        // getting XPath of the block where scroll is using
        $specFieldsetXpath = $this->_getControlXpath($blockType, $blockName);
        // getting @ID of the block where scroll is using
        $specFieldsetId = $this->getAttribute($specFieldsetXpath . "/@id");

        // getting offset position of the element what should be visible after scrolling
        $destinationOffsetTop = $this->getEval("this.browserbot.findElement('id=" . $specElementId . "').offsetTop");
        // moving scroll bar to previously defined offest
        // position (to the element what should be visible after scrolling)
        $this->getEval("this.browserbot.findElement('id=" . $specFieldsetId
                . "').scrollTop = " . $destinationOffsetTop);
    }

    /**
     * Moving specific element (with type = $elementType and name = $elementName)<br>
     * over the specified JS tree (with type = $blockType and name = $blockName)<br>
     * to position = $moveToPosition
     *
     * @param string $elementType Type of the element to move
     * @param string $elementName Name of the element to move
     * @param string $blockType Type of the block what contains JS tree
     * @param string $blockName Name of the block what contains JS tree
     * @param integer $moveToPosition Index of position where element should be after moving (default = 1)
     *
     * @return null
     */
    public function moveElementOverTree($elementType, $elementName, $blockType, $blockName, $moveToPosition = 1)
    {
        // getting XPath of the element to move
        $specElemantXpath = $this->_getControlXpath($elementType, $elementName);
        // getting @ID of the element to move
        $specElementId = $this->getAttribute($specElemantXpath . "/@id");

        // getting XPath of the block what is a JS tree
        $specFieldsetXpath = $this->_getControlXpath($blockType, $blockName);
        // getting @ID of the block what is a JS tree
        $specFieldsetId = $this->getAttribute($specFieldsetXpath . "/@id");

        // getting offset position of the element to move
        $destinationOffsetTop = $this->getEval("this.browserbot.findElement('id=" . $specElementId . "').offsetTop");

        // storing of current height of the block with JS tree
        $tmpBlockHeight = (integer) $this->getEval("this.browserbot.findElement('id="
                        . $specFieldsetId . "').style.height");

        // if element to move situated abroad of the current height, it will be increased
        if ($destinationOffsetTop >= $tmpBlockHeight) {
            $destinationOffsetTop = $destinationOffsetTop + 50;
            $this->getEval("this.browserbot.findElement('id=" . $specFieldsetId
                    . "').style.height='" . $destinationOffsetTop . "px'");
        }

        $this->clickAt($specElemantXpath, '1,1');
        $blockTo = $specFieldsetXpath . '//li[' . $moveToPosition . ']//a//span';
        $this->mouseDownAt($specElemantXpath, '1,1');
        $this->mouseMoveAt($blockTo, '1,1');
        $this->mouseUpAt($blockTo, '1,1');
        $this->clickAt($specElemantXpath, '1,1');
    }

}
