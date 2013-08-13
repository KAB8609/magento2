<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Reports
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Wishlist Report collection
 *
 * @category    Magento
 * @package     Magento_Reports
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Reports_Model_Resource_Wishlist_Collection extends Magento_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Wishlist table name
     *
     * @var string
     */
    protected $_wishlistTable;

    /**
     * Resource initialization
     *
     */
    protected function _construct()
    {
        $this->_init('Magento_Wishlist_Model_Wishlist', 'Magento_Wishlist_Model_Resource_Wishlist');
        $this->setWishlistTable($this->getTable('wishlist'));
    }
    /**
     * Set wishlist table name
     *
     * @param string $value
     * @return Magento_Reports_Model_Resource_Wishlist_Collection
     */
    public function setWishlistTable($value)
    {
        $this->_wishlistTable = $value;
        return $this;
    }

    /**
     * retrieve wishlist table name
     *
     * @return string
     */
    public function getWishlistTable()
    {
        return $this->_wishlistTable;
    }

    /**
     * Retrieve wishlist customer count
     *
     * @return array
     */
    public function getWishlistCustomerCount()
    {
        /** @var $collection Mage_Customer_Model_Resource_Customer_Collection */
        $collection = Mage::getResourceModel('Mage_Customer_Model_Resource_Customer_Collection');
        
        $customersSelect = $collection->getSelectCountSql();

        $countSelect = clone $customersSelect;
        $countSelect->joinLeft(
                array('wt' => $this->getWishlistTable()),
                'wt.customer_id = e.entity_id',
                array()
            )
            ->group('wt.wishlist_id');
        $count = $collection->count();
        $resultSelect = $this->getConnection()->select()
            ->union(array($customersSelect, $count), Zend_Db_Select::SQL_UNION_ALL);
        list($customers, $count) = $this->getConnection()->fetchCol($resultSelect);

        return array(($count*100)/$customers, $count);
    }

    /**
     * Get shared items collection count
     *
     * @return int
     */
    public function getSharedCount()
    {
        /** @var $collection Mage_Customer_Model_Resource_Customer_Collection */
        $collection = Mage::getResourceModel('Mage_Customer_Model_Resource_Customer_Collection');
        $countSelect = $collection->getSelectCountSql();
        $countSelect->joinLeft(
                array('wt' => $this->getWishlistTable()),
                'wt.customer_id = e.entity_id',
                array()
            )
            ->where('wt.shared > 0')
            ->group('wt.wishlist_id');
        return $countSelect->getAdapter()->fetchOne($countSelect);
    }
}
