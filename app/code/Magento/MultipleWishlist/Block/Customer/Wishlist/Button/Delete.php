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
 * Wishlist delete button
 *
 * @category    Magento
 * @package     Magento_MultipleWishlist
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_MultipleWishlist_Block_Customer_Wishlist_Button_Delete extends Magento_Wishlist_Block_Abstract
{
    /**
     * Build block html
     *
     * @return string
     */
    protected function _toHtml()
    {
        if (Mage::helper('Magento_MultipleWishlist_Helper_Data')->isMultipleEnabled() && $this->isWishlistDeleteable()) {
            return parent::_toHtml();
        }
        return '';
    }

    /**
     * Check whether current wishlist can be deleted
     *
     * @return bool
     */
    protected function isWishlistDeleteable()
    {
        return !Mage::helper('Magento_MultipleWishlist_Helper_Data')->isWishlistDefault($this->getWishlistInstance());
    }

    /**
     * Build wishlist deletion url
     *
     * @return string
     */
    public function getDeleteUrl()
    {
        return $this->getUrl('wishlist/index/deletewishlist', array('wishlist_id' => '%item%'));
    }

    /**
     * Retrieve url to redirect customer to after wishlist is deleted
     *
     * @return string
     */
    public function getRedirectUrl()
    {
        return $this->getUrl('wishlist/index/index');
    }
}