<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_TargetRule
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * TargetRule Rule Resource Model
 *
 * @category    Magento
 * @package     Magento_TargetRule
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_TargetRule_Model_Resource_Rule extends Magento_Rule_Model_Resource_Abstract
{
    /**
     * Store associated with rule entities information map
     *
     * @var array
     */
    protected $_associatedEntitiesMap = array(
        'product' => array(
            'associations_table' => 'magento_targetrule_product',
            'rule_id_field'      => 'rule_id',
            'entity_id_field'    => 'product_id'
        )
    );

    /**
     * @var Magento_Index_Model_Indexer
     */
    protected $_indexer;

    /**
     * @param Magento_Index_Model_Indexer $indexer
     * @param Magento_Core_Model_Resource $resource
     */
    public function __construct(
        Magento_Index_Model_Indexer $indexer,
        Magento_Core_Model_Resource $resource
    ) {
        $this->_indexer = $indexer;
        parent::__construct($resource);
    }


    /**
     * Initialize main table and table id field
     */
    protected function _construct()
    {
        $this->_init('magento_targetrule', 'rule_id');
    }

    /**
     * Get Customer Segment Ids by rule
     *
     * @param Magento_Core_Model_Abstract $object
     * @return array
     */
    public function getCustomerSegmentIds(Magento_Core_Model_Abstract $object)
    {
        $ids = $this->getReadConnection()->select()
            ->from($this->getTable('magento_targetrule_customersegment'), 'segment_id')
            ->where('rule_id = ?', $object->getId())
            ->query()->fetchAll(Zend_Db::FETCH_COLUMN);
        return empty($ids) ? array() : $ids;
    }

    /**
     * Bind rule to customer segments
     *
     * @param int $ruleId
     * @param array $segmentIds
     * @return Magento_TargetRule_Model_Resource_Rule
     */
    public function saveCustomerSegments($ruleId, $segmentIds)
    {
        if (empty($segmentIds)) {
            $segmentIds = array();
        }
        $adapter = $this->_getWriteAdapter();
        foreach ($segmentIds as $segmentId) {
            if (!empty($segmentId)) {
                $adapter->insertOnDuplicate($this->getTable('magento_targetrule_customersegment'),
                    array('rule_id' => $ruleId, 'segment_id' => $segmentId),
                    array()
                );
            }
        }

        if (empty($segmentIds)) {
            $segmentIds = array(0);
        }

        $adapter->delete($this->getTable('magento_targetrule_customersegment'),
            array('rule_id = ?' => $ruleId, 'segment_id NOT IN (?)' => $segmentIds));
        return $this;
    }

    /**
     * Add customer segment ids to rule
     *
     * @param Magento_Core_Model_Abstract $object
     * @return Magento_Core_Model_Resource_Db_Abstract
     */
    protected function _afterLoad(Magento_Core_Model_Abstract $object)
    {
        $object->setData('customer_segment_ids', $this->getCustomerSegmentIds($object));
        return parent::_afterLoad($object);
    }

    /**
     * Save matched products for current rule and clean index
     *
     * @param Magento_Core_Model_Abstract|Magento_TargetRule_Model_Rule $object
     *
     * @return Magento_TargetRule_Model_Resource_Rule
     */
    protected function _afterSave(Magento_Core_Model_Abstract $object)
    {
        parent::_afterSave($object);
        $segmentIds = $object->getUseCustomerSegment() ? $object->getCustomerSegmentIds() : array(0);
        $this->saveCustomerSegments($object->getId(), $segmentIds);

        $this->unbindRuleFromEntity($object->getId(), array(), 'product');
        $this->bindRuleToEntity($object->getId(), $object->getMatchingProductIds(), 'product');

        $typeId = (!$object->isObjectNew() && $object->getOrigData('apply_to') != $object->getData('apply_to'))
            ? null
            : $object->getData('apply_to');

        $this->_indexer->processEntityAction(
            new Magento_Object(array('type_id' => $typeId)),
            Magento_TargetRule_Model_Index::ENTITY_TARGETRULE,
            Magento_TargetRule_Model_Index::EVENT_TYPE_CLEAN_TARGETRULES
        );

        return $this;
    }

    /**
     * Clean index
     *
     * @param Magento_Core_Model_Abstract|Magento_TargetRule_Model_Rule $object
     *
     * @return Magento_TargetRule_Model_Resource_Rule
     */
    protected function _beforeDelete(Magento_Core_Model_Abstract $object)
    {
        $this->_indexer->processEntityAction(
            new Magento_Object(array('type_id' => $object->getData('apply_to'))),
            Magento_TargetRule_Model_Index::ENTITY_TARGETRULE,
            Magento_TargetRule_Model_Index::EVENT_TYPE_CLEAN_TARGETRULES
        );

        parent::_beforeDelete($object);
        return $this;
    }
}
