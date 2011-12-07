<?php
/**
 * {license_notice}
 *
 * @category   Varien
 * @package    Varien_Data
 * @copyright  {copyright}
 * @license    {license_link}
 */


/**
 * Data tree
 *
 * @category   Varien
 * @package    Varien_Data
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Varien_Data_Tree
{

    /**
     * Nodes collection
     *
     * @var Varien_Data_Tree_Node_Collection
     */
    protected $_nodes;

    /**
     * Enter description here...
     *
     */
    public function __construct()
    {
        $this->_nodes = new Varien_Data_Tree_Node_Collection($this);
    }

    /**
     * Enter description here...
     *
     * @return Varien_Data_Tree
     */
    public function getTree()
    {
        return $this;
    }

    /**
     * Enter description here...
     *
     * @param Varien_Data_Tree_Node $parentNode
     */
    public function load($parentNode=null)
    {
    }

    /**
     * Enter description here...
     *
     * @param unknown_type $nodeId
     */
    public function loadNode($nodeId)
    {
    }

    /**
     * Enter description here...
     *
     * @param array|Varien_Data_Tree_Node $data
     * @param Varien_Data_Tree_Node $parentNode
     * @param Varien_Data_Tree_Node $prevNode
     * @return Varien_Data_Tree_Node
     */
    public function appendChild($data=array(), $parentNode, $prevNode=null)
    {
        if (is_array($data)) {
            $node = $this->addNode(
                new Varien_Data_Tree_Node($data, $parentNode->getIdField(), $this),
                $parentNode
            );
        } elseif ($data instanceof Varien_Data_Tree_Node) {
            $node = $this->addNode($data, $parentNode);
        }
        return $node;
    }

    /**
     * Enter description here...
     *
     * @param Varien_Data_Tree_Node $node
     * @param Varien_Data_Tree_Node $parent
     * @return Varien_Data_Tree_Node
     */
    public function addNode($node, $parent=null)
    {
        $this->_nodes->add($node);
        $node->setParent($parent);
        if (!is_null($parent) && ($parent instanceof Varien_Data_Tree_Node) ) {
            $parent->addChild($node);
        }
        return $node;
    }

    /**
     * Enter description here...
     *
     * @param Varien_Data_Tree_Node $node
     * @param Varien_Data_Tree_Node $parentNode
     * @param Varien_Data_Tree_Node $prevNode
     */
    public function moveNodeTo($node, $parentNode, $prevNode=null)
    {
    }

    /**
     * Enter description here...
     *
     * @param Varien_Data_Tree_Node $node
     * @param Varien_Data_Tree_Node $parentNode
     * @param Varien_Data_Tree_Node $prevNode
     */
    public function copyNodeTo($node, $parentNode, $prevNode=null)
    {
    }

    /**
     * Enter description here...
     *
     * @param Varien_Data_Tree_Node $node
     * @return Varien_Data_Tree
     */
    public function removeNode($node)
    {
        $this->_nodes->delete($node);
        if ($node->getParent()) {
            $node->getParent()->removeChild($node);
        }
        unset($node);
        return $this;
    }

    /**
     * Enter description here...
     *
     * @param Varien_Data_Tree_Node $parentNode
     * @param Varien_Data_Tree_Node $prevNode
     */
    public function createNode($parentNode, $prevNode=null)
    {
    }

    /**
     * Enter description here...
     *
     * @param Varien_Data_Tree_Node $node
     */
    public function getChild($node)
    {
    }

    /**
     * Enter description here...
     *
     * @param Varien_Data_Tree_Node $node
     */
    public function getChildren($node)
    {
    }

    /**
     * Enter description here...
     *
     * @return Varien_Data_Tree_Node_Collection
     */
    public function getNodes()
    {
        return $this->_nodes;
    }

    /**
     * Enter description here...
     *
     * @param unknown_type $nodeId
     * @return Varien_Data_Tree_Node
     */
    public function getNodeById($nodeId)
    {
        return $this->_nodes->searchById($nodeId);
    }

    /**
     * Enter description here...
     *
     * @param Varien_Data_Tree_Node $node
     * @return array
     */
    public function getPath($node)
    {
        if ($node instanceof Varien_Data_Tree_Node ) {

        } elseif (is_numeric($node)){
            if ($_node = $this->getNodeById($node)) {
                return $_node->getPath();
            }
        }
        return array();
    }

}
