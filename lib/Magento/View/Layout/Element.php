<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Layout;

use Magento\View\Layout\Handle\Render\Block;
use Magento\View\Layout\Handle\Reference\Block as ReferenceBlock;

class Element extends \Magento\Simplexml\Element
{
    /**
     * Retrive the name of block
     *
     * @return bool|string
     */
    public function getBlockName()
    {
        $tagName = (string)$this->getName();
        if (empty($this['name'])
            || !in_array($tagName, array(Block::TYPE, ReferenceBlock::TYPE))) {
            return false;
        }
        return (string)$this['name'];
    }

    /**
     * Get element name
     *
     * Advanced version of getBlockName() method: gets name for container as well as for block
     *
     * @return string|bool
     */
    public function getElementName()
    {
        return $this->getName();
    }

    /**
     * Extracts sibling from 'before' and 'after' attributes
     *
     * @return string
     */
    public function getSibling()
    {
        $sibling = null;
        if ($this->getAttribute('before')) {
            $sibling = $this->getAttribute('before');
        } elseif ($this->getAttribute('after')) {
            $sibling = $this->getAttribute('after');
        }
        return $sibling;
    }

    /**
     * @param array $args
     * @return $this
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function prepareBlock($args)
    {
        $parent = $this->getParent();
        if (isset($parent['name']) && !isset($this['parent'])) {
            $this->addAttribute('parent', (string)$parent['name']);
        }
        return $this;
    }

    /**
     * @param array $args
     * @return $this
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function prepareAction($args)
    {
        $parent = $this->getParent();
        $this->addAttribute('block', (string)$parent['name']);
        return $this;
    }
}
