<?php
/**
 * Automated replacement of factory names into real ones
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     tools
 * @copyright   {copyright}
 * @license     {license_link}
 */
require realpath(dirname(dirname(dirname(__DIR__)))) . '/dev/tests/static/framework/bootstrap.php';

// PHP code
foreach (Utility_Files::init()->getPhpFiles(true, true, true, false) as $file) {
    $content = file_get_contents($file);
    $classes = Legacy_ClassesTest::collectPhpCodeClasses($content);
    $factoryNames = array_filter($classes, 'isFactoryName');
    if (!$factoryNames) {
        continue;
    }
    $search = array();
    $replace = array();
    foreach ($factoryNames as $factoryName) {
        list($module, $name) = getModuleName($factoryName);
        addReplace($factoryName, $module, $name, '::getModel(\'%s\'', '_Model_', $search, $replace);
        addReplace($factoryName, $module, $name, '::getSingleton(\'%s\'', '_Model_', $search, $replace);
        addReplace($factoryName, $module, $name, '::getResourceModel(\'%s\'', '_Model_Resource_', $search, $replace);
        addReplace($factoryName, $module, $name, "::getResourceSingleton('%s'", '_Model_Resource_', $search, $replace);
        addReplace($factoryName, $module, $name, 'addBlock(\'%s\'', '_Block_', $search, $replace);
        addReplace($factoryName, $module, $name, 'createBlock(\'%s\'', '_Block_', $search, $replace);
        addReplace($factoryName, $module, $name, 'getBlockClassName(\'%s\'', '_Block_', $search, $replace);
        addReplace($factoryName, $module, $name, 'getBlockSingleton(\'%s\'', '_Block_', $search, $replace);
        addReplace($factoryName, $module, $name, 'helper(\'%s\'', '_Helper_', $search, $replace);
    }
    $newContent = str_replace($search, $replace, $content);
    if ($newContent != $content) {
        echo "{$file}\n";
        print_r($factoryNames);
        file_put_contents($file, $newContent);
    }
}

// layouts
foreach (Utility_Files::init()->getLayoutFiles(array(), false) as $file) {
    $xml = simplexml_load_file($file);
    $classes = Utility_Classes::collectLayoutClasses($xml);
    $factoryNames = array_filter($classes, 'isFactoryName');
    if (!$factoryNames) {
        continue;
    }
    $search = array();
    $replace = array();
    foreach ($factoryNames as $factoryName) {
        list($module, $name) = getModuleName($factoryName);
        addReplace($factoryName, $module, $name, 'type="%s"', '_Block_', $search, $replace);
    }
    $content = file_get_contents($file);
    $newContent = str_replace($search, $replace, $content);
    if ($newContent != $content) {
        echo "{$file}\n";
        print_r($factoryNames);
        file_put_contents($file, $newContent);
    }
}

/**
 * Whether the given class name is a factory name
 *
 * @param string $class
 * @return bool
 */
function isFactoryName($class)
{
    return false !== strpos($class, '/') || preg_match('/^[a-z\d]+(_[A-Za-z\d]+)?$/', $class);
}

/**
 * Transform factory name into a pair of module and name
 *
 * @param string $factoryName
 * @return array
 */
function getModuleName($factoryName)
{
    if (false !== strpos($factoryName, '/')) {
        list($module, $name) = explode('/', $factoryName);
    } else {
        $module = $factoryName;
        $name = false;
    }
    if (false === strpos($module, '_')) {
        $module = "Mage_{$module}";
    }
    return array($module, $name);
}

/**
 * Add search/replacements of factory name into real name based on a specified "sprintf()" pattern
 *
 * @param string $factoryName
 * @param string $module
 * @param string $name
 * @param string $pattern
 * @param string $suffix
 * @param array &$search
 * @param array &$replace
 */
function addReplace($factoryName, $module, $name, $pattern, $suffix, &$search, &$replace)
{
    if (empty($name)) {
        if ('_Helper_' !== $suffix) {
            return;
        }
        $name = 'data';
    }
    $realName = implode('_', array_map('ucfirst', explode('_', $module . $suffix . $name)));
    $search[] = sprintf($pattern, "{$factoryName}");
    $replace[] = sprintf($pattern, "{$realName}");
}
