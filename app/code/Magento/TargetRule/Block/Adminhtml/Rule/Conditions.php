<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_TargetRule
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Magento_TargetRule_Block_Adminhtml_Rule_Conditions implements \Magento\Data\Form\Element\Renderer\RendererInterface
{
    public function render(\Magento\Data\Form\Element\AbstractElement $element)
    {
        if ($element->getRule() && $element->getRule()->getConditions()) {
            return $element->getRule()->getConditions()->asHtmlRecursive();
        }
        return '';
    }
}
