<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Shell
 * @copyright   {copyright}
 * @license     {license_link}
 */

require_once __DIR__ . '/../../app/bootstrap.php';
$params = array(
    Mage::PARAM_RUN_CODE => 'admin',
    Mage::PARAM_RUN_TYPE => 'store',
);

$entryPoint = new Magento_Index_Model_EntryPoint_Shell(
    basename(__FILE__),
    new Magento_Index_Model_EntryPoint_Shell_ErrorHandler(),
    new Magento_Core_Model_Config_Primary(BP, $params)
);
$entryPoint->processRequest();
