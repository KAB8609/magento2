<?php

class Mage_Core_Layout extends Varien_Simplexml_Config
{
    const SIMPLEXML_CLASS = 'Mage_Core_Layout_Object';
    
    public function init($id)
    {
        $this->setCacheDir(Mage::getBaseDir('var').DS.'cache'.DS.'layout');
        $this->setCacheKey($id);
        
        if ($xml = $this->loadCache()) {
            $this->setXml($xml);
        } else {
            $this->setXml('<layout/>');
        }
    }
    
    public function loadUpdate($args)
    {
        $fileName = (string)$args->file;
        $moduleName = (string)$args->module;
        $fileName = Mage::getBaseDir('layout', $moduleName).DS.$fileName;
        $this->addCacheStat($fileName);
        $update = $this->loadFile($fileName);
        $update->prepare($args);
        foreach ($update as $child) {
            $this->getXml()->appendChild($child);
        }
    }
 
    public function loadUpdatesFromConfig($area, $id)
    {
        $layoutConfig = Mage::getConfig('/')->global->$area->layouts->$id;
        if (!empty($layoutConfig)) {
            $updates = $layoutConfig->updates->children();
            foreach ($updates as $update) {
                $this->loadUpdate($update);
            }
        }
        return false;
    }
    
    public function createBlocks($parent=null)
    {
        if (empty($parent)) {
            $parent = $this->getXml();
        }
        foreach ($parent as $node) {
            switch ($node->getName()) {
                case 'block':
                    $className = (string)$node['class'];
                    $blockName = (string)$node['name'];
                    $block = Mage_Core_Block::addBlock($className, $blockName);
                    
                    if (!empty($node['parent'])) {
                        $parentName = (string)$node['parent'];
                        $parent = Mage_Core_Block::getBlockByName($parentName);
                        
                        if (isset($node['as'])) {
                            $as = (string)$node['as'];
                            $parent->setChild($as, $block);
                        } elseif (isset($node['before'])) {
                            $sibling = (string)$node['before'];
                            if ('-'===$sibling) {
                                $sibling = '';
                            }
                            $parent->insert($block, $sibling);
                        } elseif (isset($node['after'])) {
                            $sibling = (string)$node['after'];
                            if ('-'===$sibling) {
                                $sibling = '';
                            }
                            $parent->insert($block, $sibling, true);
                        } else {
                            $parent->append($block);
                        }
                    }
                    if (!empty($node['output'])) {
                        $method = (string)$node['output'];
                        Mage_Core_Block::addOutputBlock($blockName, $method);
                    }
                    $this->createBlocks($node);
                    break;
                    
                case 'reference':
                    $this->createBlocks($node);
                    break;

                case 'action':
                    $name = (string)$node['block'];
                    $block = Mage_Core_Block::getBlockByName($name);
                    $method = (string)$node['method'];
                    $args = (array)$node->children();
                    unset($args['@attributes']);
                    if (isset($node['json'])) {
                        $json = explode(',', (string)$node['json']);
                        foreach ($json as $arg) {
                            $args[$arg] = Zend_Json::decode($args[$arg]);
                        }
                    }
#echo "<hr><pre>".$name."::".$method." / "; print_r($args); echo "</pre>";
                    call_user_func_array(array($block, $method), $args);
                    break;
            }
        }
    }
    
}