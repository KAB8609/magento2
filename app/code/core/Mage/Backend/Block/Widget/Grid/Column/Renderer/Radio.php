<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Grid radiogroup column renderer
 *
 * @category   Mage
 * @package    Mage_Backend
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Backend_Block_Widget_Grid_Column_Renderer_Radio
    extends Mage_Backend_Block_Widget_Grid_Column_Renderer_Abstract
{
    protected $_defaultWidth = 55;
    protected $_values;

    /**
     * Returns all values for the column
     *
     * @return array
     */
    public function getValues()
    {
        if (is_null($this->_values)) {
            $this->_values = $this->getColumn()->getData('values') ? $this->getColumn()->getData('values') : array();
        }
        return $this->_values;
    }
    /**
     * Renders grid column
     *
     * @param   Varien_Object $row
     * @return  string
     */
    public function render(Varien_Object $row)
    {
        $values = $this->getColumn()->getValues();
        $value  = $row->getData($this->getColumn()->getIndex());
        if (is_array($values)) {
            $checked = in_array($value, $values) ? ' checked="checked"' : '';
        } else {
            $checked = ($value === $this->getColumn()->getValue()) ? ' checked="checked"' : '';
        }
        $html = '<input type="radio" name="' . $this->getColumn()->getHtmlName() . '" ';
        $html .= 'value="' . $row->getId() . '" class="radio"' . $checked . '/>';
        return $html;
    }
}
