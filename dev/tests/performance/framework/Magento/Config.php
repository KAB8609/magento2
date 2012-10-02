<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     performance_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Configuration of performance tests
 */
class Magento_Config
{
    /**
     * Default value for configuration of benchmarking executable file path
     */
    const DEFAULT_JMETER_JAR_FILE = 'ApacheJMeter.jar';

    /**
     * @var string
     */
    protected $_applicationUrlHost;

    /**
     * @var string
     */
    protected $_applicationUrlPath;

    /**
     * @var array
     */
    protected $_adminOptions = array();

    /**
     * @var string
     */
    protected $_reportDir;

    /**
     * @var string
     */
    protected $_jMeterPath;

    /**
     * @var array
     */
    protected $_installOptions = array();

    /**
     * @var array
     */
    protected $_fixtureFiles = array();

    /**
     * @var array
     */
    protected $_scenarios = array();

    /**
     * Constructor
     *
     * @param array $configData
     * @param string $baseDir
     * @throws InvalidArgumentException
     * @throws Magento_Exception
     */
    public function __construct(array $configData, $baseDir)
    {
        $this->_validateData($configData);
        if (!is_dir($baseDir)) {
            throw new Magento_Exception("Base directory '$baseDir' does not exist.");
        }
        $this->_reportDir = $baseDir . DIRECTORY_SEPARATOR . $configData['report_dir'];

        $applicationOptions = $configData['application'];
        $this->_applicationUrlHost = $applicationOptions['url_host'];
        $this->_applicationUrlPath = $applicationOptions['url_path'];
        $this->_adminOptions = $applicationOptions['admin'];

        if (isset($applicationOptions['installation'])) {
            $installConfig = $applicationOptions['installation'];
            $this->_installOptions = $installConfig['options'];
            if (isset($installConfig['fixture_files'])) {
                if (!is_array($installConfig['fixture_files'])) {
                    throw new InvalidArgumentException(
                        "'application' => 'installation' => 'fixture_files' option must be array"
                    );
                }
                $this->_fixtureFiles = array();
                foreach ($installConfig['fixture_files'] as $fixtureName) {
                    $fixtureFile = $baseDir . DIRECTORY_SEPARATOR . $fixtureName;
                    if (!file_exists($fixtureFile)) {
                        throw new Magento_Exception("Fixture '$fixtureName' doesn't exist in $baseDir");
                    }
                    $this->_fixtureFiles[] = $fixtureFile;
                }
            }
        }

        if (!empty($configData['scenario']['jmeter_jar_file'])) {
            $this->_jMeterPath = $configData['scenario']['jmeter_jar_file'];
        } else {
            $this->_jMeterPath = getenv('jmeter_jar_file') ?: self::DEFAULT_JMETER_JAR_FILE;
        }

        $this->_expandScenarios($configData['scenario'], $baseDir);
    }

    /**
     * Expands scenario options and file paths glob to a list of scenarios
     * @param array $scenarios
     * @param string $baseDir
     * @throws InvalidArgumentException
     * @throws Magento_Exception
     */
    protected function _expandScenarios($scenarios, $baseDir)
    {
        $scenarioCommonConfig = array(
            'arguments' => array(),
            'settings' => array(),
        );
        if (!empty($scenarios['common_config'])) {
            $scenarioCommonConfig = array_merge($scenarioCommonConfig, $scenarios['common_config']);
        }

        if (isset($scenarios['scenarios'])) {
            if (!is_array($scenarios['scenarios'])) {
                throw new InvalidArgumentException("'scenario' => 'scenarios' option must be array");
            }
            foreach ($scenarios['scenarios'] as $scenarioName => $scenarioConfig) {
                $scenarioFile = $baseDir . DIRECTORY_SEPARATOR . $scenarioName;
                if (!file_exists($scenarioFile)) {
                    throw new Magento_Exception("Scenario '$scenarioName' doesn't exist in $baseDir");
                }

                foreach ($scenarioCommonConfig as $configKey => $config) {
                    if (!empty($scenarioConfig[$configKey])) {
                        $config = array_merge($config, $scenarioConfig[$configKey]);
                    }
                    if (!empty($config)) {
                        $this->_scenarios[$scenarioFile][$configKey] = $config;
                    }
                }
            }
        }
    }

    /**
     * Validate high-level configuration structure
     *
     * @param array $configData
     * @throws Magento_Exception
     */
    protected function _validateData(array $configData)
    {
        // Validate 1st-level options data
        $requiredKeys = array('application', 'scenario', 'report_dir');
        foreach ($requiredKeys as $requiredKeyName) {
            if (empty($configData[$requiredKeyName])) {
                throw new Magento_Exception("Configuration array must define '$requiredKeyName' key.");
            }
        }

        // Validate admin options data
        $requiredAdminKeys = array('frontname', 'username', 'password');
        foreach ($requiredAdminKeys as $requiredKeyName) {
            if (empty($configData['application']['admin'][$requiredKeyName])) {
                throw new Magento_Exception("Admin options array must define '$requiredKeyName' key.");
            }
        }
    }

    /**
     * Retrieve application URL host component
     *
     * @return string
     */
    public function getApplicationUrlHost()
    {
        return $this->_applicationUrlHost;
    }

    /**
     * Retrieve application URL path component
     *
     * @return string
     */
    public function getApplicationUrlPath()
    {
        return $this->_applicationUrlPath;
    }

    /**
     * Retrieve admin options - backend path and admin user credentials
     *
     * @return array
     */
    public function getAdminOptions()
    {
        return $this->_adminOptions;
    }

    /**
     * Retrieve application installation options
     *
     * @return array
     */
    public function getInstallOptions()
    {
        return $this->_installOptions;
    }

    /**
     * Retrieve scenario files and their configuration as specified in the config
     *
     * @return array
     */
    public function getScenarios()
    {
        return $this->_scenarios;
    }

    /**
     * Retrieve fixture script files
     *
     * @return array
     */
    public function getFixtureFiles()
    {
        return $this->_fixtureFiles;
    }

    /**
     * Retrieve reports directory
     *
     * @return string
     */
    public function getReportDir()
    {
        return $this->_reportDir;
    }

    /**
     * Retrieves path to JMeter java file
     *
     * @return string
     */
    public function getJMeterPath()
    {
        return $this->_jMeterPath;
    }
}
