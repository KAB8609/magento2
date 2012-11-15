<?php
/**
 * Performance framework bootstrap script
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     performance_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

$testsBaseDir = realpath(__DIR__ . '/..');
$magentoBaseDir = realpath($testsBaseDir . '/../../../');

require_once "$magentoBaseDir/app/bootstrap.php";
$loader->addIncludePath("$testsBaseDir/framework");

$bootstrap = new Magento_Performance_Bootstrap($testsBaseDir, $magentoBaseDir);
$bootstrap->cleanupReports();
return $bootstrap->getConfig();
