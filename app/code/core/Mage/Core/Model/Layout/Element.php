<?php

class Mage_Core_Model_Layout_Element extends Varien_Simplexml_Element
{    
    public function prepare($args)
    {
        switch ($this->getName()) {
            case 'layoutUpdate':
                break;
                
            case 'block':
                $this->prepareBlock($args);
                break;
                
            case 'reference':
                $this->prepareReference($args);
                break;
                
            case 'action':
                $this->prepareAction($args);
                break;
                
            default:
                $this->prepareActionArgument($args);
                break;
        }
        $children = $this->children();
        foreach ($this as $child) {
            $child->prepare($args);
        }
        return $this;
    }
    
    public function getBlockName()
    {
        $tagName = (string)$this->getName();
        if ('block'!==$tagName && 'reference'!==$tagName || empty($this['name'])) {
            return false;
        }
        return (string)$this['name'];
    }
    
    public function prepareBlock($args)
    {
        $type = (string)$this['type'];
        $name = (string)$this['name'];
        
        $class = Mage::getConfig()->getNode("global/blockTypes/$type")->getClassName();        
        $this->addAttribute('class', $class);
        
        $parent = $this->getParent();
        if (isset($parent['name']) && !isset($this['parent'])) {
            $this->addAttribute('parent', (string)$parent['name']);
        }
        
        return $this;
    }
    
    public function prepareReference($args)
    {
        return $this;
    }
    
    public function prepareAction($args)
    {
        $parent = $this->getParent();
        $this->addAttribute('block', (string)$parent['name']);
        
        return $this;
    }
    
    public function prepareActionArgument($args)
    {
        return $this;
    }

}