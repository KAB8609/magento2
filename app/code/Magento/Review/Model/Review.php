<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Review
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Review model
 *
 * @method Magento_Review_Model_Resource_Review _getResource()
 * @method Magento_Review_Model_Resource_Review getResource()
 * @method string getCreatedAt()
 * @method Magento_Review_Model_Review setCreatedAt(string $value)
 * @method Magento_Review_Model_Review setEntityId(int $value)
 * @method int getEntityPkValue()
 * @method Magento_Review_Model_Review setEntityPkValue(int $value)
 * @method int getStatusId()
 * @method Magento_Review_Model_Review setStatusId(int $value)
 *
 * @category    Magento
 * @package     Magento_Review
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Review_Model_Review extends Magento_Core_Model_Abstract
{
    /**
     * Event prefix for observer
     *
     * @var string
     */
    protected $_eventPrefix = 'review';

    /**
     * Review entity codes
     *
     */
    const ENTITY_PRODUCT_CODE   = 'product';
    const ENTITY_CUSTOMER_CODE  = 'customer';
    const ENTITY_CATEGORY_CODE  = 'category';

    const STATUS_APPROVED       = 1;
    const STATUS_PENDING        = 2;
    const STATUS_NOT_APPROVED   = 3;

    /**
     * @var Magento_Review_Model_Resource_Review_Product_CollectionFactory
     */
    protected $_productFactory;

    /**
     * @var Magento_Review_Model_Resource_Review_Status_CollectionFactory
     */
    protected $_statusFactory;

    /**
     * @var Magento_Review_Model_Review_SummaryFactory
     */
    protected $_summaryFactory;

    /**
     * @var Magento_Review_Model_Review_Summary
     */
    protected $_reviewSummary;

    /**
     * @var Magento_Core_Model_StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var Magento_Core_Model_UrlInterface
     */
    protected $_urlModel;

    /**
     * @param Magento_Core_Model_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param Magento_Review_Model_Resource_Review_Product_CollectionFactory $productFactory
     * @param Magento_Review_Model_Resource_Review_Status_CollectionFactory $statusFactory
     * @param Magento_Review_Model_Review_SummaryFactory $summaryFactory
     * @param Magento_Review_Model_Review_Summary $reviewSummary
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @param Magento_Core_Model_UrlInterface $urlModel
     * @param Magento_Core_Model_Resource_Abstract $resource
     * @param Magento_Data_Collection_Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_Context $context,
        Magento_Core_Model_Registry $registry,
        Magento_Review_Model_Resource_Review_Product_CollectionFactory $productFactory,
        Magento_Review_Model_Resource_Review_Status_CollectionFactory $statusFactory,
        Magento_Review_Model_Review_SummaryFactory $summaryFactory,
        Magento_Review_Model_Review_Summary $reviewSummary,
        Magento_Core_Model_StoreManagerInterface $storeManager,
        Magento_Core_Model_UrlInterface $urlModel,
        Magento_Core_Model_Resource_Abstract $resource = null,
        Magento_Data_Collection_Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_productFactory = $productFactory;
        $this->_statusFactory = $statusFactory;
        $this->_summaryFactory = $summaryFactory;
        $this->_reviewSummary = $reviewSummary;
        $this->_storeManager = $storeManager;
        $this->_urlModel = $urlModel;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    protected function _construct()
    {
        $this->_init('Magento_Review_Model_Resource_Review');
    }

    public function getProductCollection()
    {
        return $this->_productFactory->create();
    }

    public function getStatusCollection()
    {
        return $this->_statusFactory->create();
    }

    public function getTotalReviews($entityPkValue, $approvedOnly=false, $storeId=0)
    {
        return $this->getResource()->getTotalReviews($entityPkValue, $approvedOnly, $storeId);
    }

    public function aggregate()
    {
        $this->getResource()->aggregate($this);
        return $this;
    }

    public function getEntitySummary($product, $storeId=0)
    {
        $summaryData = $this->_summaryFactory->create()
            ->setStoreId($storeId)
            ->load($product->getId());
        $summary = new Magento_Object();
        $summary->setData($summaryData->getData());
        $product->setRatingSummary($summary);
    }

    public function getPendingStatus()
    {
        return self::STATUS_PENDING;
    }

    public function getReviewUrl()
    {
        return $this->_urlModel->getUrl('review/product/view', array('id' => $this->getReviewId()));
    }

    public function validate()
    {
        $errors = array();

        if (!Zend_Validate::is($this->getTitle(), 'NotEmpty')) {
            $errors[] = __('The review summary field can\'t be empty.');
        }

        if (!Zend_Validate::is($this->getNickname(), 'NotEmpty')) {
            $errors[] = __('The nickname field can\'t be empty.');
        }

        if (!Zend_Validate::is($this->getDetail(), 'NotEmpty')) {
            $errors[] = __('The review field can\'t be empty.');
        }

        if (empty($errors)) {
            return true;
        }
        return $errors;
    }

    /**
     * Perform actions after object delete
     *
     * @return Magento_Core_Model_Abstract
     */
    protected function _afterDeleteCommit()
    {
        $this->getResource()->afterDeleteCommit($this);
        return parent::_afterDeleteCommit();
    }

    /**
     * Append review summary to product collection
     *
     * @param Magento_Catalog_Model_Resource_Product_Collection $collection
     * @return Magento_Review_Model_Review
     */
    public function appendSummary($collection)
    {
        $entityIds = array();
        foreach ($collection->getItems() as $_item) {
            $entityIds[] = $_item->getEntityId();
        }

        if (sizeof($entityIds) == 0) {
            return $this;
        }

        $summaryData = $this->_summaryFactory->create()
            ->addEntityFilter($entityIds)
            ->addStoreFilter($this->_storeManager->getStore()->getId())
            ->load();

        foreach ($collection->getItems() as $_item ) {
            foreach ($summaryData as $_summary) {
                if ($_summary->getEntityPkValue() == $_item->getEntityId()) {
                    $_item->setRatingSummary($_summary);
                }
            }
        }

        return $this;
    }

    protected function _beforeDelete()
    {
        $this->_protectFromNonAdmin();
        return parent::_beforeDelete();
    }

    /**
     * Check if current review approved or not
     *
     * @return bool
     */
    public function isApproved()
    {
        return $this->getStatusId() == self::STATUS_APPROVED;
    }

    /**
     * Check if current review available on passed store
     *
     * @param int|Magento_Core_Model_Store $store
     * @return bool
     */
    public function isAvailableOnStore($store = null)
    {
        $store = $this->_storeManager->getStore($store);
        if ($store) {
            return in_array($store->getId(), (array)$this->getStores());
        }

        return false;
    }

    /**
     * Get review entity type id by code
     *
     * @param string $entityCode
     * @return int|bool
     */
    public function getEntityIdByCode($entityCode)
    {
        return $this->getResource()->getEntityIdByCode($entityCode);
    }
}
