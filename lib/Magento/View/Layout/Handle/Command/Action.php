<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Layout\Handle\Command;

use Magento\View\LayoutInterface;
use Magento\View\Layout\Element;
use Magento\View\Layout\Handle\CommandInterface;

class Action implements CommandInterface
{
    /**
     * Container type
     */
    const TYPE = 'action';

    /**
     * @var int
     */
    private $inc = 0;

    /**
     * Core store config
     *
     * @deprecated
     * @var \Magento\Core\Model\Store\Config
     */
    protected $coreStoreConfig;

    public function __construct(\Magento\Core\Model\Store\Config $coreStoreConfig)
    {
        $this->coreStoreConfig = $coreStoreConfig;
    }
    /**
     * @param Element $layoutElement
     * @param LayoutInterface $layout
     * @param string $parentName
     * @return Action
     */
    public function parse(Element $layoutElement, LayoutInterface $layout, $parentName)
    {
        $element = array();
        foreach ($layoutElement->attributes() as $attributeName => $attribute) {
            if ($attribute) {
                $element[$attributeName] = (string)$attribute;
            }
        }

        $ifConfig = isset($element['ifconfig']) ? $element['ifconfig'] : null;
        if (!empty($ifConfig) && !$this->coreStoreConfig->getConfigFlag($ifConfig)) {
            return $this;
        }

        $element['type'] = self::TYPE;
        $elementName = isset($element['name']) ? $element['name'] : ('Command-Action-' . $this->inc++);

        $arguments = array();
        foreach ($layoutElement as $argument) {
            /** @var $argument Element */
            $name = $argument->getAttribute('name');
            $value = (string) $argument;
            $arguments[$name] = $value;
        }
        $element['arguments'] = $arguments;

        $layout->addElement($elementName, $element);

        if (isset($parentName)) {
            $layout->setChild($parentName, $elementName, $elementName);
        }

        return $this;
    }

    /**
     * @param array $element
     * @param LayoutInterface $layout
     * @param string $parentName
     * @return Action
     */
    public function register(array $element, LayoutInterface $layout, $parentName)
    {
        $method = isset($element['method']) ? $element['method'] : null;

        if (isset($method) && isset($parentName)) {
            $arguments = isset($element['arguments']) ? $element['arguments'] : array();
            $block = $layout->getBlock($parentName);
            if (isset($block)) {
                call_user_func_array(array($block, $method), $arguments);
            }
        }

        $alias = $layout->getChildAlias($parentName, $element['name']);
        $layout->unsetChild($parentName, $alias);

        return $this;
    }
}
