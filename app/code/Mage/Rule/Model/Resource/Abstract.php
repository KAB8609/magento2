<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Rule
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Abstract Rule entity resource model
 *
 * @category Mage
 * @package Mage_Rule
 * @author Magento Core Team <core@magentocommerce.com>
 */
abstract class Mage_Rule_Model_Resource_Abstract extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Store associated with rule entities information map
     *
     * Example:
     * array(
     *    'entity_type1' => array(
     *        'associations_table' => 'table_name',
     *        'rule_id_field'      => 'rule_id',
     *        'entity_id_field'    => 'entity_id'
     *    ),
     *    'entity_type2' => array(
     *        'associations_table' => 'table_name',
     *        'rule_id_field'      => 'rule_id',
     *        'entity_id_field'    => 'entity_id'
     *    )
     *    ....
     * )
     *
     * @var array
     */
    protected $_associatedEntitiesMap = array();

    /**
     * Prepare rule's active "from" and "to" dates
     *
     * @param Mage_Core_Model_Abstract $object
     *
     * @return Mage_Rule_Model_Resource_Abstract
     */
    public function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        $fromDate = $object->getFromDate();
        if ($fromDate instanceof Zend_Date) {
            $object->setFromDate($fromDate->toString(Magento_Date::DATETIME_INTERNAL_FORMAT));
        } elseif (!is_string($fromDate) || empty($fromDate)) {
            $object->setFromDate(null);
        }

        $toDate = $object->getToDate();
        if ($toDate instanceof Zend_Date) {
            $object->setToDate($toDate->toString(Magento_Date::DATETIME_INTERNAL_FORMAT));
        } elseif (!is_string($toDate) || empty($toDate)) {
            $object->setToDate(null);
        }

        parent::_beforeSave($object);
        return $this;
    }

    /**
     * Bind specified rules to entities
     *
     * @param array|int|string $ruleIds
     * @param array|int|string $entityIds
     * @param string $entityType
     *
     * @return Mage_Rule_Model_Resource_Abstract
     */
    public function bindRuleToEntity($ruleIds, $entityIds, $entityType)
    {
        if (empty($ruleIds) || empty($entityIds)) {
            return $this;
        }
        $adapter    = $this->_getWriteAdapter();
        $entityInfo = $this->_getAssociatedEntityInfo($entityType);

        if (!is_array($ruleIds)) {
            $ruleIds = array((int) $ruleIds);
        }
        if (!is_array($entityIds)) {
            $entityIds = array((int) $entityIds);
        }

        $data  = array();
        $count = 0;

        $adapter->beginTransaction();

        try {
            foreach ($ruleIds as $ruleId) {
                foreach ($entityIds as $entityId) {
                    $data[] = array(
                        $entityInfo['entity_id_field'] => $entityId,
                        $entityInfo['rule_id_field'] => $ruleId
                    );
                    $count++;
                    if (($count % 1000) == 0) {
                        $adapter->insertOnDuplicate(
                            $this->getTable($entityInfo['associations_table']),
                            $data,
                            array($entityInfo['rule_id_field'])
                        );
                        $data = array();
                    }
                }
            }
            if (!empty($data)) {
                $adapter->insertOnDuplicate(
                    $this->getTable($entityInfo['associations_table']),
                    $data,
                    array($entityInfo['rule_id_field'])
                );
            }

            $adapter->delete($this->getTable($entityInfo['associations_table']),
                $adapter->quoteInto($entityInfo['rule_id_field']   . ' IN (?) AND ', $ruleIds) .
                $adapter->quoteInto($entityInfo['entity_id_field'] . ' NOT IN (?)',  $entityIds)
            );
        } catch (Exception $e) {
            $adapter->rollback();
            throw $e;

        }

        $adapter->commit();

        return $this;
    }

    /**
     * Unbind specified rules from entities
     *
     * @param array|int|string $ruleIds
     * @param array|int|string $entityIds
     * @param string $entityType
     *
     * @return Mage_Rule_Model_Resource_Abstract
     */
    public function unbindRuleFromEntity($ruleIds = array(), $entityIds = array(), $entityType)
    {
        $writeAdapter = $this->_getWriteAdapter();
        $entityInfo   = $this->_getAssociatedEntityInfo($entityType);

        if (!is_array($entityIds)) {
            $entityIds = array((int) $entityIds);
        }
        if (!is_array($ruleIds)) {
            $ruleIds = array((int) $ruleIds);
        }

        $where = array();
        if (!empty($ruleIds)) {
            $where[] = $writeAdapter->quoteInto($entityInfo['rule_id_field'] . ' IN (?)', $ruleIds);
        }
        if (!empty($entityIds)) {
            $where[] = $writeAdapter->quoteInto($entityInfo['entity_id_field'] . ' IN (?)', $entityIds);
        }

        $writeAdapter->delete($this->getTable($entityInfo['associations_table']), implode(' AND ', $where));

        return $this;
    }

    /**
     * Retrieve rule's associated entity Ids by entity type
     *
     * @param int $ruleId
     * @param string $entityType
     *
     * @return array
     */
    public function getAssociatedEntityIds($ruleId, $entityType)
    {
        $entityInfo = $this->_getAssociatedEntityInfo($entityType);

        $select = $this->_getReadAdapter()->select()
            ->from($this->getTable($entityInfo['associations_table']), array($entityInfo['entity_id_field']))
            ->where($entityInfo['rule_id_field'] . ' = ?', $ruleId);

        return $this->_getReadAdapter()->fetchCol($select);
    }

    /**
     * Retrieve website ids of specified rule
     *
     * @param int $ruleId
     * @return array
     */
    public function getWebsiteIds($ruleId)
    {
        return $this->getAssociatedEntityIds($ruleId, 'website');
    }

    /**
     * Retrieve customer group ids of specified rule
     *
     * @param int $ruleId
     * @return array
     */
    public function getCustomerGroupIds($ruleId)
    {
        return $this->getAssociatedEntityIds($ruleId, 'customer_group');
    }

    /**
     * Retrieve correspondent entity information (associations table name, columns names)
     * of rule's associated entity by specified entity type
     *
     * @param string $entityType
     *
     * @return array
     */
    protected function _getAssociatedEntityInfo($entityType)
    {
        if (isset($this->_associatedEntitiesMap[$entityType])) {
            return $this->_associatedEntitiesMap[$entityType];
        }

        $e = Mage::exception(
            'Mage_Core',
            __(
                'There is no information about associated entity type "%1".', $entityType
            )
        );
        throw $e;
    }
}
