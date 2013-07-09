<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_TargetRule
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * TargetRule Product List Abstract Indexer Resource Model
 *
 * @category    Enterprise
 * @package     Enterprise_TargetRule
 * @author      Magento Core Team <core@magentocommerce.com>
 */
abstract class Enterprise_TargetRule_Model_Resource_Index_Abstract extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Product List Type identifier
     *
     * @var int
     */
    protected $_listType;

    /**
     * Retrieve Product List Type identifier
     *
     * @throws Mage_Core_Exception
     *
     * @return int
     */
    public function getListType()
    {
        if (is_null($this->_listType)) {
            Mage::throwException(
                Mage::helper('Enterprise_TargetRule_Helper_Data')->__('The product list type identifier is not defined.')
            );
        }
        return $this->_listType;
    }

    /**
     * Set Product List identifier
     *
     * @param int $listType
     * @return Enterprise_TargetRule_Model_Resource_Index_Abstract
     */
    public function setListType($listType)
    {
        $this->_listType = $listType;
        return $this;
    }

    /**
     * Retrieve Product Resource instance
     *
     * @return Mage_Catalog_Model_Resource_Product
     */
    public function getProductResource()
    {
        return Mage::getResourceSingleton('Mage_Catalog_Model_Resource_Product');
    }

    public function loadProductIdsBySegmentId($object, $segmentId)
    {
        $select = $this->_getReadAdapter()->select()
            ->from($this->getMainTable(), 'product_ids')
            ->where('entity_id = :entity_id')
            ->where('store_id = :store_id')
            ->where('customer_group_id = :customer_group_id')
            ->where('customer_segment_id = :customer_segment_id');
        $bind = array(
            ':entity_id' => $object->getProduct()->getEntityId(),
            ':store_id' => $object->getStoreId(),
            ':customer_group_id' => $object->getCustomerGroupId(),
            ':customer_segment_id' => $segmentId
        );
        $value  = $this->_getReadAdapter()->fetchOne($select, $bind);

        return (!empty($value)) ? explode(',', $value) :array();
    }

    /**
     * Load Product Ids by Index object
     *
     * @param Enterprise_TargetRule_Model_Index $object
     * @return array
     * @deprecated after 1.12.0.0
     */
    public function loadProductIds($object)
    {
        $select = $this->_getReadAdapter()->select()
            ->from($this->getMainTable(), 'product_ids')
            ->where('entity_id = :entity_id')
            ->where('store_id = :store_id')
            ->where('customer_group_id = :customer_group_id');
        $bind = array(
            ':entity_id'         => $object->getProduct()->getEntityId(),
            ':store_id'          => $object->getStoreId(),
            ':customer_group_id' => $object->getCustomerGroupId()
        );
        $value  = $this->_getReadAdapter()->fetchOne($select, $bind);
        if (!empty($value)) {
            $productIds = explode(',', $value);
        } else {
            $productIds = array();
        }

        return $productIds;
    }

    /**
     * Save matched product Ids by customer segments
     *
     * @param Enterprise_TargetRule_Model_Index $object
     * @param int $segmentId
     * @param string $productIds
     * @return Enterprise_TargetRule_Model_Resource_Index_Abstract
     */
    public function saveResultForCustomerSegments($object, $segmentId, $productIds)
    {
        $adapter = $this->_getWriteAdapter();
        $data    = array(
            'entity_id' => $object->getProduct()->getEntityId(),
            'store_id' => $object->getStoreId(),
            'customer_group_id' => $object->getCustomerGroupId(),
            'customer_segment_id' => $segmentId,
            'product_ids' => $productIds,
        );
        $adapter->insertOnDuplicate($this->getMainTable(), $data, array('product_ids'));
        return $this;
    }

    /**
     * Save matched product Ids
     *
     * @param Enterprise_TargetRule_Model_Index $object
     * @param string $value
     * @return Enterprise_TargetRule_Model_Resource_Index_Abstract
     * @deprecated after 1.12.0.0
     */
    public function saveResult($object, $value)
    {
        $adapter = $this->_getWriteAdapter();
        $data    = array(
            'entity_id'         => $object->getProduct()->getEntityId(),
            'store_id'          => $object->getStoreId(),
            'customer_group_id' => $object->getCustomerGroupId(),
            'product_ids'       => $value
        );

        $adapter->insertOnDuplicate($this->getMainTable(), $data, array('product_ids'));

        return $this;
    }

    /**
     * Remove index by product ids
     *
     * @param Varien_Db_Select|array $entityIds
     * @return Enterprise_TargetRule_Model_Resource_Index_Abstract
     */
    public function removeIndex($entityIds)
    {
        $this->_getWriteAdapter()->delete($this->getMainTable(), array(
            'entity_id IN(?)'   => $entityIds
        ));

        return $this;
    }

    /**
     * Remove all data from index
     *
     * @param Mage_Core_Model_Store|int|array $store
     * @return Enterprise_TargetRule_Model_Resource_Index_Abstract
     */
    public function cleanIndex($store = null)
    {
        if (is_null($store)) {
            $this->_getWriteAdapter()->delete($this->getMainTable());
            return $this;
        }
        if ($store instanceof Mage_Core_Model_Store) {
            $store = $store->getId();
        }
        $where = array('store_id IN(?)' => $store);
        $this->_getWriteAdapter()->delete($this->getMainTable(), $where);

        return $this;
    }
}
