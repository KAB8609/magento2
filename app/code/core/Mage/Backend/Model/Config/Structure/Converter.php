<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Backend_Model_Config_Structure_Converter
{
    /**
     * @var Mage_Backend_Model_Config_Structure_Mapper_Factory
     */
    protected $_mapperFactory;

    /**
     * Mapper type list
     *
     * @var array
     */
    protected $_mapperList = array(
        Mage_Backend_Model_Config_Structure_Mapper_Factory::MAPPER_PATH,
        Mage_Backend_Model_Config_Structure_Mapper_Factory::MAPPER_DEPENDENCIES,
        Mage_Backend_Model_Config_Structure_Mapper_Factory::MAPPER_ATTRIBUTE_INHERITANCE,
        Mage_Backend_Model_Config_Structure_Mapper_Factory::MAPPER_IGNORE,
        Mage_Backend_Model_Config_Structure_Mapper_Factory::MAPPER_SORTING,
    );

    /**
     * @param Mage_Backend_Model_Config_Structure_Mapper_Factory $mapperFactory
     */
    public function __construct(Mage_Backend_Model_Config_Structure_Mapper_Factory $mapperFactory)
    {
        $this->_mapperFactory = $mapperFactory;
    }

    /**
     * Map of single=>plural sub-node names per node
     *
     * E.G. first element makes all 'tab' nodes be renamed to 'tabs' in system node.
     *
     * @var array
     */
    protected $nameMap = array(
        'system' => array('tab' => 'tabs', 'section'=> 'sections'),
        'section' => array('group' => 'children'),
        'group' => array('field' => 'children', 'group' => 'children'),
        'depends' => array('field' => 'fields'),
    );

    /**
     * Retrieve DOMDocument as array
     *
     * @param DOMNode $root
     * @return mixed
     */
    public function convert(DOMNode $root)
    {
        $result = $this->_convertDOMDocument($root);

        foreach ($this->_mapperList as $type) {
            /** @var $mapper Mage_Backend_Model_Config_Structure_MapperInterface */
            $mapper = $this->_mapperFactory->create($type);
            $result = $mapper->map($result);
        }

        return $result;
    }

    /**
     * Retrieve DOMDocument as array
     *
     * @param DOMNode $root
     * @return mixed
     */
    protected function _convertDOMDocument(DOMNode $root)
    {
        $result = $this->_processAttributes($root);

        $children = $root->childNodes;

        $processedSubLists = array();
        for ($i = 0; $i < $children->length; $i++) {
            $child = $children->item($i);
            $childName = $child->nodeName;
            $convertedChild = array();

            switch ($child->nodeType) {
                case XML_COMMENT_NODE:
                    continue 2;
                    break;

                case XML_TEXT_NODE:
                    if ($children->length && trim($child->nodeValue, "\n ") === '') {
                        continue 2;
                    }
                    $childName = 'value';
                    $convertedChild = $child->nodeValue;
                    break;

                case XML_CDATA_SECTION_NODE:
                    $childName = 'value';
                    $convertedChild = $child->nodeValue;
                    break;

                default:
                    /** @var $child DOMElement */
                    if ($childName == 'attribute') {
                        $childName = $child->getAttribute('type');
                    }
                    $convertedChild = $this->_convertDOMDocument($child);
                    break;
            }

            if (array_key_exists($root->nodeName, $this->nameMap)
                && array_key_exists($child->nodeName, $this->nameMap[$root->nodeName])) {
                $childName = $this->nameMap[$root->nodeName][$child->nodeName];
                $processedSubLists[] = $childName;
                $convertedChild['_elementType'] = $child->nodeName;
            }

            if (in_array($childName, $processedSubLists)) {
                if (is_array($convertedChild) && array_key_exists('id', $convertedChild)) {
                    $result[$childName][$convertedChild['id']] = $convertedChild;
                } else {
                    $result[$childName][] = $convertedChild;
                }
            } else if (array_key_exists($childName, $result)) {
                $result[$childName] = array($result[$childName], $convertedChild);
                $processedSubLists[] = $childName;
            } else {
                $result[$childName] = $convertedChild;
            }
        }

        if (count($result) == 1 && array_key_exists('value', $result)) {
            $result = $result['value'];
        }

        return $result;
    }

    /**
     * Process element attributes
     * 
     * @param DOMNode $root
     * @return array
     */
    protected function _processAttributes(DOMNode $root)
    {
        $result = array();

        if ($root->hasAttributes()) {
            $attributes = $root->attributes;
            foreach ($attributes as $attribute) {
                if ($root->nodeName == 'attribute' && $attribute->name == 'type') {
                    continue;
                }
                $result[$attribute->name] = $attribute->value;
            }
            return $result;
        }
        return $result;
    }

    /**
     * Sort sections/tabs
     *
     * @param mixed $a
     * @param mixed $b
     * @return int
     */
    protected function _sort($a, $b)
    {
        $aSortOrder = isset($a['sortOrder']) ? (int)$a['sortOrder'] : 0;
        $bSortOrder = isset($b['sortOrder']) ? (int)$b['sortOrder'] : 0;
        return $aSortOrder < $bSortOrder ? -1 : ($aSortOrder > $bSortOrder ? 1 : 0);
    }
}
