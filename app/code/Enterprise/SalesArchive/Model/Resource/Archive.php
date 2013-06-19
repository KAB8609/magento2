<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_SalesArchive
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Archive resource model
 *
 * @category    Enterprise
 * @package     Enterprise_SalesArchive
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_SalesArchive_Model_Resource_Archive extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Archive entities tables association
     *
     * @var $_tables array
     */
    protected $_tables   = array(
        Enterprise_SalesArchive_Model_Archive::ORDER
            => array('sales_flat_order_grid', 'enterprise_sales_order_grid_archive'),
        Enterprise_SalesArchive_Model_Archive::INVOICE
            => array('sales_flat_invoice_grid', 'enterprise_sales_invoice_grid_archive'),
        Enterprise_SalesArchive_Model_Archive::SHIPMENT
            => array('sales_flat_shipment_grid', 'enterprise_sales_shipment_grid_archive'),
        Enterprise_SalesArchive_Model_Archive::CREDITMEMO
            => array('sales_flat_creditmemo_grid', 'enterprise_sales_creditmemo_grid_archive')
    );

    /**
     * Model initialization
     *
     */
    protected function _construct()
    {
        $this->_setResource('enterprise_salesarchive');
    }

    /**
     * Check archive entity existence
     *
     * @param string $archiveEntity
     * @return boolean
     */
    public function isArchiveEntityExists($archiveEntity)
    {
        return isset($this->_tables[$archiveEntity]);
    }

    /**
     * Get archive config
     *
     * @return Enterprise_SalesArchive_Model_Config
     */
    protected function _getConfig()
    {
        return Mage::getSingleton('Enterprise_SalesArchive_Model_Config');
    }

    /**
     * Get archive entity table
     *
     * @param string $archiveEntity
     * @return string
     */
    public function getArchiveEntityTable($archiveEntity)
    {
        if (!$this->isArchiveEntityExists($archiveEntity)) {
            return false;
        }
        return $this->getTable($this->_tables[$archiveEntity][1]);
    }

    /**
     * Retrieve archive entity source table
     *
     * @param string $archiveEntity
     * @return string
     */
    public function getArchiveEntitySourceTable($archiveEntity)
    {
        if (!$this->isArchiveEntityExists($archiveEntity)) {
            return false;
        }
        return $this->getTable($this->_tables[$archiveEntity][0]);
    }

    /**
     * Retrieve entity ids in archive
     *
     * @param string $archiveEntity
     * @param array|int $ids
     * @return array
     */
    public function getIdsInArchive($archiveEntity, $ids)
    {
        if (!$this->isArchiveEntityExists($archiveEntity) || empty($ids)) {
            return array();
        }

        if (!is_array($ids)) {
            $ids = array($ids);
        }

        $select = $this->_getReadAdapter()->select()
            ->from($this->getArchiveEntityTable($archiveEntity), 'entity_id')
            ->where('entity_id IN(?)', $ids);

        return $this->_getReadAdapter()->fetchCol($select);
    }

    /**
     * Retrieve order ids for archive
     *
     * @param array $orderIds
     * @param boolean $useAge
     * @return array
     */
    public function getOrderIdsForArchive($orderIds = array(), $useAge = false)
    {
        $statuses = $this->_getConfig()->getArchiveOrderStatuses();
        $archiveAge = ($useAge ? $this->_getConfig()->getArchiveAge() : 0);

        if (empty($statuses)) {
            return array();
        }

        $select = $this->_getOrderIdsForArchiveSelect($statuses, $archiveAge);
        if (!empty($orderIds)) {
            $select->where('entity_id IN(?)', $orderIds);
        }
        return $this->_getReadAdapter()->fetchCol($select);
    }

    /**
     * Retrieve order ids in archive select
     *
     * @param array $statuses
     * @param int $archiveAge
     * @return Varien_Db_Select
     */
    protected function _getOrderIdsForArchiveSelect($statuses, $archiveAge)
    {
        $adapter = $this->_getReadAdapter();
        $table = $this->getArchiveEntitySourceTable(Enterprise_SalesArchive_Model_Archive::ORDER);
        $select = $adapter->select()
            ->from($table, 'entity_id')
            ->where('status IN(?)', $statuses);

        if ($archiveAge) { // Check archive age
            $archivePeriodExpr = $adapter->getDateSubSql($adapter->quote($this->formatDate(true)),
                (int) $archiveAge,
                Varien_Db_Adapter_Interface::INTERVAL_DAY
            );
            $select->where($archivePeriodExpr . ' >= updated_at');
        }

        return $select;
    }

    /**
     * Retrieve order ids for archive subselect expression
     *
     * @return Zend_Db_Expr
     */
    public function getOrderIdsForArchiveExpression()
    {
        $statuses = $this->_getConfig()->getArchiveOrderStatuses();
        $archiveAge = $this->_getConfig()->getArchiveAge();

        if (empty($statuses)) {
            $statuses = array(0);
        }
        $select = $this->_getOrderIdsForArchiveSelect($statuses, $archiveAge);
        return new Zend_Db_Expr($select);
    }

    /**
     * Move records to from regular grid tables to archive
     *
     * @param Enterprise_SalesArchive_Model_Archive $archive
     * @param string $archiveEntity
     * @param string $conditionField
     * @param array $conditionValue
     * @return Enterprise_SalesArchive_Model_Resource_Archive
     */
    public function moveToArchive($archive, $archiveEntity, $conditionField, $conditionValue)
    {
        if (!$this->isArchiveEntityExists($archiveEntity)) {
            return $this;
        }
        $adapter = $this->_getWriteAdapter();
        $sourceTable = $this->getArchiveEntitySourceTable($archiveEntity);
        $targetTable = $this->getArchiveEntityTable($archiveEntity);

        $insertFields = array_intersect(
            array_keys($adapter->describeTable($targetTable)),
            array_keys($adapter->describeTable($sourceTable))
        );

        $fieldCondition = $adapter->quoteIdentifier($conditionField) . ' IN(?)';
        $select = $adapter->select()
            ->from($sourceTable, $insertFields)
            ->where($fieldCondition, $conditionValue);

        $adapter->query($select->insertFromSelect($targetTable, $insertFields, true));
        return $this;
    }

    /**
     * Remove regords from source grid table
     *
     * @param Enterprise_SalesArchive_Model_Archive $archive
     * @param string $archiveEntity
     * @param string $conditionField
     * @param array $conditionValue
     * @return Enterprise_SalesArchive_Model_Resource_Archive
     */
    public function removeFromGrid($archive, $archiveEntity, $conditionField, $conditionValue)
    {
        if (!$this->isArchiveEntityExists($archiveEntity)) {
            return $this;
        }
        $adapter = $this->_getWriteAdapter();
        $sourceTable = $this->getArchiveEntitySourceTable($archiveEntity);
        $targetTable = $this->getArchiveEntityTable($archiveEntity);
        $sourceResource = Mage::getResourceSingleton($archive->getEntityResourceModel($archiveEntity));
        if ($conditionValue instanceof Zend_Db_Expr) {
            $select = $adapter->select();
            // Remove order grid records moved to archive
            $select->from($targetTable, $sourceResource->getIdFieldName());
            $condition = $adapter->quoteInto($sourceResource->getIdFieldName() . ' IN(?)', new Zend_Db_Expr($select));
        } else {
            $fieldCondition = $adapter->quoteIdentifier($conditionField) . ' IN(?)';
            $condition = $adapter->quoteInto($fieldCondition, $conditionValue);
        }

        $adapter->delete($sourceTable, $condition);
        return $this;
    }

    /**
     * Remove records from archive
     *
     * @param Enterprise_SalesArchive_Model_Archive $archive
     * @param string $archiveEntity
     * @param string $conditionField
     * @param array $conditionValue
     * @return Enterprise_SalesArchive_Model_Resource_Archive
     */
    public function removeFromArchive($archive, $archiveEntity, $conditionField = '', $conditionValue = null)
    {
        if (!$this->isArchiveEntityExists($archiveEntity)) {
            return $this;
        }
        $adapter = $this->_getWriteAdapter();
        $sourceTable = $this->getArchiveEntityTable($archiveEntity);
        $targetTable = $this->getArchiveEntitySourceTable($archiveEntity);
        $sourceResource = Mage::getResourceSingleton($archive->getEntityResourceModel($archiveEntity));

        $insertFields = array_intersect(
            array_keys($adapter->describeTable($targetTable)),
            array_keys($adapter->describeTable($sourceTable))
        );

        $selectFields = $insertFields;
        $updatedAtIndex = array_search('updated_at', $selectFields);
        if ($updatedAtIndex !== false) {
            unset($selectFields[$updatedAtIndex]);
            $selectFields['updated_at'] = new Zend_Db_Expr($adapter->quoteInto('?', $this->formatDate(true)));
        }

        $select = $adapter->select()
            ->from($sourceTable, $selectFields);

        if (!empty($conditionField)) {
            $select->where($adapter->quoteIdentifier($conditionField) . ' IN(?)', $conditionValue);
        }

        $adapter->query($select->insertFromSelect($targetTable, $insertFields, true));
        if ($conditionValue instanceof Zend_Db_Expr) {
            $select->reset()
                ->from($targetTable, $sourceResource->getIdFieldName()); // Remove order grid records from archive
            $condition = $adapter->quoteInto($sourceResource->getIdFieldName() . ' IN(?)', new Zend_Db_Expr($select));
        } elseif (!empty($conditionField)) {
            $condition = $adapter->quoteInto(
                $adapter->quoteIdentifier($conditionField) . ' IN(?)', $conditionValue
            );
        } else {
            $condition = '';
        }

        $adapter->delete($sourceTable, $condition);
        return $this;
    }

    /**
     * Update grid records
     *
     * @param Enterprise_SalesArchive_Model_Archive $archive
     * @param string $archiveEntity
     * @param array $ids
     * @return Enterprise_SalesArchive_Model_Resource_Archive
     */
    public function updateGridRecords($archive, $archiveEntity, $ids)
    {
        if (!$this->isArchiveEntityExists($archiveEntity) || empty($ids)) {
            return $this;
        }

        /* @var $resource Mage_Sales_Model_Resource_Abstract */
        $resource = Mage::getResourceSingleton($archive->getEntityResourceModel($archiveEntity));

        $gridColumns = array_keys($this->_getWriteAdapter()->describeTable(
            $this->getArchiveEntityTable($archiveEntity)
        ));

        $columnsToSelect = array();

        $select = $resource->getUpdateGridRecordsSelect($ids, $columnsToSelect, $gridColumns, true);

        $this->_getWriteAdapter()->query(
            $select->insertFromSelect($this->getArchiveEntityTable($archiveEntity), $columnsToSelect, true)
        );

        return $this;
    }

    /**
     * Find related to order entity ids for checking of new items in archive
     *
     * @param Enterprise_SalesArchive_Model_Archive $archive
     * @param string $archiveEntity
     * @param array $ids
     * @return array
     */
    public function getRelatedIds($archive, $archiveEntity, $ids)
    {
        $resourceClass = $archive->getEntityResourceModel($archiveEntity);

        if (empty($resourceClass) || empty($ids)) {
            return array();
        }

        /** @var $resource Mage_Sales_Model_Resource_Abstract */
        $resource = Mage::getResourceSingleton($resourceClass);

        $select = $this->_getReadAdapter()->select()
            ->from(array('main_table' => $resource->getMainTable()), 'entity_id')
            ->joinInner( // Filter by archived order
                array('order_archive' => $this->getArchiveEntityTable('order')),
                'main_table.order_id = order_archive.entity_id',
                array()
            )
            ->where('main_table.entity_id IN(?)', $ids);

        return $this->_getReadAdapter()->fetchCol($select);
    }
}
