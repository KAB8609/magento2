<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CatalogSearch
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * CatalogSearch Fulltext Observer
 *
 * @category    Magento
 * @package     Magento_CatalogSearch
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\CatalogSearch\Model\Fulltext;

class Observer
{
    /**
     * Store manager
     *
     * @var \Magento\Core\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Catalog search fulltext
     *
     * @var \Magento\CatalogSearch\Model\Fulltext
     */
    protected $_catalogSearchFulltext;

    /**
     * Eav config
     *
     * @var \Magento\Eav\Model\Config
     */
    protected $_eavConfig;

    /**
     * Backend url
     *
     * @var \Magento\Backend\Model\Url
     */
    protected $_backendUrl;

    /**
     * Backend session
     *
     * @var \Magento\Backend\Model\Session
     */
    protected $_backendSession;

    /**
     * @var \Magento\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * Construct
     *
     * @param \Magento\Backend\Model\Session $backendSession
     * @param \Magento\Backend\Model\Url $backendUrl
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param \Magento\CatalogSearch\Model\Fulltext $catalogSearchFulltext
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     * @param \Magento\Message\ManagerInterface $messageManager
     */
    public function __construct(
        \Magento\Backend\Model\Session $backendSession,
        \Magento\Backend\Model\Url $backendUrl,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\CatalogSearch\Model\Fulltext $catalogSearchFulltext,
        \Magento\Core\Model\StoreManagerInterface $storeManager,
        \Magento\Message\ManagerInterface $messageManager
    ) {
        $this->_backendSession = $backendSession;
        $this->_backendUrl = $backendUrl;
        $this->_eavConfig = $eavConfig;
        $this->_catalogSearchFulltext = $catalogSearchFulltext;
        $this->_storeManager = $storeManager;
        $this->messageManager = $messageManager;
    }

    /**
     * Retrieve fulltext (indexer) model
     *
     * @return \Magento\CatalogSearch\Model\Fulltext
     */
    protected function _getFulltextModel()
    {
        return $this->_catalogSearchFulltext;
    }

    /**
     * Update product index when product data updated
     *
     * @deprecated since 1.11
     * @param \Magento\Event\Observer $observer
     * @return \Magento\CatalogSearch\Model\Fulltext\Observer
     */
    public function refreshProductIndex(\Magento\Event\Observer $observer)
    {
        $product = $observer->getEvent()->getProduct();

        $this->_getFulltextModel()
            ->rebuildIndex(null, $product->getId())
            ->resetSearchResults();

        return $this;
    }

    /**
     * Clean product index when product deleted or marked as unsearchable/invisible
     *
     * @deprecated since 1.11
     * @param \Magento\Event\Observer $observer
     * @return \Magento\CatalogSearch\Model\Fulltext\Observer
     */
    public function cleanProductIndex(\Magento\Event\Observer $observer)
    {
        $product = $observer->getEvent()->getProduct();

        $this->_getFulltextModel()
            ->cleanIndex(null, $product->getId())
            ->resetSearchResults();

        return $this;
    }

    /**
     * Update all attribute-dependant index
     *
     * @param \Magento\Event\Observer $observer
     * @return \Magento\CatalogSearch\Model\Fulltext\Observer
     */
    public function eavAttributeChange(\Magento\Event\Observer $observer)
    {
        $attribute = $observer->getEvent()->getAttribute();
        /* @var $attribute \Magento\Eav\Model\Entity\Attribute */
        $entityType = $this->_eavConfig->getEntityType(\Magento\Catalog\Model\Product::ENTITY);
        /* @var $entityType \Magento\Eav\Model\Entity\Type */

        if ($attribute->getEntityTypeId() != $entityType->getId()) {
            return $this;
        }
        $delete = $observer->getEventName() == 'eav_entity_attribute_delete_after';

        if (!$delete && !$attribute->dataHasChangedFor('is_searchable')) {
            return $this;
        }

        $showNotice = false;
        if ($delete) {
            if ($attribute->getIsSearchable()) {
                $showNotice = true;
            }
        }
        elseif ($attribute->dataHasChangedFor('is_searchable')) {
            $showNotice = true;
        }

        if ($showNotice) {
            $url = $this->_backendUrl->getUrl('adminhtml/system_cache');
            $this->messageManager->addNotice(
                __('Attribute setting change related with Search Index. Please run <a href="%1">Rebuild Search Index</a> process.', $url)
            );
        }

        return $this;
    }

    /**
     * Rebuild index after import
     *
     * @return \Magento\CatalogSearch\Model\Fulltext\Observer
     */
    public function refreshIndexAfterImport()
    {
        $this->_getFulltextModel()
            ->rebuildIndex();
        return $this;
    }

    /**
     * Refresh fulltext index when we add new store
     *
     * @param   \Magento\Event\Observer $observer
     * @return  \Magento\CatalogSearch\Model\Fulltext\Observer
     */
    public function refreshStoreIndex(\Magento\Event\Observer $observer)
    {
        $storeId = $observer->getEvent()->getStore()->getId();
        $this->_getFulltextModel()->rebuildIndex($storeId);
        return $this;
    }

    /**
     * Catalog Product mass website update
     *
     * @deprecated since 1.11
     * @param \Magento\Event\Observer $observer
     * @return \Magento\CatalogSearch\Model\Fulltext\Observer
     */
    public function catalogProductWebsiteUpdate(\Magento\Event\Observer $observer)
    {
        $websiteIds = $observer->getEvent()->getWebsiteIds();
        $productIds = $observer->getEvent()->getProductIds();
        $actionType = $observer->getEvent()->getAction();

        foreach ($websiteIds as $websiteId) {
            foreach ($this->_storeManager->getWebsite($websiteId)->getStoreIds() as $storeId) {
                if ($actionType == 'remove') {
                    $this->_getFulltextModel()
                        ->cleanIndex($storeId, $productIds)
                        ->resetSearchResults();
                }
                elseif ($actionType == 'add') {
                    $this->_getFulltextModel()
                        ->rebuildIndex($storeId, $productIds)
                        ->resetSearchResults();
                }
            }
        }

        return $this;
    }

    /**
     * Store delete processing
     *
     * @param \Magento\Event\Observer $observer
     * @return \Magento\CatalogSearch\Model\Fulltext\Observer
     */
    public function cleanStoreIndex(\Magento\Event\Observer $observer)
    {
        $store = $observer->getEvent()->getStore();
        /* @var $store \Magento\Core\Model\Store */

        $this->_getFulltextModel()
            ->cleanIndex($store->getId());

        return $this;
    }
}
