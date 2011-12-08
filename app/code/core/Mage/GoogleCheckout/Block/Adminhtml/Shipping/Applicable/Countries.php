<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_GoogleCheckout
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_GoogleCheckout_Block_Adminhtml_Shipping_Applicable_Countries
    extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    protected $_addRowButtonHtml = array();
    protected $_removeRowButtonHtml = array();

    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $html = parent::_getElementHtml($element);
        $html .= $this->_appendJs($element);
        return $html;
    }

    protected function _appendJs($element)
    {
        $elId = $element->getHtmlId();
        $childId = str_replace('sallowspecific', 'specificcountry', $elId);
        $html = "<script type='text/javascript'>
        var dwvie = function ()
        {
            var valueSelectId = '{$elId}';
            var elementToDisableId = '{$childId}';

            var source = $(valueSelectId);
            var target = $(elementToDisableId);

            if (source.options[source.selectedIndex].value == '0') {
                target.disabled = true;
            } else {
                target.disabled = false;
            }
        }

        Event.observe('{$elId}', 'change', dwvie);
        Event.observe(window, 'load', dwvie);
        </script>";
        return $html;
    }
}
