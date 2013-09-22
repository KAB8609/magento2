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
 * Catalog search query model
 *
 * @method \Magento\CatalogSearch\Model\Resource\Query _getResource()
 * @method \Magento\CatalogSearch\Model\Resource\Query getResource()
 * @method string getQueryText()
 * @method \Magento\CatalogSearch\Model\Query setQueryText(string $value)
 * @method int getNumResults()
 * @method \Magento\CatalogSearch\Model\Query setNumResults(int $value)
 * @method int getPopularity()
 * @method \Magento\CatalogSearch\Model\Query setPopularity(int $value)
 * @method string getRedirect()
 * @method \Magento\CatalogSearch\Model\Query setRedirect(string $value)
 * @method string getSynonymFor()
 * @method \Magento\CatalogSearch\Model\Query setSynonymFor(string $value)
 * @method int getDisplayInTerms()
 * @method \Magento\CatalogSearch\Model\Query setDisplayInTerms(int $value)
 * @method int getIsActive()
 * @method \Magento\CatalogSearch\Model\Query setIsActive(int $value)
 * @method int getIsProcessed()
 * @method \Magento\CatalogSearch\Model\Query setIsProcessed(int $value)
 * @method string getUpdatedAt()
 * @method \Magento\CatalogSearch\Model\Query setUpdatedAt(string $value)
 *
 * @category    Magento
 * @package     Magento_CatalogSearch
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\CatalogSearch\Model;

class Query extends \Magento\Core\Model\AbstractModel
{
    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'catalogsearch_query';

    /**
     * Event object key name
     *
     * @var string
     */
    protected $_eventObject = 'catalogsearch_query';

    const CACHE_TAG                     = 'SEARCH_QUERY';
    const XML_PATH_MIN_QUERY_LENGTH     = 'catalog/search/min_query_length';
    const XML_PATH_MAX_QUERY_LENGTH     = 'catalog/search/max_query_length';
    const XML_PATH_MAX_QUERY_WORDS      = 'catalog/search/max_query_words';

    /**
     * Core store config
     *
     * @var \Magento\Core\Model\Store\Config
     */
    protected $_coreStoreConfig;

    /**
     * @param \Magento\Core\Model\Context $context
     * @param \Magento\Core\Model\Registry $registry
     * @param \Magento\Core\Model\Store\Config $coreStoreConfig
     * @param \Magento\Core\Model\Resource\AbstractResource $resource
     * @param \Magento\Data\Collection\Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Model\Context $context,
        \Magento\Core\Model\Registry $registry,
        \Magento\Core\Model\Store\Config $coreStoreConfig,
        \Magento\Core\Model\Resource\AbstractResource $resource = null,
        \Magento\Data\Collection\Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_coreStoreConfig = $coreStoreConfig;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Init resource model
     *
     */
    protected function _construct()
    {
        $this->_init('Magento\CatalogSearch\Model\Resource\Query');
    }

    /**
     * Retrieve search collection
     *
     * @return \Magento\CatalogSearch\Model\Resource\Search\Collection
     */
    public function getSearchCollection()
    {
        return \Mage::getResourceModel('Magento\CatalogSearch\Model\Resource\Search\Collection');
    }

    /**
     * Retrieve collection of search results
     *
     * @return \Magento\Eav\Model\Entity\Collection\AbstractCollection
     */
    public function getResultCollection()
    {
        $collection = $this->getData('result_collection');
        if (is_null($collection)) {
            $collection = $this->getSearchCollection();

            $text = $this->getSynonymFor();
            if (!$text) {
                $text = $this->getQueryText();
            }

            $collection->addSearchFilter($text)
                ->addStoreFilter()
                ->addMinimalPrice()
                ->addTaxPercents();
            $this->setData('result_collection', $collection);
        }
        return $collection;
    }

    /**
     * Retrieve collection of suggest queries
     *
     * @return \Magento\CatalogSearch\Model\Resource\Query\Collection
     */
    public function getSuggestCollection()
    {
        $collection = $this->getData('suggest_collection');
        if (is_null($collection)) {
            $collection = \Mage::getResourceModel('Magento\CatalogSearch\Model\Resource\Query\Collection')
                ->setStoreId($this->getStoreId())
                ->setQueryFilter($this->getQueryText());
            $this->setData('suggest_collection', $collection);
        }
        return $collection;
    }

    /**
     * Load Query object by query string
     *
     * @param string $text
     * @return \Magento\CatalogSearch\Model\Query
     */
    public function loadByQuery($text)
    {
        $this->_getResource()->loadByQuery($this, $text);
        $this->_afterLoad();
        $this->setOrigData();
        return $this;
    }

    /**
     * Load Query object only by query text (skip 'synonym For')
     *
     * @param string $text
     * @return \Magento\CatalogSearch\Model\Query
     */
    public function loadByQueryText($text)
    {
        $this->_getResource()->loadByQueryText($this, $text);
        $this->_afterLoad();
        $this->setOrigData();
        return $this;
    }

    /**
     * Set Store Id
     *
     * @param int $storeId
     * @return \Magento\CatalogSearch\Model\Query
     */
    public function setStoreId($storeId)
    {
        $this->setData('store_id', $storeId);
    }

    /**
     * Retrieve store Id
     *
     * @return int
     */
    public function getStoreId()
    {
        if (!$storeId = $this->getData('store_id')) {
            $storeId = \Mage::app()->getStore()->getId();
        }
        return $storeId;
    }

    /**
     * Prepare save query for result
     *
     * @return \Magento\CatalogSearch\Model\Query
     */
    public function prepare()
    {
        if (!$this->getId()) {
            $this->setIsActive(0);
            $this->setIsProcessed(0);
            $this->save();
            $this->setIsActive(1);
        }

        return $this;
    }

    /**
     * Retrieve minimum query length
     *
     * @return int
     */
    public function getMinQueryLength()
    {
        return $this->_coreStoreConfig->getConfig(self::XML_PATH_MIN_QUERY_LENGTH, $this->getStoreId());
    }

    /**
     * Retrieve maximum query length
     *
     * @return int
     */
    public function getMaxQueryLength()
    {
        return $this->_coreStoreConfig->getConfig(self::XML_PATH_MAX_QUERY_LENGTH, $this->getStoreId());
    }

    /**
     * Retrieve maximum query words for like search
     *
     * @return int
     */
    public function getMaxQueryWords()
    {
        return $this->_coreStoreConfig->getConfig(self::XML_PATH_MAX_QUERY_WORDS, $this->getStoreId());
    }
}
