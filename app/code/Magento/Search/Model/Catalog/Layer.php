<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Search
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Catalog layer model integrated with search engine
 */
namespace Magento\Search\Model\Catalog;

class Layer extends \Magento\Catalog\Model\Layer
{
    /**
     * @var \Magento\CatalogSearch\Model\Resource\EngineProvider
     */
    protected $_engineProvider;

    /**
     * Catalog search data
     *
     * @var \Magento\CatalogSearch\Helper\Data
     */
    protected $_catalogSearchData;

    /**
     * @param \Magento\CatalogSearch\Model\Resource\EngineProvider $engineProvider
     * @param \Magento\CatalogSearch\Helper\Data $catalogSearchData
     * @param \Magento\Catalog\Model\Layer\StateFactory $layerStateFactory
     * @param \Magento\Catalog\Model\CategoryFactory $categoryFactory
     * @param \Magento\Catalog\Model\Resource\Product\Attribute\CollectionFactory $attributeCollectionFactory
     * @param \Magento\Catalog\Model\Resource\Product $catalogProduct
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     * @param \Magento\Catalog\Model\Product\Visibility $catalogProductVisibility
     * @param \Magento\Catalog\Model\Config $catalogConfig
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Core\Model\Registry $coreRegistry
     * @param array $data
     */
    public function __construct(
        \Magento\CatalogSearch\Model\Resource\EngineProvider $engineProvider,
        \Magento\CatalogSearch\Helper\Data $catalogSearchData,
        \Magento\Catalog\Model\Layer\StateFactory $layerStateFactory,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Magento\Catalog\Model\Resource\Product\Attribute\CollectionFactory $attributeCollectionFactory,
        \Magento\Catalog\Model\Resource\Product $catalogProduct,
        \Magento\Core\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\Product\Visibility $catalogProductVisibility,
        \Magento\Catalog\Model\Config $catalogConfig,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Core\Model\Registry $coreRegistry,
        array $data = array()
    ) {
        $this->_engineProvider = $engineProvider;
        $this->_catalogSearchData = $catalogSearchData;
        parent::__construct(
            $layerStateFactory,
            $categoryFactory,
            $attributeCollectionFactory,
            $catalogProduct,
            $storeManager,
            $catalogProductVisibility,
            $catalogConfig,
            $customerSession,
            $coreRegistry,
            $data
        );
    }

    /**
     * Retrieve current layer product collection
     *
     * @return \Magento\Search\Model\Resource\Collection
     */
    public function getProductCollection()
    {
        if (isset($this->_productCollections[$this->getCurrentCategory()->getId()])) {
            $collection = $this->_productCollections[$this->getCurrentCategory()->getId()];
        } else {
            $collection = $this->_engineProvider->get()->getResultCollection();
            $collection->setStoreId($this->getCurrentCategory()->getStoreId())
                ->addCategoryFilter($this->getCurrentCategory())
                ->setGeneralDefaultQuery();
            $this->prepareProductCollection($collection);
            $this->_productCollections[$this->getCurrentCategory()->getId()] = $collection;
        }

        return $collection;
    }

    /**
     * Get default tags for current layer state
     *
     * @param   array $additionalTags
     * @return  array
     */
    public function getStateTags(array $additionalTags = array())
    {
        $additionalTags = array_merge($additionalTags, array(
            \Magento\Catalog\Model\Category::CACHE_TAG . $this->getCurrentCategory()->getId() . '_SEARCH'
        ));

        return parent::getStateTags($additionalTags);
    }
}
