<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Rma
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Enterprise_Rma_Block_Adminhtml_Product_Bundle_Product
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Text
{
    /**
     * Render product name to add Configure link
     *
     * @param   Varien_Object $row
     * @return  string
     */
    public function render(Varien_Object $row)
    {
        $rendered       =  parent::render($row);
        $link           = '';
        if ($row->getProductType() == Mage_Catalog_Model_Product_Type::TYPE_BUNDLE) {
            $link = sprintf(
                '<a href="javascript:void(0)" class="product_to_add" id="productId_%s">%s</a>',
                $row->getId(),
                Mage::helper('Enterprise_Rma_Helper_Data')->__('Select Items')
            );
        }
        return $rendered.$link;
    }
}
