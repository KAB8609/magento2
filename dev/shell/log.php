<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Shell
 * @copyright   {copyright}
 * @license     {license_link}
 */

require_once __DIR__ . '/../../app/bootstrap.php';
$params = array(
    Mage::PARAM_RUN_CODE => 'admin',
    Mage::PARAM_RUN_TYPE => 'store',
);
$entryPoint = new Magento_Log_Model_EntryPoint_Shell(
    new Magento_Core_Model_Config_Primary(BP, $params),
    basename(__FILE__)
);
$entryPoint->processRequest();
