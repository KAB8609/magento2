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

$baseDir = realpath(__DIR__ . '/../../../../');
define('DS', DIRECTORY_SEPARATOR);
define('BP', $baseDir);
require $baseDir . '/app/autoload.php';
Magento_Autoload_IncludePath::addIncludePath(array(
    __DIR__,
    dirname(__DIR__) . '/testsuite',
    $baseDir . '/lib',
));
Magento_TestFramework_Utility_Files::init(new Magento_TestFramework_Utility_Files($baseDir));

function tool_autoloader($className)
{
    if (strpos($className, 'Magento\\Tools\\') === false) {
        return false;
    }
    $filePath = str_replace('\\', DS, $className);
    $filePath = BP . DS . 'dev' . DS . 'tools' . DS . $filePath . '.php';

    if (file_exists($filePath)) {
        include_once($filePath);
    } else {
        return false;
    }
}
spl_autoload_register('tool_autoloader');
