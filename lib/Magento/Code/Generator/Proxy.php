<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Code
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Code_Generator_Proxy extends Magento_Code_Generator_EntityAbstract
{
    /**
     * Entity type
     */
    const ENTITY_TYPE = 'proxy';

    /**
     * @param string $modelClassName
     * @return string
     */
    protected function _getDefaultResultClassName($modelClassName)
    {
        return $modelClassName . '_' . ucfirst(static::ENTITY_TYPE);
    }

    /**
     * Retrieve class properties
     *
     * @return array
     */
    protected function _getClassProperties()
    {
        $properties = array();

        // protected $_objectManager = null;
        $properties[] = array(
            'name'       => '_objectManager',
            'visibility' => 'protected',
            'docblock'   => array(
                'shortDescription' => 'Object Manager instance',
                'tags'             => array(
                    array('name' => 'var', 'description' => '\Magento_ObjectManager')
                )
            ),
        );

        // protected $_instanceName = null;
        $properties[] = array(
            'name'       => '_instanceName',
            'visibility' => 'protected',
            'docblock'   => array(
                'shortDescription' => 'Proxied instance name',
                'tags'             => array(
                    array('name' => 'var', 'description' => 'string')
                )
            ),
        );

        $properties[] = array(
            'name'       => '_subject',
            'visibility' => 'protected',
            'docblock'   => array(
                'shortDescription' => 'Proxied instance',
                'tags'             => array(
                    array('name' => 'var', 'description' => '\\' . $this->_getSourceClassName())
                )
            ),
        );

        // protected $_shared = null;
        $properties[] = array(
            'name'       => '_isShared',
            'visibility' => 'protected',
            'docblock'   => array(
                'shortDescription' => 'Instance shareability flag',
                'tags'             => array(
                    array('name' => 'var', 'description' => 'bool')
                )
            ),
        );
        return $properties;
    }

    /**
     * @return array
     */
    protected function _getClassMethods()
    {
        $construct = $this->_getDefaultConstructorDefinition();

        // create proxy methods for all non-static and non-final public methods (excluding constructor)
        $methods = array($construct);
        $methods[] = array(
            'name'     => '__sleep',
            'body'     => 'return array(\'_subject\', \'_isShared\');',
            'docblock' => array(
                'tags' => array(
                    array('name' => 'return', 'description' => 'array')
                ),
            ),
        );
        $methods[] = array(
            'name'     => '__wakeup',
            'body'     => '$this->_objectManager = Mage::getObjectManager();',
            'docblock' => array(
                'shortDescription' => 'Retrieve ObjectManager from global scope',
            ),
        );
        $methods[] = array(
            'name'     => '__clone',
            'body'     => "\$this->_subject = clone \$this->_getSubject();",
            'docblock' => array(
                'shortDescription' => 'Clone proxied instance',
            ),
        );

        $methods[] = array(
            'name'       => '_getSubject',
            'visibility' => 'protected',
            'body'       => "if (!\$this->_subject) {\n" .
                "    \$this->_subject = true === \$this->_isShared\n" .
                "        ? \$this->_objectManager->create(\$this->_instanceName)\n" .
                "        : \$this->_objectManager->get(\$this->_instanceName);\n" .
                "}\n" .
                "return \$this->_subject;",
            'docblock'   => array(
                'shortDescription' => 'Get proxied instance',
                'tags'             => array(
                    array('name' => 'return', 'description' => '\\' . $this->_getSourceClassName())
                )
            ),

        );
        $reflectionClass = new ReflectionClass($this->_getSourceClassName());
        $publicMethods   = $reflectionClass->getMethods(ReflectionMethod::IS_PUBLIC);
        foreach ($publicMethods as $method) {
            if (!($method->isConstructor() || $method->isFinal() || $method->isStatic() || $method->isDestructor())
                && !in_array($method->getName(), array('__sleep', '__wakeup', '__clone'))
            ) {
                $methods[] = $this->_getMethodInfo($method);
            }
        }

        return $methods;
    }

    /**
     * @return string
     */
    protected function _generateCode()
    {
        $typeName = $this->_getFullyQualifiedClassName($this->_getSourceClassName());
        $reflection = new ReflectionClass($typeName);

        if ($reflection->isInterface()) {
            $this->_classGenerator->setImplementedInterfaces(array($typeName));
        } else {
            $this->_classGenerator->setExtendedClass($typeName);
        }
        return parent::_generateCode();
    }

    /**
     * Collect method info
     *
     * @param ReflectionMethod $method
     * @return array
     */
    protected function _getMethodInfo(ReflectionMethod $method)
    {
        $parameterNames = array();
        $parameters     = array();
        foreach ($method->getParameters() as $parameter) {
            $parameterNames[] = '$' . $parameter->getName();
            $parameters[]     = $this->_getMethodParameterInfo($parameter);
        }

        $methodInfo = array(
            'name'       => $method->getName(),
            'parameters' => $parameters,
            'body'       => $this->_getMethodBody($method->getName(), $parameterNames),
            'docblock'   => array(
                'shortDescription' => '{@inheritdoc}',
            ),
        );

        return $methodInfo;
    }

    /**
     * Collect method parameter info
     *
     * @param ReflectionParameter $parameter
     * @return array
     */
    protected function _getMethodParameterInfo(ReflectionParameter $parameter)
    {
        $parameterInfo = array(
            'name'              => $parameter->getName(),
            'passedByReference' => $parameter->isPassedByReference()
        );

        if ($parameter->isArray()) {
            $parameterInfo['type'] = 'array';
        } elseif ($parameter->getClass()) {
            $parameterInfo['type'] = $this->_getFullyQualifiedClassName($parameter->getClass()->getName());
        }

        if ($parameter->isOptional() && $parameter->isDefaultValueAvailable()) {
            $defaultValue = $parameter->getDefaultValue();
            if (is_string($defaultValue)) {
                $parameterInfo['defaultValue'] = $this->_escapeDefaultValue($parameter->getDefaultValue());
            } elseif ($defaultValue === null) {
                $parameterInfo['defaultValue'] = $this->_getNullDefaultValue();
            } else {
                $parameterInfo['defaultValue'] = $defaultValue;
            }
        }

        return $parameterInfo;
    }

    /**
     * Get default constructor definition for generated class
     *
     * @return array
     */
    protected function _getDefaultConstructorDefinition()
    {
        // public function __construct(\Magento_ObjectManager $objectManager, $instanceName, $shared = false)
        return array(
            'name'       => '__construct',
            'parameters' => array(
                array('name' => 'objectManager', 'type' => '\Magento_ObjectManager'),
                array('name' => 'instanceName'),
                array('name' => 'shared', 'defaultValue' => false),
            ),
            'body' => "\$this->_objectManager = \$objectManager;" .
                "\n\$this->_instanceName = \$instanceName;" .
                "\n\$this->_isShared = \$shared;",
            'docblock' => array(
                'shortDescription' => ucfirst(static::ENTITY_TYPE) . ' constructor',
                'tags'             => array(
                    array(
                        'name'        => 'param',
                        'description' => '\Magento_ObjectManager $objectManager'
                    ),
                    array(
                        'name'        => 'param',
                        'description' => 'string $instanceName'
                    ),
                    array(
                        'name'        => 'param',
                        'description' => 'bool $shared'
                    ),
                ),
            ),
        );
    }

    /**
     * Build proxy method body
     *
     * @param string $name
     * @param array $parameters
     * @return string
     */
    protected function _getMethodBody($name, array $parameters = array())
    {
        if (count($parameters) == 0) {
            $methodCall = sprintf('%s()', $name);
        } else {
            $methodCall = sprintf('%s(%s)', $name, implode(', ', $parameters));
        }
        return 'return $this->_getSubject()->' . $methodCall . ';';
    }
}
