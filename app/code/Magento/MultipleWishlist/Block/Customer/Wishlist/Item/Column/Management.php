<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_MultipleWishlist
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Wishlist item management column (copy, move, etc.)
 *
 * @category    Magento
 * @package     Magento_MultipleWishlist
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\MultipleWishlist\Block\Customer\Wishlist\Item\Column;

class Management
    extends \Magento\Wishlist\Block\Customer\Wishlist\Item\Column
{
    /**
     * Render block
     *
     * @return bool
     */
    public function isEnabled()
    {
        return \Mage::helper('Magento\MultipleWishlist\Helper\Data')->isMultipleEnabled();
    }

    /**
     * Retrieve current customer wishlist collection
     *
     * @return \Magento\Wishlist\Model\Resource\Wishlist\Collection
     */
    public function getWishlists()
    {
        return \Mage::helper('Magento\MultipleWishlist\Helper\Data')->getCustomerWishlists();
    }

    /**
     * Retrieve default wishlist for current customer
     *
     * @return \Magento\Wishlist\Model\Wishlist
     */
    public function getDefaultWishlist()
    {
        return \Mage::helper('Magento\MultipleWishlist\Helper\Data')->getDefaultWishlist();
    }

    /**
     * Retrieve current wishlist
     *
     * @return \Magento\Wishlist\Model\Wishlist
     */
    public function getCurrentWishlist()
    {
        return \Mage::helper('Magento\Wishlist\Helper\Data')->getWishlist();
    }

    /**
     * Check whether user multiple wishlist limit reached
     *
     * @param \Magento\Wishlist\Model\Resource\Wishlist\Collection $wishlists
     * @return bool
     */
    public function canCreateWishlists(\Magento\Wishlist\Model\Resource\Wishlist\Collection $wishlists)
    {
        $customerId = \Mage::getSingleton('Magento\Customer\Model\Session')->getCustomerId();
        return !\Mage::helper('Magento\MultipleWishlist\Helper\Data')->isWishlistLimitReached($wishlists) && $customerId;
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
