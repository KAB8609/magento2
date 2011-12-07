<?php
/**
 * {license_notice}
 *
 * @category   Magento
 * @copyright  {copyright}
 * @license    {license_link}
 */

/* PHP version validation */
if (version_compare(phpversion(), '5.3.0', '<') === true) {
    if (PHP_SAPI == 'cli') {
        echo 'Magento supports PHP 5.3.0 or newer. Please read http://www.magento.com/install.';
    } else {
        echo <<<HTML
<div style="font:12px/1.35em arial, helvetica, sans-serif;">
    <div style="margin:0 0 25px 0; border-bottom:1px solid #ccc;">
        <h3 style="margin:0;font-size:1.7em;font-weight:normal;text-transform:none;text-align:left;color:#2f2f2f;">
        Whoops, it looks like you have an invalid PHP version.</h3>
    </div>
    <p>Magento supports PHP 5.3.0 or newer.
    <a href="http://www.magento.com/install" target="">Find out</a>
    how to install Magento using PHP-CGI as a work-around.
    </p>
</div>
HTML;
    }
    exit;
}

/**
 * Constants definition
 */
define('DS', DIRECTORY_SEPARATOR);
define('BP', dirname(__DIR__));

if (file_exists(BP . '/maintenance.flag')) {
    if (PHP_SAPI == 'cli') {
        echo 'Service temporarily unavailable due to maintenance downtime.';
    } else {
        include_once BP . '/pub/errors/503.php';
    }
    exit;
}

/**
 * Environment initialization
 */
error_reporting(E_ALL | E_STRICT);
#ini_set('display_errors', 1);
umask(0);

/**
 * Require necessary files
 */
require_once BP . '/lib/Magento/Autoload.php';
require_once BP . '/app/code/core/Mage/Core/functions.php';
require_once BP . '/app/Mage.php';

if (isset($_SERVER['MAGE_IS_DEVELOPER_MODE'])) {
    Mage::setIsDeveloperMode(true);
}
Mage::register('original_include_path', get_include_path());

$paths[] = BP . DS . 'app' . DS . 'code' . DS . 'local';
$paths[] = BP . DS . 'app' . DS . 'code' . DS . 'community';
$paths[] = BP . DS . 'app' . DS . 'code' . DS . 'core';
$paths[] = BP . DS . 'lib';

Magento_Autoload::getInstance()->addIncludePath($paths)
    ->addFilesMap(BP . '/_classmap.php');

#Magento_Profiler::enable();
#Magento_Profiler::registerOutput(new Magento_Profiler_Output_Html());
#Magento_Profiler::registerOutput(new Magento_Profiler_Output_Firebug());
#Magento_Profiler::registerOutput(new Magento_Profiler_Output_Csvfile(__DIR__ . '/var/log/profiler.csv'));
