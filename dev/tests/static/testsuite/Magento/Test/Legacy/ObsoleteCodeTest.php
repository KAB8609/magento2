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
 * Tests to find various obsolete code usage
 * (deprecated and removed Magento 1 legacy methods, properties, classes, etc.)
 */
class Magento_Test_Legacy_ObsoleteCodeTest extends PHPUnit_Framework_TestCase
{
    /**
     * Message text that is used to render suggestions
     */
    const SUGGESTION_MESSAGE = 'Use "%s" instead.';

    /**@#+
     * Lists of obsolete entities from fixtures
     *
     * @var array
     */
    protected static $_classes    = array();
    protected static $_constants  = array();
    protected static $_methods    = array();
    protected static $_attributes = array();
    protected static $_namespaces = array();
    /**#@-*/

    /**
     * Read fixtures into memory as arrays
     */
    public static function setUpBeforeClass()
    {
        $errors = array();
        self::_populateList(self::$_classes, $errors, 'obsolete_classes*.php', false);
        self::_populateList(self::$_constants, $errors, 'obsolete_constants*.php');
        self::_populateList(self::$_methods, $errors, 'obsolete_methods*.php');
        self::_populateList(self::$_namespaces, $errors, 'obsolete_namespaces*.php', false);
        self::_populateList(self::$_attributes, $errors, 'obsolete_properties*.php');
        if ($errors) {
            $message = 'Duplicate patterns identified in list declarations:' . PHP_EOL . PHP_EOL;
            foreach ($errors as $file => $list) {
                $message .= $file . PHP_EOL;
                foreach ($list as $key) {
                    $message .= "    {$key}" . PHP_EOL;
                }
                $message .= PHP_EOL;
            }
            throw new Exception($message);
        }
    }

    /**
     * Read the specified file pattern and merge it with the list
     *
     * Duplicate entries will be recorded into errors array.
     *
     * @param array $list
     * @param array $errors
     * @param string $filePattern
     * @param bool $hasScope
     */
    protected static function _populateList(array &$list, array &$errors, $filePattern, $hasScope = true)
    {
        foreach (glob(__DIR__ . '/_files/' . $filePattern) as $file) {
            foreach (self::_readList($file) as $row) {
                list($item, $scope, $replacement) = self::_padRow($row, $hasScope);
                $key = "{$item}|{$scope}";
                if (isset($list[$key])) {
                    $errors[$file][] = $key;
                } else {
                    $list[$key] = array($item, $scope, $replacement);
                }
            }
        }
    }

    /**
     * Populate insufficient row elements regarding to whether the row supposed to have scope value
     *
     * @param array $row
     * @param bool $hasScope
     * @return array
     */
    protected static function _padRow($row, $hasScope)
    {
        if ($hasScope) {
            return array_pad($row, 3, '');
        }
        list($item, $replacement) = array_pad($row, 2, '');
        return array($item, '', $replacement);
    }

    /**
     * Isolate including a file into a method to reduce scope
     *
     * @param $file
     * @return array
     */
    protected static function _readList($file)
    {
        return include($file);
    }

    /**
     * @param string $file
     * @dataProvider phpFileDataProvider
     */
    public function testPhpFile($file)
    {
        $content = file_get_contents($file);
        $this->_testObsoleteClasses($content);
        $this->_testObsoleteMethods($content);
        $this->_testGetChildSpecialCase($content, $file);
        $this->_testGetOptionsSpecialCase($content);
        $this->_testObsoleteMethodArguments($content);
        $this->_testObsoleteProperties($content);
        $this->_testObsoleteActions($content);
        $this->_testObsoleteConstants($content);
        $this->_testObsoletePropertySkipCalculate($content);
        $this->_testObsoleteNamespace($file);
    }

    /**
     * @return array
     */
    public function phpFileDataProvider()
    {
        return Magento_TestFramework_Utility_Files::init()->getPhpFiles();
    }

