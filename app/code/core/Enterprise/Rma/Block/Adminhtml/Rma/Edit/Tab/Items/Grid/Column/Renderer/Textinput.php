<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Rma
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Grid column widget for rendering cells, which can be of text or select type
 *
 * @category    Enterprise
 * @package     Enterprise_Rma
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Rma_Block_Adminhtml_Rma_Edit_Tab_Items_Grid_Column_Renderer_Textinput
    extends Enterprise_Rma_Block_Adminhtml_Rma_Edit_Tab_Items_Grid_Column_Renderer_Abstract
{
    /**
     * Renders quantity as integer
     *
     * @param Varien_Object $row
     * @return int|string
     */
    public function _getValue(Varien_Object $row)
    {
        $quantity = parent::_getValue($row);
        if (!$row->getIsQtyDecimal()) {
            $quantity = intval($quantity);
        }
        return $quantity;
    }

    /**
     * Renders column as input when it is editable
     *
     * @param   Varien_Object $row
     * @return  string
     */
    protected function _getEditableView(Varien_Object $row)
    {
        $value = $row->getData($this->getColumn()->getIndex());
        if (!$row->getIsQtyDecimal() && !is_null($value)) {
            $value = intval($value);
        }
        $class = 'input-text ' . $this->getColumn()->getValidateClass();
        $html = '<input type="text" ';
        $html .= 'name="items[' . $row->getId() . '][' . $this->getColumn()->getId() . ']" ';
        $html .= 'value="' . $value . '" ';
        if ($this->getStatusManager()->getAttributeIsDisabled($this->getColumn()->getId())) {
            $html .= ' disabled="disabled" ';
            $class .= ' disabled ';
        }
        $html .= 'class="' . $class . '" />';
        return $html;
    }
}
