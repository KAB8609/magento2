<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

require __DIR__ . '/../../../../app/code/Mage/Core/functions.php';
require __DIR__ . '/../../../../app/autoload.php';
Magento_Autoload_IncludePath::addIncludePath(array(
    __DIR__,
    realpath(__DIR__ . '/../testsuite'),
    realpath(__DIR__ . '/../../../../app'),
    realpath(__DIR__ . '/../../../../app/code'),
    realpath(__DIR__ . '/../../../../lib'),
));

define('TESTS_TEMP_DIR', dirname(__DIR__) . DIRECTORY_SEPARATOR . 'tmp');
define('DS', DIRECTORY_SEPARATOR);
if (is_dir(TESTS_TEMP_DIR)) {
    Varien_Io_File::rmdirRecursive(TESTS_TEMP_DIR);
}
mkdir(TESTS_TEMP_DIR);

Mage::setIsSerializable(false);
