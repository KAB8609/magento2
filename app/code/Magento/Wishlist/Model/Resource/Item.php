<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Wishlist
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Wishlist item model resource
 *
 * @category    Magento
 * @package     Magento_Wishlist
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Wishlist_Model_Resource_Item extends Magento_Core_Model_Resource_Db_Abstract
{
    /**
     * Initialize connection and define main table
     *
     */
    protected function _construct()
    {
        $this->_init('wishlist_item', 'wishlist_item_id');
    }

    /**
     * Load item by wishlist, product and shared stores
     *
     * @param Magento_Wishlist_Model_Item $object
     * @param int $wishlistId
     * @param int $productId
     * @param array $sharedStores
     * @return Magento_Wishlist_Model_Resource_Item
     */
    public function loadByProductWishlist($object, $wishlistId, $productId, $sharedStores)
    {
        $adapter = $this->_getReadAdapter();
        $storeWhere = $adapter->quoteInto('store_id IN (?)', $sharedStores);
        $select  = $adapter->select()
            ->from($this->getMainTable())
            ->where('wishlist_id=:wishlist_id AND '
                . 'product_id=:product_id AND '
                . $storeWhere);
        $bind = array(
            'wishlist_id' => $wishlistId,
            'product_id'  => $productId
        );
        $data = $adapter->fetchRow($select, $bind);
        if ($data) {
            $object->setData($data);
        }
        $this->_afterLoad($object);

        return $this;
    }
}