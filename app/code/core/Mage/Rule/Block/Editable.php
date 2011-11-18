<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Rule
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Mage_Rule_Block_Editable extends Mage_Core_Block_Abstract
    implements Varien_Data_Form_Element_Renderer_Interface
{
    /**
     * Render element
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @see Varien_Data_Form_Element_Renderer_Interface::render()
     * @return string
     */
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $element->addClass('element-value-changer');
        $valueName = $element->getValueName();

        if ($valueName === '') {
            $valueName = '...';
        }

        $coreHelper = Mage::helper('Mage_Core_Helper_Data');
        $stringHelper = Mage::helper('Mage_Core_Helper_String');

        if ($element->getShowAsText()) {
            $html = ' <input type="hidden" class="hidden" id="' . $element->getHtmlId()
                . '" name="' . $element->getName() . '" value="' . $element->getValue() . '"/> '
                . htmlspecialchars($valueName) . '&nbsp;';
        } else {
            $html = ' <span class="rule-param"'
                . ($element->getParamId() ? ' id="' . $element->getParamId() . '"' : '') . '>'
                . '<a href="javascript:void(0)" class="label">';

            $translate = Mage::getSingleton('Mage_Core_Model_Translate_Inline');

            $html .= $translate->isAllowed() ? $coreHelper->escapeHtml($valueName) :
                $coreHelper->escapeHtml($stringHelper->truncate($valueName, 33, '...'));

            $html .= '</a><span class="element"> ' . $element->getElementHtml();

            if ($element->getExplicitApply()) {
                $html .= ' <a href="javascript:void(0)" class="rule-param-apply"><img src="'
                    . $this->getSkinUrl('images/rule_component_apply.gif') . '" class="v-middle" alt="'
                    . $this->__('Apply') . '" title="' . $this->__('Apply') . '" /></a> ';
            }

            $html .= '</span></span>&nbsp;';
        }

        return $html;
    }
}
