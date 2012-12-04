<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento
 * @subpackage  static_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * PHP Code Sniffer tool wrapper
 */
class CodingStandard_Tool_CodeSniffer implements CodingStandard_ToolInterface
{
    /**
     * Ruleset directory
     *
     * @var string
     */
    protected $_rulesetDir;

    /**
     * Report file
     *
     * @var string
     */
    protected $_reportFile;

    /**
     * PHPCS cli tool wrapper
     *
     * @var PHP_CodeSniffer_CLI
     */
    protected $_wrapper;

    /**
     * Constructor
     *
     * @param string $rulesetDir Directory that locates the inspection rules
     * @param string $reportFile Destination file to write inspection report to
     */
    public function __construct($rulesetDir, $reportFile, PHP_CodeSniffer_CLI $toolWrapper)
    {
        $this->_reportFile = $reportFile;
        $this->_rulesetDir = $rulesetDir;
        $this->_wrapper = $toolWrapper;
    }

    /**
     * Whether the tool can be ran on the current environment
     *
     * @return bool
     */
    public function canRun()
    {
        return class_exists('PHP_CodeSniffer_CLI');
    }

    /**
     * Run tool for files cpecified
     *
     * @param array $whiteList Files/directories to be inspected
     * @param array $blackList Files/directories to be excluded from the inspection
     * @param array $extensions Array of alphanumeric strings, for example: 'php', 'xml', 'phtml', 'css'...
     *
     * @return bool
     */
    public function run(array $whiteList, array $blackList = array(), array $extensions = array())
    {
        $whiteList = array_map(function($item) {
            return str_replace('/', DIRECTORY_SEPARATOR, $item);
        }, $whiteList);

        $blackList = array_map(function($item) {
            return preg_quote(str_replace('/', DIRECTORY_SEPARATOR, $item));
        }, $blackList);


        $this->_wrapper->checkRequirements();
        $settings = $this->_wrapper->getDefaults();
        $settings['files'] = $whiteList;
        $settings['standard'] = $this->_rulesetDir;
        $settings['ignored'] = $blackList;
        $settings['extensions'] = $extensions;
        $settings['reportFile'] = $this->_reportFile;
        $settings['warningSeverity'] = 0;
        $settings['reports']['checkstyle'] = null;
        $this->_wrapper->setValues($settings);

        ob_start();
        $result = $this->_wrapper->process();
        ob_end_clean();
        return $result;
    }

}
