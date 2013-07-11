<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Code
 * @copyright   {copyright}
 * @license     {license_link}
 */

abstract class Magento_Code_Generator_EntityAbstract
{
    /**
     * Entity type
     */
    const ENTITY_TYPE = 'abstract';

    /**
     * @var array
     */
    private $_errors = array();

    /**
     * Source model class name
     *
     * @var string
     */
    private $_sourceClassName;

    /**
     * Result model class name
     *
     * @var string
     */
    private $_resultClassName;

    /**
     * @var Magento_Code_Generator_Io
     */
    private $_ioObject;

    /**
     * Autoloader instance
     *
     * @var Magento_Autoload_IncludePath
     */
    private $_autoloader;

    /**
     * Class generator object
     *
     * @var Magento_Code_Generator_CodeGenerator_Interface
     */
    protected $_classGenerator;

    /**
     * @param string $sourceClassName
     * @param string $resultClassName
     * @param Magento_Code_Generator_Io $ioObject
     * @param Magento_Code_Generator_CodeGenerator_Interface $classGenerator
     * @param Magento_Autoload_IncludePath $autoLoader
     */
    public function __construct(
        $sourceClassName = null,
        $resultClassName = null,
        Magento_Code_Generator_Io $ioObject = null,
        Magento_Code_Generator_CodeGenerator_Interface $classGenerator = null,
        Magento_Autoload_IncludePath $autoLoader = null
    ) {
        if ($autoLoader) {
            $this->_autoloader = $autoLoader;
        } else {
            $this->_autoloader = new Magento_Autoload_IncludePath();
        }
        if ($ioObject) {
            $this->_ioObject = $ioObject;
        } else {
            $this->_ioObject = new Magento_Code_Generator_Io(new Varien_Io_File(), $this->_autoloader);
        }
        if ($classGenerator) {
            $this->_classGenerator = $classGenerator;
        } else {
            $this->_classGenerator = new Magento_Code_Generator_CodeGenerator_Zend();
        }

        $this->_sourceClassName = ltrim($sourceClassName, Magento_Autoload_IncludePath::NS_SEPARATOR);
        if ($resultClassName) {
            $this->_resultClassName = $resultClassName;
        } elseif ($sourceClassName) {
            $this->_resultClassName = $this->_getDefaultResultClassName($sourceClassName);
        }
    }

    /**
     * Generation template method
     *
     * @return bool
     */
    public function generate()
    {
        try {
            if ($this->_validateData()) {
                $sourceCode = $this->_generateCode();
                if ($sourceCode) {
                    $fileName = $this->_ioObject->getResultFileName($this->_getResultClassName());
                    $this->_ioObject->writeResultFile($fileName, $sourceCode);
                    return true;
                } else {
                    $this->_addError('Can\'t generate source code.');
                }
            }
        } catch (Exception $e) {
            $this->_addError($e->getMessage());
        }
        return false;
    }

    /**
     * List of occurred generation errors
     *
     * @return array
     */
    public function getErrors()
    {
        return $this->_errors;
    }

    /**
     * @return string
     */
    protected function _getSourceClassName()
    {
        return $this->_sourceClassName;
    }

    /**
     * @param string $className
     * @return string
     */
    protected function _getFullyQualifiedClassName($className)
    {
        return Magento_Autoload_IncludePath::NS_SEPARATOR
            . ltrim($className, Magento_Autoload_IncludePath::NS_SEPARATOR);
    }

    /**
     * @return string
     */
    protected function _getResultClassName()
    {
        return $this->_resultClassName;
    }

    /**
     * @param string $modelClassName
     * @return string
     */
    protected function _getDefaultResultClassName($modelClassName)
    {
        return $modelClassName . ucfirst(static::ENTITY_TYPE);
    }

    /**
     * Returns list of properties for class generator
     *
     * @return array
     */
    protected function _getClassProperties()
    {
        // protected $_objectManager = null;
        $objectManager = array(
            'name'       => '_objectManager',
            'visibility' => 'protected',
            'docblock'   => array(
                'shortDescription' => 'Object Manager instance',
                'tags'             => array(
                    array('name' => 'var', 'description' => '\Magento_ObjectManager')
                )
            ),
        );

        return array($objectManager);
    }

    /**
     * Get default constructor definition for generated class
     *
     * @return array
     */
    protected abstract function _getDefaultConstructorDefinition();

    /**
     * Returns list of methods for class generator
     *
     * @return mixed
     */
    abstract protected function _getClassMethods();

    /**
     * @return string
     */
    protected function _generateCode()
    {
        $this->_classGenerator
            ->setName($this->_getResultClassName())
            ->addProperties($this->_getClassProperties())
            ->addMethods($this->_getClassMethods())
            ->setClassDocBlock($this->_getClassDocBlock());

        return $this->_getGeneratedCode();
    }

    /**
     * @param string $message
     * @return Magento_Code_Generator_EntityAbstract
     */
    protected function _addError($message)
    {
        $this->_errors[] = $message;
        return $this;
    }

    /**
     * @return bool
     */
    protected function _validateData()
    {
        $sourceClassName = $this->_getSourceClassName();
        $resultClassName = $this->_getResultClassName();
        $resultFileName  = $this->_ioObject->getResultFileName($resultClassName);

        $autoloader = $this->_autoloader;

        if (!$autoloader::getFile($sourceClassName)) {
            $this->_addError('Source class ' . $sourceClassName . ' doesn\'t exist.');
            return false;
        } elseif ($autoloader::getFile($resultClassName)) {
            $this->_addError('Result class ' . $resultClassName . ' already exists.');
            return false;
        } elseif (!$this->_ioObject->makeGenerationDirectory()) {
            $this->_addError('Can\'t create directory ' . $this->_ioObject->getGenerationDirectory() . '.');
            return false;
        } elseif (!$this->_ioObject->makeResultFileDirectory($resultClassName)) {
            $this->_addError(
                'Can\'t create directory ' . $this->_ioObject->getResultFileDirectory($resultClassName) . '.'
            );
            return false;
        } elseif ($this->_ioObject->fileExists($resultFileName)) {
            $this->_addError('Result file ' . $resultFileName . ' already exists.');
            return false;
        }
        return true;
    }

    /**
     * @return array
     */
    protected function _getClassDocBlock()
    {
        $description = ucfirst(static::ENTITY_TYPE) . ' class for \\' . $this->_getSourceClassName();
        return array('shortDescription' => $description);
    }

    /**
     * @return string
     */
    protected function _getGeneratedCode()
    {
        $sourceCode = $this->_classGenerator->generate();
        return $this->_fixCodeStyle($sourceCode);
    }

    /**
     * @param string $sourceCode
     * @return mixed
     */
    protected function _fixCodeStyle($sourceCode)
    {
        $sourceCode = str_replace(' array (', ' array(', $sourceCode);
        $sourceCode = preg_replace("/{\n{2,}/m", "{\n", $sourceCode);
        $sourceCode = preg_replace("/\n{2,}}/m", "\n}", $sourceCode);
        return $sourceCode;
    }

    /**
     * Escape method parameter default value
     *
     * @param string $value
     * @return string
     */
    protected function _escapeDefaultValue($value)
    {
        // escape slashes
        return str_replace('\\', '\\\\', $value);
    }

    /**
     * Get value generator for null default value
     *
     * @return \Zend\Code\Generator\ValueGenerator
     */
    protected function _getNullDefaultValue()
    {
        $value = new \Zend\Code\Generator\ValueGenerator(null, \Zend\Code\Generator\ValueGenerator::TYPE_NULL);

        return $value;
    }
}