    /**
     * @param string $file
     * @dataProvider phtmlFileDataProvider
     */
    public function testTemplateMageCall($file)
    {
        $content = file_get_contents($file);
        $this->_assertNotRegExp('/\bMage::(\w+?)\(/iS', $content, "Static Method of 'Mage' class is obsolete.");
    }

    /**
     * @return array
     */
    public function phtmlFileDataProvider()
    {
        return Magento_TestFramework_Utility_Files::init()->getPhpFiles(false, false, true);
    }

    /**
     * @param string $file
     * @dataProvider xmlFileDataProvider
     */
    public function testXmlFile($file)
    {
        $content = file_get_contents($file);
        $this->_testObsoleteClasses($content, $file);
        $this->_testObsoleteNamespace($file);
    }

    /**
     * @return array
     */
    public function xmlFileDataProvider()
    {
        return Magento_TestFramework_Utility_Files::init()->getXmlFiles();
    }

    /**
     * @param string $file
     * @dataProvider jsFileDataProvider
     */
    public function testJsFile($file)
    {
        $content = file_get_contents($file);
        $this->_testObsoletePropertySkipCalculate($content);
    }

    /**
     * @return array
     */
    public function jsFileDataProvider()
    {
        return Magento_TestFramework_Utility_Files::init()->getJsFiles();
    }

    /**
     * Assert that obsolete classes are not used in the content
     *
     * @param string $content
     */
    protected function _testObsoleteClasses($content)
    {
        foreach (self::$_classes as $row) {
            list($class, , $replacement) = $row;
            $this->_assertNotRegExp('/[^a-z\d_]' . preg_quote($class, '/') . '[^a-z\d_]/iS', $content,
                $this->_suggestReplacement(sprintf("Class '%s' is obsolete.", $class), $replacement)
            );
        }
    }

    /**
     * Assert that obsolete methods or functions are not used in the content
     *
     * If class context is not specified, declaration/invocation of all functions or methods (of any class)
     * will be matched across the board
     *
     * If context is specified, only the methods will be matched as follows:
     * - usage of class::method
     * - usage of $this, self and static within the class and its descendants
     *
     * @param string $content
     */
    protected function _testObsoleteMethods($content)
    {
        foreach (self::$_methods as $row) {
            list($method, $class, $replacement) = $row;
            $quotedMethod = preg_quote($method, '/');
            if ($class) {
                $message = $this->_suggestReplacement("Method '{$class}::{$method}()' is obsolete.", $replacement);
                // without opening parentheses to match static callbacks notation
                $this->_assertNotRegExp(
                    '/' . preg_quote($class, '/') . '::\s*' . $quotedMethod . '[^a-z\d_]/iS',
                    $content,
                    $message
                );
                if ($this->_isClassOrInterface($content, $class) || $this->_isDirectDescendant($content, $class)) {
                    $this->_assertNotRegExp('/function\s*' . $quotedMethod . '\s*\(/iS', $content, $message);
                    $this->_assertNotRegExp('/this->' . $quotedMethod . '\s*\(/iS', $content, $message);
                    $this->_assertNotRegExp(
                        '/(self|static|parent)::\s*' . $quotedMethod . '\s*\(/iS', $content, $message
                    );
                }
            } else {
                $message = $this->_suggestReplacement("Function or method '{$method}()' is obsolete.", $replacement);
                $this->_assertNotRegExp('/function\s*' . $quotedMethod . '\s*\(/iS', $content, $message);
                $this->_assertNotRegExp('/[^a-z\d_]' . $quotedMethod . '\s*\(/iS', $content, $message);
            }
        }
    }

