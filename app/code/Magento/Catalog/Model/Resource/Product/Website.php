<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Catalog Product Website Resource Model
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Catalog\Model\Resource\Product;

class Website extends \Magento\Core\Model\Resource\Db\AbstractDb
{
    /**
     * Store manager
     *
     * @var \Magento\Core\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Catalog product
     *
     * @var \Magento\Catalog\Model\Resource\Product
     */
    protected $_productResource;

    /**
     * @param \Magento\App\Resource $resource
     * @param \Magento\Catalog\Model\Resource\Product $productResource
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\App\Resource $resource,
        \Magento\Catalog\Model\Resource\Product $productResource,
        \Magento\Core\Model\StoreManagerInterface $storeManager
    ) {
        $this->_productResource = $productResource;
        $this->_storeManager = $storeManager;
        parent::__construct($resource);
    }

    /**
     * Initialize connection and define resource table
     *
     */
    protected function _construct()
    {
        $this->_init('catalog_product_website', 'product_id');
    }

    /**
     * Removes products from websites
     *
     * @param array $websiteIds
     * @param array $productIds
     * @return \Magento\Catalog\Model\Resource\Product\Website
     * @throws \Exception
     */
    public function removeProducts($websiteIds, $productIds)
    {
        if (!is_array($websiteIds) || !is_array($productIds)
            || count($websiteIds) == 0 || count($productIds) == 0)
        {
            return $this;
        }

        $adapter   = $this->_getWriteAdapter();
        $whereCond = array(
            $adapter->quoteInto('website_id IN(?)', $websiteIds),
           $adapter->quoteInto('product_id IN(?)', $productIds)
        );
        $whereCond = join(' AND ', $whereCond);

        $adapter->beginTransaction();
        try {
            $adapter->delete($this->getMainTable(), $whereCond);
            $adapter->commit();
        } catch (\Exception $e) {
            $adapter->rollBack();
            throw $e;
        }

        return $this;
    }

    /**
     * Add products to websites
     *
     * @param array $websiteIds
     * @param array $productIds
     * @return \Magento\Catalog\Model\Resource\Product\Website
     * @throws \Exception
     */
    public function addProducts($websiteIds, $productIds)
    {
        if (!is_array($websiteIds) || !is_array($productIds)
            || count($websiteIds) == 0 || count($productIds) == 0)
        {
            return $this;
        }

        $this->_getWriteAdapter()->beginTransaction();

        // Before adding of products we should remove it old rows with same ids
        $this->removeProducts($websiteIds, $productIds);
        try {
            foreach ($websiteIds as $websiteId) {
                foreach ($productIds as $productId) {
                    if (!$productId) {
                        continue;
                    }
                    $this->_getWriteAdapter()->insert($this->getMainTable(), array(
                        'product_id' => (int) $productId,
                        'website_id' => (int) $websiteId
                    ));
                }

                // Refresh product enabled index
                $storeIds = $this->_storeManager->getWebsite($websiteId)->getStoreIds();
                foreach ($storeIds as $storeId) {
                    $store = $this->_storeManager->getStore($storeId);
                    $this->_productResource->refreshEnabledIndex($store, $productIds);
                }
            }

            $this->_getWriteAdapter()->commit();
        } catch (\Exception $e) {
            $this->_getWriteAdapter()->rollBack();
            throw $e;
        }
        return $this;
    }

    /**
     * Retrieve product(s) website ids.
     *
     * @param array $productIds
     * @return array
     */
    public function getWebsites($productIds)
    {
        $select = $this->_getReadAdapter()->select()
            ->from($this->getMainTable(), array('product_id', 'website_id'))
            ->where('product_id IN (?)', $productIds);
        $rowset  = $this->_getReadAdapter()->fetchAll($select);

        $result = array();
        foreach ($rowset as $row) {
            $result[$row['product_id']][] = $row['website_id'];
        }

        return $result;
    }
}
