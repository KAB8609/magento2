<?php
/**
 * Public alias for the application entry point
 *
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */
require __DIR__ . '/../app/bootstrap.php';
Magento_Profiler::start('mage');
$params = $_SERVER;
$params[Mage::PARAM_APP_URIS][Mage_Core_Model_Dir::PUB] = '';
try {
    $entryPoint = new Mage_Core_Model_EntryPoint_Http(new Mage_Core_Model_Config_Primary(BP, $params));
    $entryPoint->processRequest();
} catch (Magento_BootstrapException $e) {
}
Magento_Profiler::stop('mage');
