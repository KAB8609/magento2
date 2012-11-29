<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * System configuration structure
 */
class Mage_Backend_Model_Config_Structure implements Mage_Backend_Model_Config_Structure_SearchInterface
{
    /**
     * Key that contains field type in structure array
     */
    const TYPE_KEY = '_elementType';

    /**
     * Configuration structure represented as tree
     *
     * @var array
     */
    protected $_data;

    /**
     * Config tab iterator
     *
     * @var Mage_Backend_Model_Config_Structure_Element_Iterator_Tab
     */
    protected $_tabIterator;

    /**
     * Pool of config element flyweight objects
     *
     * @var Mage_Backend_Model_Config_Structure_Element_FlyweightFactory
     */
    protected $_flyweightFactory;

    /**
     * Provider of current config scope
     *
     * @var Mage_Backend_Model_Config_ScopeDefiner
     */
    protected $_scopeDefiner;

    /**
     * List of cached elements
     *
     * @var Mage_Backend_Model_Config_Structure_ElementInterface[]
     */
    protected $_elements;

    /**
     * @param Mage_Backend_Model_Config_Structure_Reader $structureReader
     * @param Mage_Backend_Model_Config_Structure_Element_Iterator_Tab $tabIterator
     * @param Mage_Backend_Model_Config_Structure_Element_FlyweightFactory $flyweightFactory
     * @param Mage_Backend_Model_Config_ScopeDefiner $scopeDefiner
     */
    public function __construct(
        Mage_Backend_Model_Config_Structure_Reader $structureReader,
        Mage_Backend_Model_Config_Structure_Element_Iterator_Tab $tabIterator,
        Mage_Backend_Model_Config_Structure_Element_FlyweightFactory $flyweightFactory,
        Mage_Backend_Model_Config_ScopeDefiner $scopeDefiner
    ) {
        $this->_data = $structureReader->getData();
        $this->_tabIterator = $tabIterator;
        $this->_flyweightFactory = $flyweightFactory;
        $this->_scopeDefiner = $scopeDefiner;
    }

    /**
     * Retrieve tab iterator
     *
     * @return Mage_Backend_Model_Config_Structure_Element_Iterator
     */
    public function getTabs()
    {
        foreach ($this->_data['sections'] as $sectionId => $section) {
            if (isset($section['tab']) && $section['tab']) {
                $this->_data['tabs'][$section['tab']]['children'][$sectionId] = $section;
            }
        }
        $this->_tabIterator->setElements($this->_data['tabs'], $this->_scopeDefiner->getScope());
        return $this->_tabIterator;
    }

    /**
     * Find element by path
     *
     * @param string $path
     * @return Mage_Backend_Model_Config_Structure_ElementInterface|null
     */
    public function getElement($path)
    {
        return $this->getElementByPathParts(explode('/', $path));
    }

    /**
     * Find element by path parts
     *
     * @param array $pathParts
     * @return Mage_Backend_Model_Config_Structure_ElementInterface|null
     */
    public function getElementByPathParts(array $pathParts)
    {
        $path = implode('_', $pathParts);
        if (isset($this->_elements[$path])) {
            return $this->_elements[$path];
        }
        $children = $this->_data['sections'];
        $child = array();
        foreach ($pathParts as $id) {
            if (array_key_exists($id, $children)) {
                $child = $children[$id];
                $children = array_key_exists('children', $child) ? $child['children'] : array();
            } else {
                return null;
            }
        }
        $this->_elements[$path] = $this->_flyweightFactory->create($child['_elementType']);
        $this->_elements[$path]->setData($child, $this->_scopeDefiner->getScope());
        return $this->_elements[$path];
    }

    /**
     * Retrieve paths of fields that have provided attributes with provided values
     *
     * @param string $attributeName
     * @param mixed $attributeValue
     * @return array
     */
    public function getFieldPathsByAttribute($attributeName, $attributeValue)
    {
        $result = array();
        foreach ($this->_data['sections'] as $section) {
            if (!isset($section['children'])) {
                continue;
            }
            foreach ($section['children'] as $group) {
                if (isset($group['children'])) {
                    $path = $section['id'] . '/' . $group['id'];
                    $result = $result + $this->_getGroupFieldPathsByAttribute(
                        $group['children'], $path, $attributeName, $attributeValue
                    );
                }
            }
        }
        return $result;
    }

    /**
     * Find group fields with specified attribute and attribute value
     *
     * @param array $fields
     * @param string $parentPath
     * @param string $attributeName
     * @param mixed $attributeValue
     * @return array
     */
    protected function _getGroupFieldPathsByAttribute(array $fields, $parentPath, $attributeName, $attributeValue)
    {
        $result = array();
        foreach ($fields as $field) {
            if (isset($field['children'])) {
                $result += $this->_getGroupFieldPathsByAttribute(
                    $field['children'], $parentPath . '/' . $field['id'], $attributeName, $attributeValue
                );
            } else if (isset($field[$attributeName]) && $field[$attributeName] == $attributeValue) {
                $result[] = $parentPath . '/' . $field['id'];
            }
        }
        return $result;
    }
}
