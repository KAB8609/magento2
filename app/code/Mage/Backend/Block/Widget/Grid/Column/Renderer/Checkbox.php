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
 * Grid checkbox column renderer
 *
 * @category   Mage
 * @package    Mage_Backend
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Backend_Block_Widget_Grid_Column_Renderer_Checkbox
    extends Mage_Backend_Block_Widget_Grid_Column_Renderer_Abstract
{
    protected $_defaultWidth = 55;
    protected $_values;

    /**
     * @var Mage_Backend_Block_Widget_Grid_Column_Renderer_Options_Converter
     */
    protected $_converter;

    /**
     * @param Mage_Backend_Block_Context $context
     * @param Mage_Backend_Block_Widget_Grid_Column_Renderer_Options_Converter $converter
     * @param array $data
     */
    public function __construct(
        Mage_Backend_Block_Context $context,
        Mage_Backend_Block_Widget_Grid_Column_Renderer_Options_Converter $converter,
        array $data = array()
    ) {
        parent::__construct($context, $data);
        $this->_converter = $converter;
    }

    /**
     * Returns values of the column
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
     * Prepare data for renderer
     *
     * @return array
     */
    protected function _getValues()
    {
        $values = $this->getColumn()->getValues();
        return $this->_converter->toFlatArray($values);
    }

    /**
     * Renders grid column
     *
     * @param   Magento_Object $row
     * @return  string
     */
    public function render(Magento_Object $row)
    {
        $values = $this->_getValues();
        $value  = $row->getData($this->getColumn()->getIndex());
        if (is_array($values)) {
            $checked = in_array($value, $values) ? ' checked="checked"' : '';
        } else {
            $checked = ($value === $this->getColumn()->getValue()) ? ' checked="checked"' : '';
        }

        $disabledValues = $this->getColumn()->getDisabledValues();
        if (is_array($disabledValues)) {
            $disabled = in_array($value, $disabledValues) ? ' disabled="disabled"' : '';
        } else {
            $disabled = ($value === $this->getColumn()->getDisabledValue()) ? ' disabled="disabled"' : '';
        }

        $this->setDisabled($disabled);

        if ($this->getNoObjectId() || $this->getColumn()->getUseIndex()) {
            $v = $value;
        } else {
            $v = ($row->getId() != "") ? $row->getId():$value;
        }

        return $this->_getCheckboxHtml($v, $checked);
    }

    /**
     * @param string $value   Value of the element
     * @param bool   $checked Whether it is checked
     * @return string
     */
    protected function _getCheckboxHtml($value, $checked)
    {
        $html = '<input type="checkbox" ';
        $html .= 'name="' . $this->getColumn()->getFieldName() . '" ';
        $html .= 'value="' . $this->escapeHtml($value) . '" ';
        $html .= 'class="'. ($this->getColumn()->getInlineCss() ? $this->getColumn()->getInlineCss() : 'checkbox') .'"';
        $html .= $checked . $this->getDisabled() . '/>';
        return $html;
    }

    /**
     * Renders header of the column
     *
     * @return string
     */
    public function renderHeader()
    {
        if ($this->getColumn()->getHeader()) {
            return parent::renderHeader();
        }

        $checked = '';
        if ($filter = $this->getColumn()->getFilter()) {
            $checked = $filter->getValue() ? ' checked="checked"' : '';
        }

        $disabled = '';
        if ($this->getColumn()->getDisabled()) {
            $disabled = ' disabled="disabled"';
        }
        $html = '<input type="checkbox" ';
        $html .= 'name="' . $this->getColumn()->getFieldName() . '" ';
        $html .= 'onclick="' . $this->getColumn()->getGrid()->getJsObjectName() . '.checkCheckboxes(this)" ';
        $html .= 'class="checkbox"' . $checked . $disabled . ' ';
        $html .= 'title="'.__('Select All') . '"/>';
        return $html;
    }
}
