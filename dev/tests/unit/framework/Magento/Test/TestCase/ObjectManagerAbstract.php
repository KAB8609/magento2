<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Abstract class for basic object tests, such as blocks, models etc...
 *
 * @SuppressWarnings(PHPMD.NumberOfChildren)
 * @SuppressWarnings(PHPMD.numberOfChildren)
 */
abstract class Magento_Test_TestCase_ObjectManagerAbstract extends PHPUnit_Framework_TestCase
{
    /**#@+
     * Supported entities keys.
     */
    const BLOCK_ENTITY = 'block';
    const MODEL_ENTITY = 'model';
    /**#@-*/

    /**
     * List of supported entities which can be initialized
     *
     * @var array
     */
    protected $_supportedEntities = array(
        self::BLOCK_ENTITY,
        self::MODEL_ENTITY
    );

    /**
     * List of block dependencies
     *
     * @var array
     */
    protected $_blockDependencies = array(
        'request'         => 'Mage_Core_Controller_Request_Http',
        'layout'          => 'Mage_Core_Model_Layout',
        'eventManager'    => 'Mage_Core_Model_Event_Manager',
        'translator'      => 'Mage_Core_Model_Translate',
        'cache'           => 'Mage_Core_Model_Cache',
        'designPackage'   => 'Mage_Core_Model_Design_Package',
        'session'         => 'Mage_Core_Model_Session',
        'storeConfig'     => 'Mage_Core_Model_Store_Config',
        'frontController' => 'Mage_Core_Controller_Varien_Front'
    );

    /**
     * List of model dependencies
     *
     * @var array
     */
    protected $_modelDependencies = array(
        'eventDispatcher'    => 'Mage_Core_Model_Event_Manager',
        'cacheManager'       => 'Mage_Core_Model_Cache',
        'resource'           => '_getResourceModelMock',
        'resourceCollection' => 'Varien_Data_Collection_Db',
    );

    /**
     * Get block instance
     *
     * @param string $className
     * @param array $arguments
     * @return Mage_Core_Block_Abstract
     */
    public function getBlock($className, array $arguments = array())
    {
        $arguments = $this->_getConstructArguments(self::BLOCK_ENTITY, $className, $arguments);
        return $this->_getInstanceViaConstructor($className, $arguments);
    }

    /**
     * Get model instance
     *
     * @param string $className
     * @param array $arguments
     * @return Mage_Core_Model_Abstract
     */
    public function getModel($className, array $arguments = array())
    {
        $arguments = $this->_getConstructArguments(self::MODEL_ENTITY, $className, $arguments);
        return $this->_getInstanceViaConstructor($className, $arguments);
    }

    /**
     * Retrieve list of arguments that used for new block instance creation
     *
     * @param string $entityName
     * @param string $className
     * @param array $arguments
     * @throws Exception
     * @return array
     */
    protected function _getConstructArguments($entityName, $className = '', array $arguments = array())
    {
        if (!in_array($entityName, $this->_supportedEntities)) {
            throw new Exception('Unsupported entity type');
        }

        $constructArguments = array();
        $properties = '_' . $entityName . 'Dependencies';
        foreach ($this->$properties as $propertyName => $propertyType) {
            if (!isset($arguments[$propertyName])) {
                if (method_exists($this, $propertyType)) {
                    $constructArguments[$propertyName] = $this->$propertyType();
                } else {
                    $constructArguments[$propertyName] = $this->_getMockWithoutConstructorCall($propertyType);
                }
            }
        }
        $constructArguments = array_merge($constructArguments, $arguments);

        if ($className) {
            return $this->_sortConstructorArguments($className, $constructArguments);
        } else {
            return $constructArguments;
        }
    }

    /**
     * Retrieve specific mock of core resource model
     *
     * @return Mage_Core_Model_Resource_Resource|PHPUnit_Framework_MockObject_MockObject
     */
    protected function _getResourceModelMock()
    {
        /** @var $resourceMock Mage_Core_Model_Resource_Resource */
        $resourceMock = $this->getMock('Mage_Core_Model_Resource_Resource', array('getIdFieldName'),
            array(), '', false
        );
        $resourceMock->expects($this->any())
            ->method('getIdFieldName')
            ->will($this->returnValue('id'));

        return $resourceMock;
    }

    /**
     * Sort constructor arguments array as is defined for current class interface
     *
     * @param string $className
     * @param array $arguments
     * @return array
     */
    protected function _sortConstructorArguments($className, array $arguments)
    {
        $constructArguments = array();
        $method = new ReflectionMethod($className, '__construct');
        foreach ($method->getParameters() as $parameter) {
            $parameterName = $parameter->getName();
            if (isset($arguments[$parameterName])) {
                $constructArguments[$parameterName] = $arguments[$parameterName];
            } else {
                if ($parameter->isDefaultValueAvailable()) {
                    $constructArguments[$parameterName] = $parameter->getDefaultValue();
                } else {
                    $constructArguments[$parameterName] = null;
                }
            }
        }

        return $constructArguments;
    }

    /**
     * Get mock without call of original constructor
     *
     * @param string $className
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    protected function _getMockWithoutConstructorCall($className)
    {
        return $this->getMock($className, array(), array(), '', false);
    }

    /**
     * Get class instance via constructor
     *
     * @param string $className
     * @param array $arguments
     * @return object
     */
    protected function _getInstanceViaConstructor($className, array $arguments = array())
    {
        $reflectionClass = new ReflectionClass($className);
        return $reflectionClass->newInstanceArgs($arguments);
    }
}
