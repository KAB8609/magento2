<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */
require __DIR__ . '/../bootstrap.php';
$baseDirectory = realpath(BP);
use Magento\Tools\I18n\Code\Dictionary;

try {
    $options = new Zend_Console_Getopt(array(
        'directory|bd=s' => 'Absolute path to base directory, Magento code base by default',
        'with_context|wc=s' => 'Whether to infuse output with additional meta-information, by default "yes"',
        'output|o=s' => 'Path to output file name, by default output the results into standard output stream',
    ));
    $options->parse();
    $specificDirectory = $options->getOption('directory') ? : null;
    $outputFilename = $options->getOption('output') ? : null;
    $withContext = !in_array($options->getOption('with_context'), array('n', 'no', 'N', 'No', 'NO'));

    $options = array(
        'php' => array(
            'paths' => $specificDirectory ? array($specificDirectory) : array(
                $baseDirectory . '/app/code/',
                $baseDirectory . '/app/design/',
            ),
            'fileMask' => '/\.(php|phtml)$/',
        ),
        'js' => array(
            'paths' => $specificDirectory ? array($specificDirectory) : array(
                $baseDirectory . '/app/code/',
                $baseDirectory . '/app/design/',
                $baseDirectory . '/pub/lib/mage/',
                $baseDirectory . '/pub/lib/varien/',
            ),
            'fileMask' => '/\.(js|phtml)$/',
        ),
        'xml' => array(
            'paths' => $specificDirectory ? array($specificDirectory) : array(
                $baseDirectory . '/app/code/',
                $baseDirectory . '/app/design/',
            ),
            'fileMask' => '/\.xml$/',
        ),
        'outputFilename' => $outputFilename,
    );

    $generatorFactory = new Dictionary\Generator\Factory();
    $generatorFactory->create($options)->generate($withContext);

} catch (Zend_Console_Getopt_Exception $e) {
    echo $e->getUsageMessage();
    exit(1);
} catch (Exception $e) {
    fwrite(STDERR, "Translate phrase generator failed with exception:\n" . $e->getMessage() . "\n");
    exit(1);
}
