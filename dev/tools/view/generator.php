<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     tools
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Script to pre-deploy Magento - copy all the static view files to public directory,
 * so in production mode the paths and urls can be composed without looking for files on disk.
 */

require __DIR__ . '/../../../app/bootstrap.php';
Magento_Autoload_IncludePath::addIncludePath(__DIR__);

// ----Parse params and run the tool-------------------------
define('USAGE', <<<USAGE
$>./generator.php -- [--source=<source>] [--destination=<destination>] [--dry-run] [-h] [--help]
    Pre-deploy Magento view files to a public directory.
    Additional parameters:
    --source=<source>           Base directory to start searching for copy files. If not specified, then it is
                                calculated according to current file
    --destination=<destination> custom path to copy files to, if not specified, then default one within system is used
    --dry-run                   run through files, but do not copy anything
    -h or --help                print usage

USAGE
);

$options = getopt('h', array('help', 'dry-run', 'source:', 'destination:'));
if (isset($options['h']) || isset($options['help'])) {
    echo USAGE;
    exit(0);
}

$logWriter = new Zend_Log_Writer_Stream('php://output');
$logWriter->setFormatter(new Zend_Log_Formatter_Simple('%message%' . PHP_EOL));
$logger = new Zend_Log($logWriter);

try {
    $config = new Generator_Config(BP, $options);

    $generator = new Generator_CopyRule($config->getSourceDir());
    $copyRules = $generator->getCopyRules();

    $deployment = new Generator_ThemeDeployment(
        $logger,
        __DIR__ . '/config/permitted.txt',
        __DIR__ . '/config/forbidden.txt'
    );
    $deployment->run($copyRules, $config->getDestinationDir(), $config->isDryRun());
} catch (Exception $e) {
    $logger->log('Error: ' . $e->getMessage(), Zend_Log::ERR);
    exit(1);
}
