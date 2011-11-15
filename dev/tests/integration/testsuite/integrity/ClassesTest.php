<?php
/**
 * Integrity test used to check, that all classes, written as direct class names in code, really exist
 *
 * {license_notice}
 *
 * @category    tests
 * @package     integration
 * @subpackage  integrity
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Integrity_ClassesTest extends PHPUnit_Framework_TestCase
{
    /**
     * List of methods in this class, that are designed to check file content.
     * Filled automatically via reflection.
     *
     * @var array
     */
    protected $_visitorMethods = null;

    /**
     * @param string $className
     * @dataProvider classExistsDataProvider
     */
    public function testClassExists($className)
    {
        if ($className == 'Mage_Catalog_Model_Resource_Convert') {
            $this->markTestSkipped('Bug MAGE-4763');
        }

        if ($className == 'Mage_Install_Model_Installer_Pear') {
            $this->markTestSkipped('Bug MAGETWO-602');
        }

        $skippedClasses = array(
            'Enterprise_Staging_Block_Staging_Merge_Settings',
            'Enterprise_Giftregistry_Block_Customer_View',
            'Enterprise_Permissions_Block_System_Config_Switcher',
            'Enterprise_Permissions_Block_Store_Switcher',
            'Mage_Adminhtml_Block_Api_Tab_Useredit',
            'Mage_Adminhtml_Block_Api_Grid_User',
            'Mage_Adminhtml_Block_Sales_Grid',
            'Mage_Adminhtml_Block_Tag_Grid_Column_Renderer_Tags',
            'Mage_Adminhtml_Block_Tag_Tab_All',
            'Mage_Adminhtml_Block_Customer_Config',
            'Mage_Adminhtml_Block_Customer_Config_Tabs',
            'Mage_Adminhtml_Block_Alert_Template_Preview',
            'Mage_Adminhtml_Block_Promo_Quote_Edit_Tab_Product',
            'Mage_Adminhtml_Block_Sales_Order_Totals_Subtotal',
            'Mage_Core_Block_Profiler',
            'Mage_Core_Block_List',
            'Mage_Catalog_Block_Seo_Searchterm',
            'Mage_Giftcard_Block_Catalog_Product_Price_Giftcard',
            'Mage_XmlConnect_Block_Adminhtml_Mobile_Helper_Image',

            'emph',
            'Enterprise_Enterprise_Helper_Data',
        );
        if (in_array($className, $skippedClasses)) {
            $this->markTestSkipped('Task MAGETWO-586');
        }

        $this->assertTrue(
            Magento_Autoload::getInstance()->classExists($className),
            'Class ' . $className . ' does not exist'
        );
    }

    /**
     * @return array
     */
    public function classExistsDataProvider()
    {
        $classNames = $this->_findAllClassNames();

        $result = array();
        foreach ($classNames as $className) {
            $result[] = array($className);
        }
        return $result;
    }

    /**
     * Gathers all class name definitions in Magento
     *
     * @return array
     */
    protected function _findAllClassNames()
    {
        $directory  = new RecursiveDirectoryIterator(Mage::getRoot());
        $iterator = new RecursiveIteratorIterator($directory);
        $regexIterator = new RegexIterator($iterator, '/(\.php|\.phtml|\.xml)$/');

        $result = array();
        foreach ($regexIterator as $fileInfo) {
            $classNames = $this->_findClassNamesInFile($fileInfo);
            $result = array_merge($result, $classNames);
        }
        return $result;
    }

    /**
     * Gathers all class name definitions in a class
     *
     * @param SplFileInfo $fileInfo
     * @return array
     */
    protected function _findClassNamesInFile($fileInfo)
    {
        $content = file_get_contents((string)$fileInfo);

        $result = array();
        $visitorMethods = $this->_getVisitorMethods();
        foreach ($visitorMethods as $method) {
            $classNames = $this->$method($fileInfo, $content);
            if (!$classNames) {
                continue;
            }
            $classNames = array_combine($classNames, $classNames); // Thus array_merge will not have duplicates
            $result = array_merge($result, $classNames);
        }

        return $result;
    }

    /**
     * Returns all methods in this class, that are designed to visit the file content.
     * Protected methods starting with '_visit' are considered to be visitor methods.
     *
     * @return array
     */
    protected function _getVisitorMethods()
    {
        if ($this->_visitorMethods === null) {
            $this->_visitorMethods = array();
            $reflection = new ReflectionClass($this);
            foreach ($reflection->getMethods(ReflectionMethod::IS_PROTECTED) as $method) {
                if (substr($method->name, 0, 6) == '_visit') {
                    $this->_visitorMethods[] = $method->name;
                }
            }
        }

        return $this->_visitorMethods;
    }

    /**
     * Checks whether file path has required extension
     *
     * @param SplFileInfo $fileInfo
     * @param string|array $extensions
     * @return bool
     */
    protected function _fileHasExtensions($fileInfo, $extensions)
    {
        if (is_string($extensions)) {
            $extensions = array($extensions);
        }

        $fileExtension = pathinfo($fileInfo->getBasename(), PATHINFO_EXTENSION);
        $key = array_search($fileExtension, $extensions);
        return ($key !== false);
    }

    /**
     * Finds all usages of function $funcName in $content, where it has only one constant string argument.
     * Returns array of all these arguments.
     *
     * @param string $funcName
     * @param string $content
     * @return array
     */
    protected function _getFuncStringArguments($funcName, $content)
    {
        $result = array();
        $matched = preg_match_all('/' . $funcName . '\([\'"]([^\'"\$]+)[\'"]\)/', $content, $matches);
        if ($matched) {
            $result = $matches[1];
        }
        return $result;
    }

    /**
     * Finds usage of Mage::getResourceModel('Class_Name'), Mage::getResourceSingleton('Class_Name')
     *
     * @param SplFileInfo $fileInfo
     * @param string $content
     * @return array
     */
    protected function _visitMageGetResource($fileInfo, $content)
    {
        if (!$this->_fileHasExtensions($fileInfo, array('php', 'phtml'))) {
            return array();
        }

        $funcNames = array('Mage::getResourceModel', 'Mage::getResourceSingleton');
        $result = array();
        foreach ($funcNames as $funcName) {
            $classNames = $this->_getFuncStringArguments($funcName, $content);
            $result = array_merge($result, $classNames);
        }

        return $result;
    }

    /**
     * Finds usage of Mage::getResourceHelper('Module_Name')
     *
     * @param SplFileInfo $fileInfo
     * @param string $content
     * @return array
     */
    protected function _visitMageGetResourceHelper($fileInfo, $content)
    {
        if (!$this->_fileHasExtensions($fileInfo, array('php', 'phtml'))) {
            return array();
        }

        $modules = $this->_getFuncStringArguments('Mage::getResourceHelper', $content);

        $result = array();
        $dbSuffixes = array('Mysql4', 'Mssql', 'Oracle');
        foreach ($modules as $module) {
            foreach ($dbSuffixes as $dbSuffix) {
                $result[] = $module . '_Model_Resource_Helper_' . $dbSuffix;
            }
        }

        return $result;
    }

    /**
     * Finds usage of staging resource adapters across xml configs
     *
     * @param SplFileInfo $fileInfo
     * @param string $content
     * @return array
     */
    protected function _visitStagingResourceAdapters($fileInfo, $content)
    {
        if ($fileInfo->getBasename() != 'config.xml') {
            return array();
        }

        $xml = new SimpleXMLElement($content);
        $resourceAdapters = $xml->xpath('/config/global/enterprise/staging/staging_items/*/resource_adapter');
        if (!$resourceAdapters) {
            return array();
        }

        $result = array();
        foreach ($resourceAdapters as $resourceAdapter) {
            $result[] = (string) $resourceAdapter;
        }

        return $result;
    }

    /**
     * Finds usage of helpers in php files through helper function
     *
     * @param SplFileInfo $fileInfo
     * @param string $content
     * @return array
     */
    protected function _visitHelperFunctionCalls($fileInfo, $content)
    {
        if (!$this->_fileHasExtensions($fileInfo, array('php', 'phtml'))) {
            return array();
        }

        $result = array();
        $modules = $this->_getFuncStringArguments('helper', $content);
        $result = array_merge($result, $modules);

        $modules = $this->_getFuncStringArguments('setDataHelperName', $content);
        $result = array_merge($result, $modules);

        $combine = array();
        $matched = preg_match_all('/addProductConfigurationHelper\(.*,[\'"](.*)[\'"]\)/', $content, $matches);
        if ($matched) {
            $combine = array_merge($combine, $matches[1]);
        }

        $matched = preg_match_all('/addOptionsRenderCfg\(.*,[\'"](.*)[\'"],.*\)/', $content, $matches);
        if ($matched) {
            $combine = array_merge($combine, $matches[1]);
        }

        foreach ($combine as $match) {
            if (strpos($match, '$') === FALSE) {
                continue;
            }
            $result[] = $match;
        }

        return $result;
    }

    /**
     * Finds usage of helpers in layout files through attributes and layouts
     *
     * @param SplFileInfo $fileInfo
     * @param string $content
     * @return array
     */
    protected function _visitXmlAttributeDefinitions($fileInfo, $content)
    {
        if (!$this->_fileHasExtensions($fileInfo, array('xml'))) {
            return array();
        }

        $result = array();
        $matched = preg_match_all('/helper="(.*)::.*"/Us', $content, $matches);
        if ($matched) {
            $result = array_merge($result, $matches[1]);
        }

        $matched = preg_match_all('/module="(.*)"/Us', $content, $matches);
        if ($matched) {
            foreach ($matches[1] as $module) {
                $result[] = $module . '_Helper_Data';
            }
        }

        $matched = preg_match_all('/method="addProductConfigurationHelper"><type>.*<\/type><name>(.*)<\/name>/',
            $content, $matches);
        if ($matched) {
            $result = array_merge($result, $matches[1]);
        }

        $matched = preg_match_all('/method="addOptionsRenderCfg"><type>.*<\/type><helper>(.*)<\/helper>/',
            $content, $matches);
        if ($matched) {
            $result = array_merge($result, $matches[1]);
        }

        return $result;
    }

    /**
     * Finds methods that return collection names for grid blocks
     *
     * @param SplFileInfo $fileInfo
     * @param string $content
     * @return array
     */
    protected function _visitCollectionNamesInGridBlocks($fileInfo, $content)
    {
        if (!$this->_fileHasExtensions($fileInfo, 'php')) {
            return array();
        }

        $regexp = ' _getCollectionClass\(\)\s+';
        $regexp .= '{\s+';
        $regexp .= 'return\s+[\'"]([a-zA-Z_\/]+)[\'"];';
        $matched = preg_match_all('/' . $regexp . '/', $content, $matches);

        if (!$matched) {
            return array();
        }
        return $matches[1];
    }

    /**
     * Finds usage of initReport('Class_Name')
     *
     * @param SplFileInfo $fileInfo
     * @param string $content
     * @return array
     */
    protected function _visitInitReport($fileInfo, $content)
    {
        if (!$this->_fileHasExtensions($fileInfo, array('php', 'phtml'))) {
            return array();
        }

        return $this->_getFuncStringArguments('->initReport', $content);
    }

    /**
     * Finds usage of "'resource_model' => 'Class_Name'"
     *
     * @param SplFileInfo $fileInfo
     * @param string $content
     * @return array
     */
    protected function _visitResourceClassesAsArrayEntries($fileInfo, $content)
    {
        if (!$this->_fileHasExtensions($fileInfo, array('php'))) {
            return array();
        }

        $regexp = "/'resource_model'" . '\s*=>\s*' . "'([A-Za-z_]+)'/";
        $matched = preg_match_all($regexp, $content, $matches);

        if (!$matched) {
            return array();
        }
        return $matches[1];
    }

    /**
     * Finds usage of block class names in tags or attribute values of layout files
     *
     * @param SplFileInfo $fileInfo
     * @param string $content
     * @return array
     */
    protected function _visitBlockClassesInLayoutFiles($fileInfo, $content)
    {
        if (!$this->_fileHasExtensions($fileInfo, 'xml')) {
            return array();
        }

        $matches = array();
        if (!preg_match('/\n\s*<([a-z]+)/', $content, $matches) || $matches[1] != 'layout') {
            return array();
        }

        if (preg_match_all('/[>"]{1}([A-Z]{1}\w+_Block_\w+[a-zA-Z0-9]{1})[>"]{1}/', $content, $matches)) {
            return array_unique($matches[1]);
        }
        return array();
    }

    /**
     * Finds usage of block names in tags values of layout files
     *
     * @param SplFileInfo $fileInfo
     * @param string $content
     * @return array
     */
    protected function _visitBlockNamesInLayoutFiles($fileInfo, $content)
    {
        if (!$this->_fileHasExtensions($fileInfo, 'xml')) {
            return array();
        }

        $matches = array();
        if (!preg_match('/\n\s*<([a-z]+)/', $content, $matches) || $matches[1] != 'layout') {
            return array();
        }

        $classes = array();

        $tags = "attributeType|name|content|render|admin_renderer|block|renderer_block|renderer";
        $regexp = "/<(?:" . $tags . ")>([a-zA-Z0-9_]+\/[a-zA-Z0-9_]+)<\/(?:" . $tags . ")>/";
        if (preg_match_all($regexp, $content, $matches)) {
            $classes = $matches[1];
        }

        if (preg_match_all("/<block[a-z0-9_.\- =\"]+type=\"([a-zA-Z0-9_\/]+)\"/", $content, $matches)) {
            $classes = array_merge($classes, $matches[1]);
        }

        $classes = array_unique($classes);
        return $classes;
    }

    /**
     * Finds usage of block names like parameter of methods
     *
     * @param SplFileInfo $fileInfo
     * @param string $content
     * @return array
     */
    protected function _visitBlockNamesInMethodCalls($fileInfo, $content)
    {
        if (!$this->_fileHasExtensions($fileInfo, array('php','phtml'))) {
            return array();
        }

        $methods = "addBlock|createBlock|getBlockClassName|getBlockSingleton";
        if (preg_match_all("/(?:" . $methods . ")\(['\"]([a-zA-Z0-9_\/]+)['\"][\),]/", $content, $matches)) {
            return array_unique($matches[1]);
        }

        return array();
    }

    /**
     * Finds usage of resource model classes, set as "_parentResourceModelName = 'Class_Name'"
     *
     * @param SplFileInfo $fileInfo
     * @param string $content
     * @return array
     */
    protected function _visitEnterpriseSalesParentResourceModel($fileInfo, $content)
    {
        if (!$this->_fileHasExtensions($fileInfo, 'php')) {
            return array();
        }

        $regexp = '/_parentResourceModelName\s*=\s*[\'"]([a-zA-Z0-9_]+)[\'"];/';
        if (!preg_match_all($regexp, $content, $matches)) {
            return array();
        }
        return $matches[1];
    }

    /**
     * Find usage of Mage::getModel("Class_Name")
     *
     * @param SplFileInfo $fileInfo
     * @param string $content
     * @return array
     */
    protected function _visitMageGetModel($fileInfo, $content)
    {
        if (!$this->_fileHasExtensions($fileInfo, array('php', 'phtml'))) {
            return array();
        }

        return $this->_getFuncStringArguments("Mage::getModel", $content);
    }

    /**
     * Find usage of Mage::getSingleton("Class_Name")
     *
     * @param SplFileInfo $fileInfo
     * @param string $content
     * @return array
     */
    protected function _visitMageGetSingleton($fileInfo, $content)
    {
        if (!$this->_fileHasExtensions($fileInfo, array('php', 'phtml'))) {
            return array();
        }

        return $this->_getFuncStringArguments("Mage::getSingleton", $content);
    }

    /**
     * Find usage of ->_init(.*, .*)
     *
     * @param SplFileInfo $fileInfo
     * @param string $content
     * @return array
     */
    protected function _visitInternalInitFunction($fileInfo, $content)
    {
        if (!$this->_fileHasExtensions($fileInfo, array('php', 'phtml'))) {
            return array();
        }

        $funcPatterns = array(
             /** _init is used for setting table_names either */
             array ("->_init", "/{%function_name%}\\(['\"]([\\w\\d\\/\\_]+)?['\"](\\s?,\\s?['\"].*['\"]\\))/Ui" ),
        );
        $skippedClassKeys = array(
            'id', '_id', 'code', 'serial_number', 'entity_pk_value', 'status', 'pk'
        );


        $patterns = array();
        foreach ($funcPatterns as $funcPattern) {
            list($function, $pattern) = $funcPattern;
            $patterns[] = str_replace("{%function_name%}", $function, $pattern);
        }

        $result = array();
        foreach ($patterns as $pattern) {
            $matched = preg_match_all($pattern, $content, $matches);
            if ($matched) {
                $skipClassName = false;
                foreach ($skippedClassKeys as $key) {
                    if (strpos($matches[2][0], $key) > 0 ) {
                        $skipClassName = true;
                        break;
                    }
                }
                if ($skipClassName) {
                    continue;
                }
                $result = array_merge($result, $matches[1]);
            }
        }
        $result = array_unique($result);
        return $result;
    }

    /**
     * Find usage model_names in other functions
     *
     * @param SplFileInfo $fileInfo
     * @param string $content
     * @return array
     */
    protected function _visitOtherFunctionsWithModelDeclaration($fileInfo, $content)
    {
        if (!$this->_fileHasExtensions($fileInfo, array('php', 'phtml'))) {
            return array();
        }

        $funcPatterns = array(
            array ("->_initLayoutMessages", "/{%function_name%}\\(['\"]([\\w\\d\\/\\_]+)?[\"'](\\))/Ui" ),
            array ("->initLayoutMessages",  "/{%function_name%}\\(['\"]([\\w\\d\\/\\_]+)?[\"'](\\))/Ui" ),
            array ("->setAttributeModel",   "/{%function_name%}\\(['\"]([\\w\\d\\/\\_]+)?[\"'](\\))/Ui" ),
            array ("->setAttributeModel",   "/{%function_name%}\\(['\"]([\\w\\d\\/\\_]+)?['\"](.*\\))/Ui" ),
            array ("->setBackendModel",     "/{%function_name%}\\(['\"]([\\w\\d\\/\\_]+)?[\"'](\\))/Ui" ),
            array ("->setBackendModel",     "/{%function_name%}\\(['\"]([\\w\\d\\/\\_]+)?['\"](.*\\))/Ui" ),
            array ("->setFrontendModel",    "/{%function_name%}\\(['\"]([\\w\\d\\/\\_]+)?[\"'](\\))/Ui" ),
            array ("->setFrontendModel",    "/{%function_name%}\\(['\"]([\\w\\d\\/\\_]+)?['\"](.*\\))/Ui" ),
            array ("->setSourceModel",      "/{%function_name%}\\(['\"]([\\w\\d\\/\\_]+)?[\"'](\\))/Ui" ),
            array ("->setSourceModel",      "/{%function_name%}\\(['\"]([\\w\\d\\/\\_]+)?['\"](.*\\))/Ui" ),
            array ("->setModel",            "/{%function_name%}\\(['\"]([\\w\\d\\/\\_]+)?[\"'](\\))/Ui" ),
            array ("->setModel",            "/{%function_name%}\\(['\"]([\\w\\d\\/\\_]+)?['\"](.*\\))/Ui" ),
            /** setType function could be used as test for manual checking,
             * but globally function is used for different variables setting
             */
            // array ("->setType",            "/{%function_name%}\\(['\"]([\\w\\d\\/\\_]+)?[\"'](\\))/Ui" ),
            // array ("->setType",            "/{%function_name%}\\(['\"]([\\w\\d\\/\\_]+)?['\"](.*\\))/Ui" ),
        );

        $patterns = array();
        foreach ($funcPatterns as $funcPattern) {
            list($function, $pattern) = $funcPattern;
            $patterns[] = str_replace("{%function_name%}", $function, $pattern);
        }

        $result = array();
        foreach ($patterns as $pattern) {
            $matched = preg_match_all($pattern, $content, $matches);
            if ($matched) {
                $result = array_merge($result, $matches[1]);
            }
        }
        $result = array_unique($result);
        return $result;
    }

    /**
     * Finds usage model in *.xml files
     *
     * @param SplFileInfo $fileInfo
     * @param string $content
     * @return array
     */
    protected function _visitXmlAttributeDefinitionsForModels($fileInfo, $content)
    {
        if (!$this->_fileHasExtensions($fileInfo, array('xml'))) {
            return array();
        }

        $skippedFiles = array(
            "app" . DS . "etc" . DS . "config.xml",
            "Enterprise" . DS . "Staging" . DS . "etc" . DS . "config.xml",
        );

        foreach ($skippedFiles as $skippedFile) {
            if (strpos($fileInfo->getRealPath(), $skippedFile) !== false) {
                return array();
            }
        }

        $_wordsToFind = array(
            "class",
            "model",
            "backend_model",
            "source_model",
            "price_model",
            "model_token",
            "attribute_model",
            "writer_model"
        );
        $_patternsToFind = array(
            ">([\\w\\d\\/\\_]+)?(<\\/)",
            ">([\\w\\d\\/\\_]+)?(::[\\w\\d\\_]+<\\/)"
        );

        $patterns = array();
        foreach ($_wordsToFind as $wordToFind) {
            foreach ($_patternsToFind as $patternToFind) {
                $patterns[] = "/<" . $wordToFind . $patternToFind . $wordToFind . ">/Ui";
            }
        }

        $result = array();
        foreach ($patterns as $pattern) {
            $matched = preg_match_all($pattern, $content, $matches);
            if ($matched) {
                $result = array_merge($result, $matches[1]);
            }
        }
        $result = array_unique($result);
        return $result;
    }

    /**
     * Finds usage expected_models in logging.xml files
     *
     * @param SplFileInfo $fileInfo
     * @param string $content
     * @return array
     */
    protected function _visitModelsInLoggingXmlDefinitions($fileInfo, $content)
    {
        if ($fileInfo->getBasename() != 'logging.xml') {
            return array();
        }

        $xml = new SimpleXMLElement($content);
        $expectedModels = array();
        foreach (array(
                '//logging/*/expected_models',
                '//logging/*/actions/*/expected_models'
            ) as $xpath
        ) {
            $expectedModels = array_merge($expectedModels, $xml->xpath($xpath));
        }

        $result = array();
        foreach ($expectedModels as $expectModelNode) {
            $expectedModel = (array) $expectModelNode;
            foreach (array_keys($expectedModel) as $key) {
                if ($key == "@attributes") {
                    continue;
                }
                $result[] = $key;
            }
        }

        return array_unique($result);
    }

    /**
     * Finds usage action[@method="setEntityModelClass"]/code in layout.xml files
     *
     * @param SplFileInfo $fileInfo
     * @param string $content
     * @return array
     */
    protected function _visitModelsInLayoutXmlDefinitions($fileInfo, $content)
    {
        $result = array();
        $separator = DIRECTORY_SEPARATOR;

        if ($this->_fileHasExtensions($fileInfo, 'xml')
            && (false !== strpos($fileInfo->getPath(), "{$separator}view{$separator}")
                || false !== strpos($fileInfo->getPath(), "{$separator}app{$separator}design{$separator}")
            )
        ) {
            $xml = new SimpleXMLElement($content);
            foreach (array(
                    '//layout/*/block/action[@method="setEntityModelClass"]/code',
                    '//layout/*/reference/block/action[@method="setEntityModelClass"]/code'
                ) as $xpath
            ) {
                foreach ($xml->xpath($xpath) as $expectModelNode) {
                    $result[] = (string) $expectModelNode;
                }
            }
        }

        return array_unique($result);
    }
}
