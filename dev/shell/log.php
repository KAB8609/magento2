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
    'entryPoint' => basename(__FILE__),
);
$entryPoint = new Mage_Log_Model_EntryPoint_Shell(BP, $params);
$entryPoint->processRequest();
