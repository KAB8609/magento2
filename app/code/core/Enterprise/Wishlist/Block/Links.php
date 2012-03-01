<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Wishlist
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Links block
 *
 * @category    Enterprise
 * @package     Enterprise_Wishlist
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Wishlist_Block_Links extends Mage_Wishlist_Block_Links
{
    /**
     * Count items in wishlist
     *
     * @return int
     */
    protected function _getItemCount()
    {
        return $this->helper('Enterprise_Wishlist_Helper_Data')->getItemCount();
    }

    /**
     * Create Button label
     *
     * @param int $count
     * @return string
     */
    protected function _createLabel($count)
    {
        if (Mage::helper('Enterprise_Wishlist_Helper_Data')->isMultipleEnabled()) {
            if ($count > 1) {
                return $this->__('My Wishlists (%d items)', $count);
            } else if ($count == 1) {
                return $this->__('My Wishlists (%d item)', $count);
            } else {
                return $this->__('My Wishlists');
            }
        } else {
            return parent::_createLabel($count);
        }
    }
}
