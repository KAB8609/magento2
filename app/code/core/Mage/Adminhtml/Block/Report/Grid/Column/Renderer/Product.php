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
 * Adminhtml Report Products Reviews renderer
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Report_Grid_Column_Renderer_Product
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * Renders grid column
     *
     * @param   Varien_Object $row
     * @return  string
     */
    public function render(Varien_Object $row)
    {
        $id   = $row->getId();

        return sprintf('<a href="%s">%s</a>',
            $this->getUrl('*/catalog_product_review/', array('productId' => $id)),
            Mage::helper('Mage_Adminhtml_Helper_Data')->__('Show Reviews')
        );
    }
}
