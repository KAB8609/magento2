<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
require_once __DIR__ . '/../../app/autoload.php';
define('BP', __DIR__ . '/../..');
define('DS', DIRECTORY_SEPARATOR);
\Magento\Autoload\IncludePath::addIncludePath(array(
    BP . DS . 'app' . DS . 'code',
    BP . DS . 'lib',
));

function tool_autoloader($className)
{
    if (strpos($className, 'Magento\\Tools\\') === false) {
        return false;
    }
    $filePath = str_replace('\\', '/', $className);
    $filePath = __DIR__ . '/' . $filePath . '.php';
    if (file_exists($filePath)) {
        include($filePath);
    } else {
        return false;
    }
}
spl_autoload_register('tool_autoloader');
