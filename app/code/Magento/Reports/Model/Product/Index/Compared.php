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
 * Catalog Compared Product Index Model
 *
 * @method Magento_Reports_Model_Resource_Product_Index_Compared _getResource()
 * @method Magento_Reports_Model_Resource_Product_Index_Compared getResource()
 * @method Magento_Reports_Model_Product_Index_Compared setVisitorId(int $value)
 * @method Magento_Reports_Model_Product_Index_Compared setCustomerId(int $value)
 * @method int getProductId()
 * @method Magento_Reports_Model_Product_Index_Compared setProductId(int $value)
 * @method Magento_Reports_Model_Product_Index_Compared setStoreId(int $value)
 * @method string getAddedAt()
 * @method Magento_Reports_Model_Product_Index_Compared setAddedAt(string $value)
 *
 * @category    Magento
 * @package     Magento_Reports
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Reports_Model_Product_Index_Compared extends Magento_Reports_Model_Product_Index_Abstract
{
    /**
     * Cache key name for Count of product index
     *
     * @var string
     */
    protected $_countCacheKey   = 'product_index_compared_count';

    /**
     * Catalog product compare
     *
     * @var Magento_Catalog_Helper_Product_Compare
     */
    protected $_productCompare = null;

    /**
     * @param Magento_Catalog_Helper_Product_Compare $productCompare
     * @param Magento_Core_Model_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @param Magento_Log_Model_Visitor $logVisitor
     * @param Magento_Customer_Model_Session $customerSession
     * @param Magento_Core_Model_Session_Generic $reportSession
     * @param Magento_Catalog_Model_Product_Visibility $productVisibility
     * @param Magento_Core_Model_Resource_Abstract $resource
     * @param Magento_Data_Collection_Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        Magento_Catalog_Helper_Product_Compare $productCompare,
        Magento_Core_Model_Context $context,
        Magento_Core_Model_Registry $registry,
        Magento_Core_Model_StoreManagerInterface $storeManager,
        Magento_Log_Model_Visitor $logVisitor,
        Magento_Customer_Model_Session $customerSession,
        Magento_Core_Model_Session_Generic $reportSession,
        Magento_Catalog_Model_Product_Visibility $productVisibility,
        Magento_Core_Model_Resource_Abstract $resource = null,
        Magento_Data_Collection_Db $resourceCollection = null,
        array $data = array()
    ) {
        parent::__construct(
            $context, $registry, $storeManager, $logVisitor, $customerSession,
            $reportSession, $productVisibility, $resource, $resourceCollection, $data
        );
        $this->_productCompare = $productCompare;
    }

    /**
     * Initialize resource model
     */
    protected function _construct()
    {
        $this->_init('Magento_Reports_Model_Resource_Product_Index_Compared');
    }

    /**
     * Retrieve Exclude Product Ids List for Collection
     *
     * @return array
     */
    public function getExcludeProductIds()
    {
        $productIds = array();
        if ($this->_productCompare->hasItems()) {
            foreach ($this->_productCompare->getItemCollection() as $_item) {
                $productIds[] = $_item->getEntityId();
            }
        }

        if ($this->_coreRegistry->registry('current_product')) {
            $productIds[] = $this->_coreRegistry->registry('current_product')->getId();
        }

        return array_unique($productIds);
    }
}
