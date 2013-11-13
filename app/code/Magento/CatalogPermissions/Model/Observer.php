<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CatalogPermissions
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Permission model
 *
 * @category   Magento
 * @package    Magento_CatalogPermissions
 */
namespace Magento\CatalogPermissions\Model;

class Observer
{
    const XML_PATH_GRANT_CATALOG_CATEGORY_VIEW = 'catalog/magento_catalogpermissions/grant_catalog_category_view';
    const XML_PATH_GRANT_CATALOG_PRODUCT_PRICE = 'catalog/magento_catalogpermissions/grant_catalog_product_price';
    const XML_PATH_GRANT_CHECKOUT_ITEMS = 'catalog/magento_catalogpermissions/grant_checkout_items';

    /**
     * Is in product queue flag
     *
     * @var boolean
     */
    protected $_isProductQueue = false;

    /**
     * Is in category queue flag
     *
     * @var boolean
     */
    protected $_isCategoryQueue = false;

    /**
     * Models queue for permission apling
     *
     * @var array
     */
    protected $_queue = array();

    /**
     * Permissions cache for products in cart
     *
     * @var array
     */
    protected $_permissionsQuoteCache = array();

    /**
     * Catalog permission helper
     *
     * @var \Magento\CatalogPermissions\Helper\Data
     */
    protected $_catalogPermData;

    /**
     * @var \Magento\CatalogPermissions\Model\Permission\Index
     */
    protected $_permissionIndex;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var \Magento\Core\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\CatalogPermissions\Model\Permission\Index $permissionIndex
     * @param \Magento\CatalogPermissions\Helper\Data $catalogPermData
     */
    public function __construct(
        \Magento\Core\Model\StoreManagerInterface $storeManager,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\CatalogPermissions\Model\Permission\Index $permissionIndex,
        \Magento\CatalogPermissions\Helper\Data $catalogPermData
    ) {
        $this->_storeManager    = $storeManager;
        $this->_catalogPermData = $catalogPermData;
        $this->_permissionIndex = $permissionIndex;
        $this->_customerSession = $customerSession;
    }

    /**
     * Apply category permissions for category collection
     *
     * @param \Magento\Event\Observer $observer
     * @return \Magento\CatalogPermissions\Model\Observer
     */
    public function applyCategoryPermissionOnIsActiveFilterToCollection(\Magento\Event\Observer $observer)
    {
        if (!$this->_catalogPermData->isEnabled()) {
            return $this;
        }

        $categoryCollection = $observer->getEvent()->getCategoryCollection();

        $this->_permissionIndex->addIndexToCategoryCollection(
            $categoryCollection,
            $this->_getCustomerGroupId(),
            $this->_getWebsiteId()
        );

        return $this;
    }

    /**
     * Apply category permissions for category collection
     *
     * @param \Magento\Event\Observer $observer
     * @return \Magento\CatalogPermissions\Model\Observer
     */
    public function applyCategoryPermissionOnLoadCollection(\Magento\Event\Observer $observer)
    {
        if (!$this->_catalogPermData->isEnabled()) {
            return $this;
        }

        $permissions = array();
        $categoryCollection = $observer->getEvent()->getCategoryCollection();
        $categoryIds = $categoryCollection->getColumnValues('entity_id');

        if ($categoryIds) {
            $permissions = $this->_permissionIndex->getIndexForCategory(
                $categoryIds,
                $this->_getCustomerGroupId(),
                $this->_getWebsiteId()
            );
        }

        foreach ($permissions as $categoryId => $permission) {
            $categoryCollection->getItemById($categoryId)->setPermissions($permission);
        }

        foreach ($categoryCollection as $category) {
            $this->_applyPermissionsOnCategory($category);
        }

        return $this;
    }

    /**
     * Apply category view for tree
     *
     * @param \Magento\Event\Observer $observer
     * @return \Magento\CatalogPermissions\Model\Observer
     */
    public function applyCategoryInactiveIds(\Magento\Event\Observer $observer)
    {
        if (!$this->_catalogPermData->isEnabled()) {
            return $this;
        }

        $categoryIds = $this->_permissionIndex->getRestrictedCategoryIds(
            $this->_getCustomerGroupId(),
            $this->_getWebsiteId()
        );

        $observer->getEvent()->getTree()->addInactiveCategoryIds($categoryIds);

        return $this;
    }

