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
 * Wishlist item collection filtered by customer
 */
class Magento_MultipleWishlist_Model_Item_Collection extends Magento_MultipleWishlist_Model_Resource_Item_Collection
{
    /**
     * Core registry
     *
     * @var Magento_Core_Model_Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param Magento_Core_Model_Registry $coreRegistry
     * @param Magento_Wishlist_Helper_Data $wishlistData
     * @param Magento_CatalogInventory_Helper_Data $catalogInventoryData
     * @param Magento_Adminhtml_Helper_Sales $adminhtmlSales
     * @param Magento_Core_Model_Event_Manager $eventManager
     * @param Magento_Core_Model_Logger $logger
     * @param Magento_Data_Collection_Db_FetchStrategyInterface $fetchStrategy
     * @param Magento_Core_Model_EntityFactory $entityFactory
     * @param Magento_Wishlist_Model_Resource_Item $resource
     */
    public function __construct(
        Magento_Core_Model_Registry $coreRegistry,
        Magento_Wishlist_Helper_Data $wishlistData,
        Magento_CatalogInventory_Helper_Data $catalogInventoryData,
        Magento_Adminhtml_Helper_Sales $adminhtmlSales,
        Magento_Core_Model_Event_Manager $eventManager,
        Magento_Core_Model_Logger $logger,
        Magento_Data_Collection_Db_FetchStrategyInterface $fetchStrategy,
        Magento_Core_Model_EntityFactory $entityFactory,
        Magento_Wishlist_Model_Resource_Item $resource
    ) {
        $this->_coreRegistry = $coreRegistry;
        parent::__construct(
            $wishlistData, $catalogInventoryData, $adminhtmlSales, $eventManager,
            $logger, $fetchStrategy, $entityFactory, $resource
        );
    }

    /**
     * Initialize db select
     *
     * @return Magento_Core_Model_Resource_Db_Collection_Abstract
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->addCustomerIdFilter($this->_coreRegistry->registry('current_customer')->getId())
            ->resetSortOrder()
            ->addDaysInWishlist()
            ->addStoreData();
        return $this;
    }
}