    /**
     * Assert that obsolete namespaces are not used in the content
     *
     * This method will search the content for references to class
     * that start with obsolete namespace
     *
     * @param string $content
     */
    protected function _testObsoleteNamespace($file)
    {
        foreach (self::$_namespaces as $row) {
            list($namespacePath, , $replacementPath) = $row;
            $relativePath = str_replace(Magento_TestFramework_Utility_Files::init()->getPathToSource(), "", $file);
            $namespacePathArray = explode('/', $namespacePath);
            $namespace = $namespacePathArray[4];
            $replacementPathArray = explode('/', $replacementPath);
            $replacement = $replacementPathArray[3];
            $message = $this->_suggestReplacement("Namespace '{$namespace}' is obsolete.", $replacement);
            $this->assertStringStartsNotWith($namespace, $relativePath, $message);
        }
    }

    /**
     * Special case: don't allow usage of getChild() method anywhere within app directory
     *
     * In Magento 1.x it used to belong only to abstract block (therefore all blocks)
     * At the same time, the name is pretty generic and can be encountered in other directories, such as lib
     *
     * @param string $content
     * @param string $file
     */
    protected function _testGetChildSpecialCase($content, $file)
    {
        if (0 === strpos($file, Magento_TestFramework_Utility_Files::init()->getPathToSource() . '/app/')) {
            $this->_assertNotRegexp('/[^a-z\d_]getChild\s*\(/iS', $content,
                'Block method getChild() is obsolete. ' .
                'Replacement suggestion: \Magento\Core\Block\AbstractBlock::getChildBlock()'
            );
        }
    }

    /**
     * Special case for ->getConfig()->getOptions()->
     *
     * @param string $content
     */
    protected function _testGetOptionsSpecialCase($content)
    {
        $this->_assertNotRegexp(
            '/getOptions\(\)\s*->get(Base|App|Code|Design|Etc|Lib|Locale|Js|Media'
                .'|Var|Tmp|Cache|Log|Session|Upload|Export)?Dir\(/S',
            $content,
            'The class Magento_Core_Model_Config_Options is obsolete. Replacement suggestion: \Magento\Core\Model\Dir'
        );
    }

    /**
     * @param string $content
     */
    protected function _testObsoleteMethodArguments($content)
    {
        $this->_assertNotRegExp('/[^a-z\d_]getTypeInstance\s*\(\s*[^\)]+/iS', $content,
            'Backwards-incompatible change: method getTypeInstance() is not supposed to be invoked with any arguments.'
        );
        $this->_assertNotRegExp('/\->getUsedProductIds\(([^\)]+,\s*[^\)]+)?\)/', $content,
            'Backwards-incompatible change: method getUsedProductIds($product)'
                . ' must be invoked with one and only one argument - product model object'
        );

        $this->_assertNotRegExp('#->_setActiveMenu\([\'"]([\w\d/_]+)[\'"]\)#Ui', $content,
            'Backwards-incompatible change: method _setActiveMenu()'
                . ' must be invoked with menu item identifier than xpath for menu item'
        );
    }

    /**
     * @param string $content
     */
    protected function _testObsoleteProperties($content)
    {
        foreach (self::$_attributes as $row) {
            list($attribute, $class, $replacement) = $row;
            if ($class) {
                if (!$this->_isClassOrInterface($content, $class) && !$this->_isDirectDescendant($content, $class)) {
                    continue;
                }
                $fullyQualified = "{$class}::\${$attribute}";
            } else {
                $fullyQualified = $attribute;
            }
            $this->_assertNotRegExp('/[^a-z\d_]' . preg_quote($attribute, '/') . '[^a-z\d_]/iS', $content,
                $this->_suggestReplacement(sprintf("Class attribute '%s' is obsolete.", $fullyQualified), $replacement)
            );
        }
    }

    /**
     * @param string $content
     */
    protected function _testObsoleteActions($content)
    {
        $suggestion = 'Resizing images upon the client request is obsolete, use server-side resizing instead';
        $this->_assertNotRegExp('#[^a-z\d_/]catalog/product/image[^a-z\d_/]#iS', $content,
            "Action 'catalog/product/image' is obsolete. $suggestion"
        );
    }

