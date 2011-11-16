<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento
 * @subpackage  static_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

$baseDir = realpath(__DIR__ . '/../../../../../');

return array(
    'report_dir' => "{$baseDir}/dev/tests/static/report",
    'white_list' => array(
        "{$baseDir}/_classmap.php",
        "{$baseDir}/app/code/core/Mage/Core/Model/Design.php",
        "{$baseDir}/app/bootstrap.php",
        "{$baseDir}/dev/tests/integration",
        "{$baseDir}/dev/tests/static",
        "{$baseDir}/dev/tests/unit",
        "{$baseDir}/dev/tools",
        "{$baseDir}/lib/Magento/Profiler",
        "{$baseDir}/lib/Magento/Profiler.php",
        "{$baseDir}/lib/Varien/Object.php",
        "{$baseDir}/app/code/core/Mage/Index/Model/Indexer/Abstract.php",
    ),
    'black_list' => array(
        /* Files that intentionally violate the requirements for testing purposes */
        "{$baseDir}/dev/tests/static/testsuite/Php/Exemplar/_files/phpcs/input",
        "{$baseDir}/dev/tests/static/testsuite/Php/Exemplar/_files/phpmd/input",

        // various fixtures, exempt from static code analysis
        "{$baseDir}/dev/tests/integration/framework/tests/unit/testsuite/Magento/Test/TestSuite/_files",
        "{$baseDir}/dev/tests/integration/testsuite/Mage/Core/Block/_files",
        "{$baseDir}/dev/tests/integration/testsuite/Integrity/modular/TemplateFilesTest.php",
        "{$baseDir}/dev/tests/integration/testsuite/Integrity/theme/TemplateFilesTest.php",
        "{$baseDir}/dev/tests/integration/testsuite/Integrity/ClassesTest.php",
        "{$baseDir}/dev/tests/integration/tmp",
    )
);