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
 * Multiple wishlist item resource collection
 *
 * @category    Magento
 * @package     Magento_MultipleWishlist
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_MultipleWishlist_Model_Resource_Item_Collection_Updater
    implements Magento_Core_Model_Layout_Argument_UpdaterInterface
{
    /**
     * Add filtration by customer id
     *
     * @param Magento_Data_Collection_Db $argument
     * @return mixed
     */
    public function update($argument)
    {
        $adapter = $argument->getConnection();
        $defaultWishlistName = Mage::helper('Magento_Wishlist_Helper_Data')->getDefaultWishlistName();
        $argument->getSelect()->columns(
            array('wishlist_name' => $adapter->getIfNullSql('wishlist.name', $adapter->quote($defaultWishlistName)))
        );

        $argument->addFilterToMap(
            'wishlist_name', $adapter->getIfNullSql('wishlist.name', $adapter->quote($defaultWishlistName))
        );
        return $argument;
    }
}