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
 * @subpackage  runner
 * @author      Magento Core Team <core@magentocommerce.com>
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
define('SELENIUM_TESTS_BASEDIR', realpath(__DIR__ . DIRECTORY_SEPARATOR . '..'));
define('SELENIUM_TESTS_SCREENSHOTDIR',
        realpath(SELENIUM_TESTS_BASEDIR . DIRECTORY_SEPARATOR . 'tmp' . DIRECTORY_SEPARATOR . 'screenshots'));

set_include_path(implode(PATH_SEPARATOR, array(
            realpath(SELENIUM_TESTS_BASEDIR . DIRECTORY_SEPARATOR . 'framework'),
            realpath(SELENIUM_TESTS_BASEDIR . DIRECTORY_SEPARATOR . 'testsuite'), //To allow load tests helper files
            get_include_path(),
        )));

require_once 'Mage/Selenium/Autoloader.php';
Mage_Selenium_Autoloader::register();

require_once 'functions.php';

$testsConfig = Mage_Selenium_TestConfiguration::initInstance();

if (defined('SELENIUM_TESTS_INSTALLATION') && SELENIUM_TESTS_INSTALLATION === 'enabled') {
    $installConfigFile = SELENIUM_TESTS_BASEDIR . '/config/install.php';
    $installConfigFile = file_exists($installConfigFile) ? $installConfigFile : "$installConfigFile.dist";
    $applicationHelper = new Mage_Selenium_Helper_Application($testsConfig);
    passthru(
        sprintf(
            'php -f %s -- --magento-dir=%s --config-file=%s',
            escapeshellarg(SELENIUM_TESTS_BASEDIR . '/framework/install.php'),
            escapeshellarg($applicationHelper->getBasePath()),
            escapeshellarg($installConfigFile)
        ),
        $installExitCode
    );
    if ($installExitCode !== 0) {
        exit($installExitCode);
    }
}

/* Unset declared global variables to release PHPUnit from maintaining their values between tests */
unset($testsConfig, $applicationHelper, $installConfigFile, $installExitCode);