    /**
     * Assert that obsolete constants are not defined/used in the content
     *
     * Without class context, only presence of the literal will be checked.
     *
     * In context of a class, match:
     * - fully qualified constant notation (with class)
     * - usage with self::/parent::/static:: notation
     *
     * @param string $content
     */
    protected function _testObsoleteConstants($content)
    {
        foreach (self::$_constants as $row) {
            list($constant, $class, $replacement) = $row;
            if ($class) {
                $fullyQualified = "{$class}::{$constant}";
                $regex = preg_quote($fullyQualified, '/');
                if ($this->_isClassOrInterface($content, $class)) {
                    $regex .= '|' . $this->_getClassConstantDefinitionRegExp($constant)
                        . '|' . preg_quote("self::{$constant}", '/')
                        . '|' . preg_quote("static::{$constant}", '/');
                } else if ($this->_isDirectDescendant($content, $class)) {
                    $regex .= '|' . preg_quote("parent::{$constant}", '/');
                    if (!$this->_isClassConstantDefined($content, $constant)) {
                        $regex .= '|' . preg_quote("self::{$constant}", '/')
                            . '|' . preg_quote("static::{$constant}", '/');
                    }
                }
            } else {
                $fullyQualified = $constant;
                $regex = preg_quote($constant, '/');
            }
            $this->_assertNotRegExp('/[^a-z\d_]' . $regex . '[^a-z\d_]/iS', $content,
                $this->_suggestReplacement(sprintf("Constant '%s' is obsolete.", $fullyQualified), $replacement)
            );
        }
    }

    /**
     * Whether a class constant is defined in the content or not
     *
     * @param string $content
     * @param string $constant
     * @return bool
     */
    protected function _isClassConstantDefined($content, $constant)
    {
        return (bool)preg_match('/' . $this->_getClassConstantDefinitionRegExp($constant) . '/iS', $content);
    }

    /**
     * Retrieve a PCRE matching a class constant definition
     *
     * @param string $constant
     * @return string
     */
    protected function _getClassConstantDefinitionRegExp($constant)
    {
        return '\bconst\s+' . preg_quote($constant, '/') . '\b';
    }

    /**
     * @param string $content
     */
    protected function _testObsoletePropertySkipCalculate($content)
    {
        $this->_assertNotRegExp('/[^a-z\d_]skipCalculate[^a-z\d_]/iS', $content,
            "Configuration property 'skipCalculate' is obsolete."
        );
    }

    /**
     * Analyze contents to determine whether this is declaration of specified class/interface
     *
     * @param string $content
     * @param string $name
     * @return bool
     */
    protected function _isClassOrInterface($content, $name)
    {
        $name = preg_quote($name, '/');
        return (bool)preg_match('/\b(?:class|interface)\s+' . $name . '\b[^{]*\{/iS', $content);
    }

    /**
     * Analyze contents to determine whether this is a direct descendant of specified class/interface
     *
     * @param string $content
     * @param string $name
     * @return bool
     */
    protected function _isDirectDescendant($content, $name)
    {
        $name = preg_quote($name, '/');
        return (bool)preg_match(
            '/\s+extends\s+' . $name . '\b|\s+implements\s+[^{]*\b' . $name . '\b[^{]*\{/iS',
            $content
        );
    }

    /**
     * Append a "suggested replacement" part to the string
     *
     * @param string $original
     * @param string $suggestion
     * @return string
     */
    private function _suggestReplacement($original, $suggestion)
    {
        if ($suggestion) {
            return "{$original} Suggested replacement: {$suggestion}";
        }
        return $original;
    }

    /**
     * Custom replacement for assertNotRegexp()
     *
     * In this particular test the original assertNotRegexp() cannot be used
     * because of too large text $content, which obfuscates tests output
     *
     * @param string $regex
     * @param string $content
     * @param string $message
     */
    protected function _assertNotRegexp($regex, $content, $message)
    {
        $this->assertSame(0, preg_match($regex, $content), $message);
    }
}
