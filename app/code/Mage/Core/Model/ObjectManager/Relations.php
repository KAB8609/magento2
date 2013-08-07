<?php
/**
 * List of parent classes with their parents and interfaces
 *
 * {license_notice}
 * 
 * @copyright {copyright}
 * @license   {license_link}
 */
class Mage_Core_Model_ObjectManager_Relations implements Magento_ObjectManager_Relations
{
    /**
     * List of class relations
     *
     * @var array
     */
    protected $_relations;

    /**
     * Default relation list
     *
     * @var array
     */
    protected $_default = array();

    /**
     * @param array $relations
     */
    public function __construct(array $relations)
    {
        $this->_relations = $relations;
    }

    public function has($type)
    {
        return isset($this->_relations[$type]);
    }

    /**
     * Retrieve parents for class
     *
     * @param string $type
     * @return array
     */
    public function getParents($type)
    {
        return $this->_relations[$type];
    }
}
