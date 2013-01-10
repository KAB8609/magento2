<?php
/**
 * Multi-tenant build deployment script
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

require __DIR__ . '/../../../app/autoload.php';
Magento_Autoload_IncludePath::addIncludePath(array(__DIR__ . '/../../../lib', __DIR__));

$logWriter = new \Zend_Log_Writer_Stream('php://output');
$logWriter->setFormatter(new \Zend_Log_Formatter_Simple('%message%' . PHP_EOL));
$logger = new \Zend_Log($logWriter);
try {
    $params = getopt('', array('meta-dir:', 'deploy-url-pattern:', 'dsn:', 'install::', 'uninstall::', 'cleanup::'));
    if (empty($params['meta-dir'])) {
        throw new Exception('Missing required parameter "meta-dir"');
    }
    $workingDir = realpath(__DIR__ . '/../../..');
    $controller = new \Magento\MultiTenant\Wizard($logger, $params, $workingDir, $params['meta-dir']);
    $controller->execute();
} catch (Exception $e) {
    $logger->log('USAGE:
    php -f deploy.php --
        --meta-dir=<absolute_path> --deploy-url-pattern=<url_pattern>
        --dsn=mysql://<db_user>:<db_password>@<db_host>
        [--install=<tenant_ids>] [--uninstall=<tenant_ids>]
        [--cleanup]' . PHP_EOL . PHP_EOL, Zend_Log::INFO);
    $logger->log((string)$e, Zend_Log::ERR);
    exit(1);
}
