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

require_once __DIR__ . '/../../../../app/bootstrap.php';
require_once __DIR__ . '/../../static/testsuite/Utility/Classes.php';

$testsBaseDir = dirname(__DIR__);
$testsTmpDir = "$testsBaseDir/tmp";
$magentoBaseDir = realpath("$testsBaseDir/../../../");

Magento_Autoload_IncludePath::addIncludePath(array(
    "$testsBaseDir/framework",
    "$testsBaseDir/testsuite",
));

if (defined('TESTS_LOCAL_CONFIG_FILE') && TESTS_LOCAL_CONFIG_FILE) {
    $localXmlFile = "$testsBaseDir/" . TESTS_LOCAL_CONFIG_FILE;
    if (!is_file($localXmlFile) && substr($localXmlFile, -5) != '.dist') {
        $localXmlFile .= '.dist';
    }
} else {
    $localXmlFile = "$testsBaseDir/etc/local-mysql.xml";
}

if (defined('TESTS_GLOBAL_CONFIG_FILES') && TESTS_GLOBAL_CONFIG_FILES) {
    $globalEtcFiles = TESTS_GLOBAL_CONFIG_FILES;
} else {
    $globalEtcFiles = "../../../app/etc/*.xml";
}

if (defined('TESTS_MODULE_CONFIG_FILES') && TESTS_MODULE_CONFIG_FILES) {
    $moduleEtcFiles = TESTS_MODULE_CONFIG_FILES;
} else {
    $moduleEtcFiles = "../../../app/etc/modules/*.xml";
}

$isCleanupEnabled = (defined('TESTS_CLEANUP') && TESTS_CLEANUP == 'enabled');

$isDeveloperMode = (defined('TESTS_MAGENTO_DEVELOPER_MODE') && TESTS_MAGENTO_DEVELOPER_MODE == 'enabled');

/* Enable profiler if necessary */
if (defined('TESTS_PROFILER_FILE') && TESTS_PROFILER_FILE) {
    $driver = new Magento_Profiler_Driver_Standard();
    $driver->registerOutput(new Magento_Profiler_Driver_Standard_Output_Csvfile(array(
        'baseDir' => $testsBaseDir,
        'filePath' => TESTS_PROFILER_FILE
    )));
    Magento_Profiler::add($driver);
}

/* Enable profiler with bamboo friendly output format */
if (defined('TESTS_BAMBOO_PROFILER_FILE') && defined('TESTS_BAMBOO_PROFILER_METRICS_FILE')) {
    $driver = new Magento_Profiler_Driver_Standard();
    $driver->registerOutput(new Magento_Test_Profiler_OutputBamboo(array(
        'baseDir' => $testsBaseDir,
        'filePath' => TESTS_BAMBOO_PROFILER_FILE,
        'metrics' => require($testsBaseDir . DIRECTORY_SEPARATOR . TESTS_BAMBOO_PROFILER_METRICS_FILE)
    )));
    Magento_Profiler::add($driver);
}

/** Memory/leak limit stats */
/** @var $memLimit Magento_Test_MemoryLimit */
$memLimit = new Magento_Test_MemoryLimit(
    defined('TESTS_MEM_USAGE_LIMIT') ? TESTS_MEM_USAGE_LIMIT : 0,
    defined('TESTS_MEM_LEAK_LIMIT') ? TESTS_MEM_LEAK_LIMIT : 0,
    new Magento_Test_Helper_Memory(new Magento_Shell)
);
register_shutdown_function(function() use ($memLimit) {
    echo $memLimit->printHeader() . $memLimit->printStats() . PHP_EOL;
});
register_shutdown_function(array($memLimit, 'validateUsage'));

/*
 * Activate custom DocBlock annotations.
 * Note: order of registering (and applying) annotations is important.
 * To allow config fixtures to deal with fixture stores, data fixtures should be processed before config fixtures.
 */
$eventManager = new Magento_Test_EventManager(array(
    new Magento_Test_Workaround_Segfault(),
    new Magento_Test_Workaround_Cleanup_TestCaseProperties(),
    new Magento_Test_Workaround_Cleanup_StaticProperties(),
    new Magento_Test_Annotation_AppIsolation(),
    new Magento_Test_Event_Transaction(new Magento_Test_EventManager(array(
        new Magento_Test_Annotation_DbIsolation(),
        new Magento_Test_Annotation_DataFixture("$testsBaseDir/testsuite"),
    ))),
    new Magento_Test_Annotation_ConfigFixture(),
));
Magento_Test_Event_PhpUnit::setDefaultEventManager($eventManager);
Magento_Test_Event_Magento::setDefaultEventManager($eventManager);

/* Initialize object manager instance */
Mage::initializeObjectManager(null, new Magento_Test_ObjectManager());

/* Bootstrap the application */
Magento_Test_Bootstrap::setInstance(new Magento_Test_Bootstrap(
    $magentoBaseDir,
    $testsBaseDir,
    $localXmlFile,
    $globalEtcFiles,
    $moduleEtcFiles,
    $testsBaseDir . DIRECTORY_SEPARATOR . 'etc/integration-tests-config.xml',
    $testsTmpDir,
    new Magento_Shell(),
    $isCleanupEnabled,
    $isDeveloperMode
));

Utility_Files::init(new Utility_Files($magentoBaseDir));

/* Unset declared global variables to release PHPUnit from maintaining their values between tests */
unset($testsBaseDir, $testsTmpDir, $magentoBaseDir, $localXmlFile, $globalEtcFiles, $moduleEtcFiles, $eventManager);
unset($memLimit);
