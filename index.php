<?php
/**
 * Application entry point
 *
 * Example - run a particular store or website:
 * --------------------------------------------
 * $params = $_SERVER;
 * $params['MAGE_RUN_CODE'] = 'website2';
 * $params['MAGE_RUN_TYPE'] = 'website';
 * ...
 * $entryPoint = new \Magento\Core\Model\EntryPoint\Http(new \Magento\Core\Model\Config\Primary(BP, $params));
 * --------------------------------------------
 *
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */
require __DIR__ . '/app/bootstrap.php';

\Magento\Profiler::start('magento');
$entryPoint = new \Magento\Core\Model\EntryPoint\Http(BP, $_SERVER);
$entryPoint->processRequest();
\Magento\Profiler::stop('magento');
