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
 * Adminhtml catalog super product link grid checkbox renderer
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Super_Config_Grid_Renderer_Checkbox extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Checkbox
{
    /**
     * Renders grid column
     *
     * @param   Magento_Object $row
     * @return  string
     */
    public function render(Magento_Object $row)
    {
        $result = parent::render($row);
        return $result.'<input type="hidden" class="value-json" value="'.htmlspecialchars($this->getAttributesJson($row)).'" />';
    }

    public function getAttributesJson(Magento_Object $row)
    {
        if(!$this->getColumn()->getAttributes()) {
            return '[]';
        }

        $result = array();
        foreach($this->getColumn()->getAttributes() as $attribute) {
            $productAttribute = $attribute->getProductAttribute();
            if($productAttribute->getSourceModel()) {
                $label = $productAttribute->getSource()->getOptionText($row->getData($productAttribute->getAttributeCode()));
            } else {
                $label = $row->getData($productAttribute->getAttributeCode());
            }
            $item = array();
            $item['label']        = $label;
            $item['attribute_id'] = $productAttribute->getId();
            $item['value_index']  = $row->getData($productAttribute->getAttributeCode());
            $result[] = $item;
        }

        return Mage::helper('Mage_Core_Helper_Data')->jsonEncode($result);
    }
}// Class Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Super_Config_Grid_Renderer_Checkbox END
