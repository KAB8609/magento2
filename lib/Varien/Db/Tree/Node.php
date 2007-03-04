<?php

require_once 'Varien/Db/Tree/Node/Exception.php';

class Varien_Db_Tree_Node {

    private $left;
    private $right;
    private $id;
    private $pid;
    private $level;
    private $title;
    private $data;


    public $hasChild = false;
    public $numChild = 0;


    function __construct($nodeData = array(), $keys) {
        if (empty($nodeData)) {
            throw new Varien_Db_Tree_Node_Exception('Empty array of node information');
        }
        if (empty($keys)) {
            throw new Varien_Db_Tree_Node_Exception('Empty keys array');
        }

        $this->id    = $nodeData[$keys['id']];
        $this->pid   = $nodeData[$keys['pid']];
        $this->left  = $nodeData[$keys['left']];
        $this->right = $nodeData[$keys['right']];
        $this->level = $nodeData[$keys['level']];

        $this->data  = $nodeData;
        $a = $this->right - $this->left;
        if ($a > 1) {
            $this->hasChild = true;
            $this->numChild = ($a - 1) / 2;
        }
        return $this;
    }

    function getData($name) {
        if (isset($this->data[$name])) {
            return $this->data[$name];
        } else {
            return null;
        }
    }

    function getLevel() {
        return $this->level;
    }

    function getLeft() {
        return $this->left;
    }

    function getRight() {
        return $this->right;
    }

    function getPid() {
        return $this->pid;
    }

    function getId() {
        return $this->id;
    }
    
    /**
     * Return true if node have chield
     *
     * @return boolean
     */
    function isParent() {
        if ($this->right - $this->left > 1) {
            return true;
        } else {
            return false;
        }
    }
}