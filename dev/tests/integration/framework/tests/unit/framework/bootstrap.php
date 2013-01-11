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

$rootDir = realpath(__DIR__ . '/../../../../../../../');
require_once $rootDir . '/app/bootstrap.php';
Magento_Autoload_IncludePath::addIncludePath($rootDir . '/dev/tests/integration/framework');

Mage::initializeObjectManager(null, new Magento_Test_ObjectManager());
