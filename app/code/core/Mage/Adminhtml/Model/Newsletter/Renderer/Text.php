<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Template text preview field renderer
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Model_Newsletter_Renderer_Text implements Varien_Data_Form_Element_Renderer_Interface
{

    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $html = '<tr><td class="label">'."\n";
        if ($element->getLabel()) {
            $html.= '<label for="'.$element->getHtmlId().'">'.$element->getLabel().'</label>'."\n";
        }
        $html.= '</td><td class="value">
<iframe src="' . $element->getValue() . '" id="' . $element->getHtmlId() . '" frameborder="0" class="template-preview"> </iframe>';
        $html.= '</td><td></td></tr>'."\n";

        return $html;
    }
}