    /**
     * Applies permissions on product count for categories
     *
     * @param \Magento\Event\Observer $observer
     * @return \Magento\CatalogPermissions\Model\Observer
     */
    public function applyCategoryPermissionOnProductCount(\Magento\Event\Observer $observer)
    {
        if (!$this->_catalogPermData->isEnabled()) {
            return $this;
        }

        $collection = $observer->getEvent()->getCollection();
        $this->_permissionIndex->addIndexToProductCount($collection, $this->_getCustomerGroupId());
        return $this;
    }

    /**
     * Applies category permission on model afterload
     *
     * @param \Magento\Event\Observer $observer
     * @return \Magento\CatalogPermissions\Model\Observer
     */
    public function applyCategoryPermission(\Magento\Event\Observer $observer)
    {
        if (!$this->_catalogPermData->isEnabled()) {
            return $this;
        }

        $category = $observer->getEvent()->getCategory();
        $permissions = $this->_permissionIndex->getIndexForCategory(
            $category->getId(),
            $this->_getCustomerGroupId(),
            $this->_getWebsiteId()
        );

        if (isset($permissions[$category->getId()])) {
            $category->setPermissions($permissions[$category->getId()]);
        }

        $this->_applyPermissionsOnCategory($category);
        if ($observer->getEvent()->getCategory()->getIsHidden()) {

            $observer->getEvent()->getControllerAction()->getResponse()
                ->setRedirect($this->_catalogPermData->getLandingPageUrl());

            throw new \Magento\Core\Exception(
                __('You may need more permissions to access this category.')
            );
        }
        return $this;
    }

    /**
     * Apply product permissions for collection
     *
     * @param \Magento\Event\Observer $observer
     * @return \Magento\CatalogPermissions\Model\Observer
     */
    public function applyProductPermissionOnCollection(\Magento\Event\Observer $observer)
    {
        if (!$this->_catalogPermData->isEnabled()) {
            return $this;
        }

        $collection = $observer->getEvent()->getCollection();
        $this->_permissionIndex->addIndexToProductCollection($collection, $this->_getCustomerGroupId());
        return $this;
    }

    /**
     * Apply category permissions for collection on after load
     *
     * @param \Magento\Event\Observer $observer
     * @return \Magento\CatalogPermissions\Model\Observer
     */
    public function applyProductPermissionOnCollectionAfterLoad(\Magento\Event\Observer $observer)
    {
        if (!$this->_catalogPermData->isEnabled()) {
            return $this;
        }

        $collection = $observer->getEvent()->getCollection();
        foreach ($collection as $product) {
            if ($collection->hasFlag('product_children')) {
                $product->addData(array(
                    'grant_catalog_category_view'   => -1,
                    'grant_catalog_product_price'   => -1,
                    'grant_checkout_items'          => -1,
                ));
            }
            $this->_applyPermissionsOnProduct($product);
        }
        return $this;
    }

    /**
     * Checks permissions for all quote items
     *
     * @param \Magento\Event\Observer $observer
     * @return \Magento\CatalogPermissions\Model\Observer
     */
    public function checkQuotePermissions(\Magento\Event\Observer $observer)
    {
        if (!$this->_catalogPermData->isEnabled()) {
            return $this;
        }

        $quote = $observer->getEvent()->getCart()->getQuote();
        $this->_initPermissionsOnQuoteItems($quote);

        foreach ($quote->getAllItems() as $quoteItem) {
            if ($quoteItem->getParentItem()) {
                $parentItem = $quoteItem->getParentItem();
            } else {
                $parentItem = false;
            }
            /* @var $quoteItem \Magento\Sales\Model\Quote\Item */
            if ($quoteItem->getDisableAddToCart() && !$quoteItem->isDeleted()) {
                $quote->removeItem($quoteItem->getId());
                if ($parentItem) {
                    $quote->setHasError(true)
                            ->addMessage(
                                __('You cannot add "%1" to the cart.', $parentItem->getName())
                            );
                } else {
                     $quote->setHasError(true)
                            ->addMessage(
                                __('You cannot add "%1" to the cart.', $quoteItem->getName())
                            );
                }
            }
        }

        return $this;
    }

