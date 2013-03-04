<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Di
 * @copyright   {copyright}
 * @license     {license_link}
 */

use Zend\Di\Exception,
    Zend\Code\Reflection,
    Zend\Di\Definition\IntrospectionStrategy;

class Magento_Di_Definition_CompilerDefinition_Zend extends Zend\Di\Definition\CompilerDefinition
    implements Magento_Di_Definition_CompilerDefinition
{
    /**
     * @var Magento_Di_Generator_Class
     */
    protected $_classGenerator;

    /**
     * @param Magento_Di_Generator_Class $classGenerator
     * @param Zend\Di\Definition\IntrospectionStrategy $strategy
     */
    public function __construct(
        IntrospectionStrategy $strategy = null,
        Magento_Di_Generator_Class $classGenerator = null
    ) {
        parent::__construct($strategy);
        $this->_classGenerator = $classGenerator ?: new Magento_Di_Generator_Class();
    }

    /**
     * Process class method parameters
     *
     * @param array $def
     * @param Zend\Code\Reflection\ClassReflection $rClass
     * @param Zend\Code\Reflection\MethodReflection $rMethod
     */
    protected function processParams(&$def, Reflection\ClassReflection $rClass, Reflection\MethodReflection $rMethod)
    {
        if (count($rMethod->getParameters()) === 0) {
            return;
        }

        parent::processParams($def, $rClass, $rMethod);

        $methodName = $rMethod->getName();

        /** @var $p \ReflectionParameter */
        foreach ($rMethod->getParameters() as $p) {
            $fqName = $rClass->getName() . '::' . $rMethod->getName() . ':' . $p->getPosition();

            $def['parameters'][$methodName][$fqName][] = ($p->isOptional() && $p->isDefaultValueAvailable())
                ? $p->getDefaultValue()
                : null;
        }
    }

    /**
     * Get definition as array
     *
     * @return array
     */
    public function toArray()
    {
        return $this->toArrayDefinition()->toArray();
    }

    /**
     * Convert to array definition
     *
     * @return Magento_Di_Definition_ArrayDefinition
     */
    public function toArrayDefinition()
    {
        return new Magento_Di_Definition_ArrayDefinition_Zend($this->classes);
    }

    /**
     * @param string $class
     */
    protected function processClass($class)
    {
        $this->_classGenerator->generateForConstructor($class);
        parent::processClass($class);
    }
}
