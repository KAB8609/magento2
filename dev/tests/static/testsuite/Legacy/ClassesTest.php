<?php
/**
 * {license_notice}
 *
 * @category    tests
 * @package     static
 * @subpackage  Integrity
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Scans source code for references to classes and see if they indeed exist
 */
class Legacy_ClassesTest extends PHPUnit_Framework_TestCase
{
    /**
     * @param string $file
     * @dataProvider Util_Files::getPhpFiles
     */
    public function testPhpCode($file)
    {
        $classes = self::collectPhpCodeClasses(file_get_contents($file));
        $this->_assertNonFactoryName($classes);
    }

    /**
     * Scan contents as PHP-code and find class name occurrences
     *
     * @param string $contents
     * @param array &$classes
     * @return array
     */
    public static function collectPhpCodeClasses($contents, &$classes = array())
    {
        Util_Classes::getAllMatches($contents, '/
            # ::getModel ::getSingleton ::getResourceModel ::getResourceSingleton
            \:\:get(?:Resource)?(?:Model | Singleton)\(\s*[\'"]([^\'"]+)[\'"]\s*[\),]

            # addBlock createBlock getBlockClassName getBlockSingleton
            | (?:addBlock | createBlock | getBlockClassName | getBlockSingleton)\(\s*[\'"]([^\'"]+)[\'"]\s*[\),]

            # Mage::helper ->helper
            | (?:Mage\:\:|\->)helper\(\s*[\'"]([^\'"]+)[\'"]\s*\)

            # various methods, first argument
            | \->(?:initReport | setDataHelperName | setEntityModelClass | _?initLayoutMessages
                | setAttributeModel | setBackendModel | setFrontendModel | setSourceModel | setModel
            )\(\s*[\'"]([^\'"]+)[\'"]\s*[\),]

            # various methods, second argument
            | \->add(?:ProductConfigurationHelper | OptionsRenderCfg)\(.+,\s*[\'"]([^\'"]+)[\'"]\s*[\),]

            # models in install or setup
            | [\'"](?:resource_model | attribute_model | entity_model | entity_attribute_collection
                | source | backend | frontend | input_renderer | frontend_input_renderer
            )[\'"]\s*=>\s*[\'"]([^\'"]+)[\'"]

            # misc
            | function\s_getCollectionClass\(\)\s+{\s+return\s+[\'"]([a-z\d_\/]+)[\'"]
            | (?:_parentResourceModelName | _checkoutType | _apiType)\s*=\s*\'([a-z\d_\/]+)\'

            /Uix',
            $classes
        );

        // check ->_init | parent::_init
        $skipForInit = implode('|',
            array('id', '[\w\d_]+_id', 'pk', 'code', 'status', 'serial_number', 'entity_pk_value', 'currency_code')
        );
        Util_Classes::getAllMatches($contents, '/
            (?:parent\:\: | \->)_init\(\s*[\'"]([^\'"]+)[\'"]\s*\)
            | (?:parent\:\: | \->)_init\(\s*[\'"]([^\'"]+)[\'"]\s*,\s*[\'"]((?!(' . $skipForInit . '))[^\'"]+)[\'"]\s*\)
            /Uix',
            $classes
        );
        return $classes;
    }

    /**
     * @param string $path
     * @dataProvider configFileDataProvider
     */
    public function testConfiguration($path)
    {
        $xml = simplexml_load_file($path);

        $classes = Util_Classes::collectClassesInConfig($xml);
        $this->_assertNonFactoryName($classes);

        $modules = Util_Classes::getXmlAttributeValues($xml, '//@module', 'module');
        $this->_assertNonFactoryName(array_unique($modules));
    }

    /**
     * @return array
     */
    public function configFileDataProvider()
    {
        return Util_Files::getConfigFiles();
    }

    /**
     * @param string $path
     * @dataProvider layoutFileDataProvider
     */
    public function testLayouts($path)
    {
        $xml = simplexml_load_file($path);
        $classes = Util_Classes::collectLayoutClasses($xml);
        foreach (Util_Classes::getXmlAttributeValues($xml, '/layout//@helper', 'helper') as $class) {
            $classes[] = Util_Classes::getCallbackClass($class);
        }
        $classes = array_merge($classes, Util_Classes::getXmlAttributeValues($xml, '/layout//@module', 'module'));
        $this->_assertNonFactoryName(array_unique($classes));
    }

    /**
     * @return array
     */
    public function layoutFileDataProvider()
    {
        return Util_Files::getLayoutFiles();
    }

    /**
     * Check whether specified classes or module names correspond to a file according PSR-0 standard
     *
     * Suppressing "unused variable" because of the "catch" block
     *
     * @param array $names
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    protected function _assertNonFactoryName($names)
    {
        if (!$names) {
            return;
        }
        $factoryNames = array();
        foreach ($names as $name) {
            try {
                $this->assertFalse(false === strpos($name, '_'));
                $this->assertRegExp('/^([A-Z][A-Za-z\d_]+)+$/', $name);
            } catch (PHPUnit_Framework_AssertionFailedError $e) {
                $factoryNames[] = $name;
            }
        }
        if ($factoryNames) {
            $this->fail('Obsolete factory name(s) detected:' . "\n" . implode("\n", $factoryNames));
        }
    }
}