    /**
     * Checks quote item for product permissions
     *
     * @param \Magento\Event\Observer $observer
     * @return \Magento\CatalogPermissions\Model\Observer
     */
    public function checkQuoteItemSetProduct(\Magento\Event\Observer $observer)
    {
        if (!$this->_catalogPermData->isEnabled()) {
            return $this;
        }

        $quoteItem = $observer->getEvent()->getQuoteItem();
        $product = $observer->getEvent()->getProduct();

        if ($quoteItem->getId()) {
            return $this;
        }

        if ($quoteItem->getParentItem()) {
            $parentItem = $quoteItem->getParentItem();
        } else {
            $parentItem = false;
        }

        /* @var $quoteItem \Magento\Sales\Model\Quote\Item */
        if ($product->getDisableAddToCart() && !$quoteItem->isDeleted()) {
            $quoteItem->getQuote()->removeItem($quoteItem->getId());
            if ($parentItem) {
                throw new \Magento\Core\Exception(
                    __('You cannot add "%1" to the cart.', $parentItem->getName())
                );
            } else {
                throw new \Magento\Core\Exception(
                    __('You cannot add "%1" to the cart.', $quoteItem->getName())
                );
            }
        }

        return $this;
    }

    /**
     * Initialize permissions for quote items
     *
     * @param \Magento\Sales\Model\Quote $quote
     * @return \Magento\CatalogPermissions\Model\Observer
     */
    protected function _initPermissionsOnQuoteItems($quote)
    {
        $productIds = array();

        foreach ($quote->getAllItems() as $item) {
            if (!isset($this->_permissionsQuoteCache[$item->getProductId()]) &&
                $item->getProductId()) {
                $productIds[] = $item->getProductId();
            }
        }

        if (!empty($productIds)) {
            $this->_permissionsQuoteCache += $this->_permissionIndex->getIndexForProduct(
                $productIds,
                $this->_getCustomerGroupId(),
                $quote->getStoreId()
            );

            foreach ($productIds as $productId) {
                if (!isset($this->_permissionsQuoteCache[$productId])) {
                    $this->_permissionsQuoteCache[$productId] = false;
                }
            }
        }

        $defaultGrants = array(
            'grant_catalog_category_view' => $this->_catalogPermData->isAllowedCategoryView(),
            'grant_catalog_product_price' => $this->_catalogPermData->isAllowedProductPrice(),
            'grant_checkout_items' => $this->_catalogPermData->isAllowedCheckoutItems()
        );

        foreach ($quote->getAllItems() as $item) {
            if ($item->getProductId()) {
                $permission = $this->_permissionsQuoteCache[$item->getProductId()];
                if (!$permission && in_array(false, $defaultGrants)) {
                    // If no permission found, and no one of default grant is disallowed
                    $item->setDisableAddToCart(true);
                    continue;
                }

                foreach ($defaultGrants as $grant => $defaultPermission) {
                    if ($permission[$grant] == -2 ||
                        ($permission[$grant] != -1 && !$defaultPermission)) {
                        $item->setDisableAddToCart(true);
                        break;
                    }
                }
            }
        }

        return $this;
    }

    /**
     * Apply product permissions on model after load
     *
     * @param \Magento\Event\Observer $observer
     * @return \Magento\CatalogPermissions\Model\Observer
     */
    public function applyProductPermission(\Magento\Event\Observer $observer)
    {
        if (!$this->_catalogPermData->isEnabled()) {
            return $this;
        }

        $product = $observer->getEvent()->getProduct();
        $this->_permissionIndex->addIndexToProduct($product, $this->_getCustomerGroupId());
        $this->_applyPermissionsOnProduct($product);
        if ($observer->getEvent()->getProduct()->getIsHidden()) {
            $observer->getEvent()->getControllerAction()->getResponse()
                ->setRedirect($this->_catalogPermData->getLandingPageUrl());

            throw new \Magento\Core\Exception(
                __('You may need more permissions to access this product.')
            );
        }

        return $this;
    }

    /**
     * Apply category related permissions on category
     *
     * @param \Magento\Data\Tree\Node|\Magento\Catalog\Model\Category
     * @return \Magento\CatalogPermissions\Model\Observer
     */
    protected function _applyPermissionsOnCategory($category)
    {
        if ($category->getData('permissions/grant_catalog_category_view') == -2 ||
            ($category->getData('permissions/grant_catalog_category_view')!= -1 &&
                !$this->_catalogPermData->isAllowedCategoryView())) {
            $category->setIsActive(0);
            $category->setIsHidden(true);
        }

        return $this;
    }

