<?php
/**
 * SaaS "Application entry point", required by "SaaS entry point"
 *
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * Both "SaaS entry point" and this "Application entry point" have a convention:
 * API consists of one and only one array argument.
 * Underlying implementation of the Application entry point may differ in future versions due to changes
 * in Application itself, but API should remain the same
 *
 * @param array $tenantData
 * @throws LogicException
 */
return function (array $tenantData)
{
    $rootDir = dirname(__DIR__);
    require $rootDir . '/app/bootstrap.php';

    $config = new Saas_Saas_Model_Tenant_Config($rootDir, $tenantData);

    //Process robots.txt request
    if ($_SERVER['REQUEST_URI'] == '/robots.txt') {
        $robotsFile = $config->getMediaDirFile('robots.txt');
        if (!file_exists($robotsFile)) {
            $robotsFile = __DIR__ . '/robots.txt';
        }
        readfile($robotsFile);
        return;
    }

    $entryPoint = new Mage_Core_Model_EntryPoint_Http($rootDir, $config->getApplicationParams());
    $entryPoint->processRequest();
};
