<?php
/**
 * {license_notice}
 *
 * @category    tests
 * @package     static
 * @subpackage  Legacy
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Tests for obsolete and removed config nodes
 */
class Legacy_ConfigTest extends PHPUnit_Framework_TestCase
{
    /**
     * @param string $file
     * @dataProvider configFileDataProvider
     */
    public function testConfigFile($file)
    {
        $obsoleteNodes = array(
            '/config/global/fieldsets'                 => '',
            '/config/admin/fieldsets'                  => '',
            '/config/global/models/*/deprecatedNode'   => '',
            '/config/global/models/*/entities/*/table' => '',
            '/config/global/models/*/class'            => '',
            '/config/global/helpers/*/class'           => '',
            '/config/global/blocks/*/class'            => '',
            '/config/global/models/*/resourceModel'    => '',
            '/config/adminhtml/menu'                   => 'Move them to adminhtml.xml.',
            '/config/adminhtml/acl'                    => 'Move them to adminhtml.xml.',
            '/config/*/events/core_block_abstract_to_html_after' =>
                'Event has been replaced with "core_layout_render_element"',
            '/config/*/events/catalog_controller_product_delete' => '',
        );
        $xml = simplexml_load_file($file);
        foreach ($obsoleteNodes as $xpath => $suggestion) {
            $this->assertEmpty(
                $xml->xpath($xpath),
                "Nodes identified by XPath '$xpath' are obsolete. $suggestion"
            );
        }
    }

    /**
     * @return array
     */
    public function configFileDataProvider()
    {
        return Utility_Files::init()->getConfigFiles('config.xml');
    }

    /**
     * Check if all localization files are declared in appropriate configuration files
     *
     * @param string $configFile
     * @param array $localeFiles
     * @param string $moduleName
     * @dataProvider modulesDataProvider
     */
    public function testLocaleDeclarations($configFile, array $localeFiles, $moduleName)
    {
        $xml = simplexml_load_file($configFile);
        $errors = array();
        foreach ($localeFiles as $localeFile) {
            $result = $xml->xpath("//translate/modules/{$moduleName}/files[* = \"{$localeFile}\"]");
            if (empty($result)) {
                $errors[] = "'$localeFile' file is not declared in '$moduleName' module";
            }
        }
        $this->assertEmpty($errors, join("\n", $errors));
    }

    /**
     * @return array
     */
    public function modulesDataProvider()
    {
        $data = array();
        $root = Utility_Files::init()->getPathToSource();
        $modulePaths = glob($root . "/app/code/*/*/*", GLOB_ONLYDIR);
        foreach ($modulePaths as $modulePath) {
            $localeFiles = glob($modulePath . "/locale/*/*.csv");
            $configFile = $modulePath . '/etc/config.xml';
            if (empty($localeFiles) || !file_exists($configFile)) {
                continue;
            }
            foreach ($localeFiles as &$file) {
                $file = basename($file);
            }
            $localeFiles = array_unique($localeFiles);

            $modulePath = str_replace($root, '', $modulePath);
            if (preg_match('#^/app/code/[\w_]+/([\w_]+)/([\w_]+)#', $modulePath, $matches)) {
                $module = $matches[1] . '_' . $matches[2];
                $data[$module] = array($configFile, $localeFiles, $module);
            }
        }
        return $data;
    }
}
