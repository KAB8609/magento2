<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Review
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Review summary resource model
 *
 * @category    Mage
 * @package     Mage_Review
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Review_Model_Resource_Review_Summary extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Define module
     *
     */
    protected function _construct()
    {
        $this->_init('review_entity_summary', 'entity_pk_value');
    }

    /**
     * Retrieve select object for load object data
     *
     * @param string $field
     * @param mixed $value
     * @param Mage_Core_Model_Abstract $object
     * @return unknown
     */
    protected function _getLoadSelect($field, $value, $object)
    {
        $select = parent::_getLoadSelect($field, $value, $object);
        $select->where('store_id = ?', (int)$object->getStoreId());
        return $select;
    }

    /**
     * Reaggregate all data by rating summary
     *
     * @param array $summary
     * @return Mage_Review_Model_Resource_Review_Summary
     */
    public function reAggregate($summary)
    {
        $adapter = $this->_getWriteAdapter();
        $select = $adapter->select()
            ->from($this->getMainTable(),
                array(
                    'primary_id' => new Zend_Db_Expr('MAX(primary_id)'),
                    'store_id',
                    'entity_pk_value'
                ))
            ->group(array('entity_pk_value', 'store_id'));
        foreach ($adapter->fetchAll($select) as $row) {
            if (isset($summary[$row['store_id']]) && isset($summary[$row['store_id']][$row['entity_pk_value']])) {
                $summaryItem = $summary[$row['store_id']][$row['entity_pk_value']];
                if ($summaryItem->getCount()) {
                    $ratingSummary = round($summaryItem->getSum() / $summaryItem->getCount());
                } else {
                    $ratingSummary = $summaryItem->getSum();
                }
            } else {
                $ratingSummary = 0;
            }
            $adapter->update(
                $this->getMainTable(),
                array('rating_summary' => $ratingSummary),
                $adapter->quoteInto('primary_id = ?', $row['primary_id'])
            );
        }
        return $this;
    }
}