    /**
     * Apply category related permissions on product
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return \Magento\CatalogPermissions\Model\Observer
     */
    protected function _applyPermissionsOnProduct($product)
    {
        if ($product->getData('grant_catalog_category_view') == -2 ||
            ($product->getData('grant_catalog_category_view')!= -1 &&
                !$this->_catalogPermData->isAllowedCategoryView())) {
            $product->setIsHidden(true);
        }


        if ($product->getData('grant_catalog_product_price') == -2 ||
            ($product->getData('grant_catalog_product_price')!= -1 &&
                !$this->_catalogPermData->isAllowedProductPrice())) {
            $product->setCanShowPrice(false);
            $product->setDisableAddToCart(true);
        }

        if ($product->getData('grant_checkout_items') == -2 ||
            ($product->getData('grant_checkout_items')!= -1 &&
                !$this->_catalogPermData->isAllowedCheckoutItems())) {
            $product->setDisableAddToCart(true);
        }

        return $this;
    }

    /**
     * Apply is salable to product
     *
     * @param \Magento\Event\Observer $observer
     * @return \Magento\CatalogPermissions\Model\Observer
     */
    public function applyIsSalableToProduct(\Magento\Event\Observer $observer)
    {
        $product = $observer->getEvent()->getProduct();
        if ($product->getDisableAddToCart()) {
            $observer->getEvent()->getSalable()->setIsSalable(false);
        }
        return $this;
    }

    /**
     * Check catalog search availability on predispatch
     *
     * @return \Magento\CatalogPermissions\Model\Observer
     */
    public function checkCatalogSearchPreDispatch(\Magento\Event\Observer $observer)
    {
        if (!$this->_catalogPermData->isEnabled()) {
            return $this;
        }

        $action = $observer->getEvent()->getControllerAction();
        if (!$this->_catalogPermData->isAllowedCatalogSearch()
            && !$action->getFlag('', \Magento\App\Action\Action::FLAG_NO_DISPATCH)
            && $action->getRequest()->isDispatched()
        ) {
            $action->setFlag('', \Magento\App\Action\Action::FLAG_NO_DISPATCH, true);
            $action->getResponse()->setRedirect($this->_catalogPermData->getLandingPageUrl());
        }

        return $this;
    }

    /**
     * Retrieve current customer group id
     *
     * @return int
     */
    protected function _getCustomerGroupId()
    {
        return $this->_customerSession->getCustomerGroupId();
    }

    /**
     * Retrieve current website id
     *
     * @return int
     */
    protected function _getWebsiteId()
    {
        return $this->_storeManager->getStore()->getWebsiteId();
    }

    /**
     * Apply catalog permissions on product RSS feeds
     *
     * @param \Magento\Event\Observer $observer
     * @return \Magento\CatalogPermissions\Model\Observer
     */
    public function checkIfProductAllowedInRss(\Magento\Event\Observer $observer)
    {
        if (!$this->_catalogPermData->isEnabled()) {
            return $this;
        }

        $row = $observer->getEvent()->getRow();
        if (!$row) {
            $row = $observer->getEvent()->getProduct()->getData();
        }

        $observer->getEvent()->getProduct()->setAllowedInRss(
            $this->_checkPermission(
                $row,
                'grant_catalog_category_view',
                'isAllowedCategoryView'
            )
        );

        $observer->getEvent()->getProduct()->setAllowedPriceInRss(
            $this->_checkPermission(
                $row,
                'grant_catalog_product_price',
                'isAllowedProductPrice'
            )
        );

        return $this;
    }

    /**
     * Checks permission in passed product data.
     * For retrieving default configuration value used
     * $method from helper magento_catalogpermissions.
     *
     * @param array $data
     * @param string $permission
     * @param string $method method name from \Magento\CatalogPermissions\Helper\Data class
     * @return bool
     */
    protected function _checkPermission($data, $permission, $method)
    {
        $result = true;

        /*
         * If there is no permissions for this
         * product then we will use configuration default
         */
        if (!array_key_exists($permission, $data)) {
            $data[$permission] = null;
        }

        if (!$this->_catalogPermData->$method()) {
            if ($data[$permission] == \Magento\CatalogPermissions\Model\Permission::PERMISSION_ALLOW) {
                $result = true;
            } else {
                $result = false;
            }
        } else {
            if ($data[$permission] != \Magento\CatalogPermissions\Model\Permission::PERMISSION_DENY
                    || is_null($data[$permission])) {
                $result = true;
            } else {
                $result = false;
            }
        }

        return $result;
    }

}
