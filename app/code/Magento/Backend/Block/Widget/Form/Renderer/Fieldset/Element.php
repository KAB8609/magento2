<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Fieldset element renderer
 *
 * @category   Mage
 * @package    Magento_Backend
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Backend_Block_Widget_Form_Renderer_Fieldset_Element extends Magento_Backend_Block_Template
    implements Magento_Data_Form_Element_Renderer_Interface
{
    protected $_element;

    protected $_template = 'Magento_Backend::widget/form/renderer/fieldset/element.phtml';

    public function getElement()
    {
        return $this->_element;
    }

    public function render(Magento_Data_Form_Element_Abstract $element)
    {
        $this->_element = $element;
        return $this->toHtml();
    }
}
