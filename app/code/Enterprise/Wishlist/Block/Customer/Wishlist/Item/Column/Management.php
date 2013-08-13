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
 * Wishlist item management column (copy, move, etc.)
 *
 * @category    Enterprise
 * @package     Enterprise_Wishlist
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Wishlist_Block_Customer_Wishlist_Item_Column_Management
    extends Magento_Wishlist_Block_Customer_Wishlist_Item_Column
{
    /**
     * Render block
     *
     * @return bool
     */
    public function isEnabled()
    {
        return Mage::helper('Enterprise_Wishlist_Helper_Data')->isMultipleEnabled();
    }

    /**
     * Retrieve current customer wishlist collection
     *
     * @return Magento_Wishlist_Model_Resource_Wishlist_Collection
     */
    public function getWishlists()
    {
        return Mage::helper('Enterprise_Wishlist_Helper_Data')->getCustomerWishlists();
    }

    /**
     * Retrieve default wishlist for current customer
     *
     * @return Magento_Wishlist_Model_Wishlist
     */
    public function getDefaultWishlist()
    {
        return Mage::helper('Enterprise_Wishlist_Helper_Data')->getDefaultWishlist();
    }

    /**
     * Retrieve current wishlist
     *
     * @return Magento_Wishlist_Model_Wishlist
     */
    public function getCurrentWishlist()
    {
        return Mage::helper('Magento_Wishlist_Helper_Data')->getWishlist();
    }

    /**
     * Check whether user multiple wishlist limit reached
     *
     * @param Magento_Wishlist_Model_Resource_Wishlist_Collection $wishlists
     * @return bool
     */
    public function canCreateWishlists(Magento_Wishlist_Model_Resource_Wishlist_Collection $wishlists)
    {
        $customerId = Mage::getSingleton('Mage_Customer_Model_Session')->getCustomerId();
        return !Mage::helper('Enterprise_Wishlist_Helper_Data')->isWishlistLimitReached($wishlists) && $customerId;
    }

    /**
     * Get wishlist item copy url
     *
     * @return string
     */
    public function getCopyItemUrl()
    {
        return $this->getUrl('wishlist/index/copyitem');
    }
}
