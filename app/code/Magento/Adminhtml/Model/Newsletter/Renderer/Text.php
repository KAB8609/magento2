<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Template text preview field renderer
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Adminhtml_Model_Newsletter_Renderer_Text implements Magento_Data_Form_Element_Renderer_Interface
{

    public function render(Magento_Data_Form_Element_Abstract $element)
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
