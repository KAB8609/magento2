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
namespace Magento\MultipleWishlist\Model\Item;

class Collection extends \Magento\MultipleWishlist\Model\Resource\Item\Collection
{
    /**
     * Core registry
     *
     * @var \Magento\Core\Model\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\CatalogInventory\Helper\Data $catalogInventoryData
     * @param \Magento\Sales\Helper\Admin $adminhtmlSales
     * @param \Magento\Event\ManagerInterface $eventManager
     * @param \Magento\Logger $logger
     * @param \Magento\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Core\Model\EntityFactory $entityFactory
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     * @param \Magento\Core\Model\Date $date
     * @param \Magento\Wishlist\Model\Config $wishlistConfig
     * @param \Magento\Catalog\Model\Product\Visibility $productVisibility
     * @param \Magento\App\Resource $coreResource
     * @param \Magento\Wishlist\Model\Resource\Item\Option\CollectionFactory $optionCollFactory
     * @param \Magento\Catalog\Model\Resource\Product\CollectionFactory $productCollFactory
     * @param \Magento\Catalog\Model\Resource\ConfigFactory $catalogConfFactory
     * @param \Magento\Catalog\Model\Entity\AttributeFactory $catalogAttrFactory
     * @param \Magento\Wishlist\Helper\Data $wishlistData
     * @param \Magento\Core\Model\Registry $coreRegistry
     * @param \Magento\Wishlist\Model\Resource\Item $resource
     * @param \Magento\App\State $appState
     */
    public function __construct(
        \Magento\CatalogInventory\Helper\Data $catalogInventoryData,
        \Magento\Sales\Helper\Admin $adminhtmlSales,
        \Magento\Event\ManagerInterface $eventManager,
        \Magento\Logger $logger,
        \Magento\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Core\Model\EntityFactory $entityFactory,
        \Magento\Core\Model\StoreManagerInterface $storeManager,
        \Magento\Core\Model\Date $date,
        \Magento\Wishlist\Model\Config $wishlistConfig,
        \Magento\Catalog\Model\Product\Visibility $productVisibility,
        \Magento\App\Resource $coreResource,
        \Magento\Wishlist\Model\Resource\Item\Option\CollectionFactory $optionCollFactory,
        \Magento\Catalog\Model\Resource\Product\CollectionFactory $productCollFactory,
        \Magento\Catalog\Model\Resource\ConfigFactory $catalogConfFactory,
        \Magento\Catalog\Model\Entity\AttributeFactory $catalogAttrFactory,
        \Magento\Wishlist\Helper\Data $wishlistData,
        \Magento\Core\Model\Registry $coreRegistry,
        \Magento\Wishlist\Model\Resource\Item $resource,
        \Magento\App\State $appState
    ) {
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($catalogInventoryData, $adminhtmlSales, $eventManager, $logger, $fetchStrategy,
            $entityFactory, $storeManager, $date, $wishlistConfig, $productVisibility, $coreResource,
            $optionCollFactory, $productCollFactory, $catalogConfFactory, $catalogAttrFactory, $wishlistData,
            $resource, $appState);
    }

    /**
     * Initialize db select
     *
     * @return \Magento\Core\Model\Resource\Db\Collection\AbstractCollection
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
