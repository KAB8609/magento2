<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Checkout
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml grid product qty column renderer
 *
 * @category   Enterprise
 * @package    Enterprise_Checkout
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Checkout_Block_Adminhtml_Manage_Grid_Renderer_Wishlist_Qty
    extends Mage_Adminhtml_Block_Sales_Order_Create_Search_Grid_Renderer_Qty
{
    /**
     * Returns whether this qty field must be inactive
     *
     * @param   Magento_Object $row
     * @return  bool
     */
    protected function _isInactive($row)
    {
        return $row->getProduct()->getTypeId() == Mage_Catalog_Model_Product_Type_Grouped::TYPE_CODE;
    }
}
