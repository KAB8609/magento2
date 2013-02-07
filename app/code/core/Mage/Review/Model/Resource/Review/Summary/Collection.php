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
 * Review summery collection
 *
 * @category    Mage
 * @package     Mage_Review
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Review_Model_Resource_Review_Summary_Collection extends Varien_Data_Collection_Db
{
    /**
     * Enter description here ...
     *
     * @var unknown
     */
    protected $_summaryTable;

    /**
     * Enter description here ...
     *
     */
    public function __construct()
    {
        $resources = Mage::getSingleton('Mage_Core_Model_Resource');
        $this->_setIdFieldName('primary_id');

        parent::__construct($resources->getConnection('review_read'));
        $this->_summaryTable = $resources->getTableName('review_entity_summary');

        $this->_select->from($this->_summaryTable);

        $this->setItemObjectClass('Mage_Review_Model_Review_Summary');
    }

    /**
     * Add entity filter
     *
     * @param unknown_type $entityId
     * @param unknown_type $entityType
     * @return Mage_Review_Model_Resource_Review_Summary_Collection
     */
    public function addEntityFilter($entityId, $entityType = 1)
    {
        $this->_select->where('entity_pk_value IN(?)', $entityId)
            ->where('entity_type = ?', $entityType);
        return $this;
    }

    /**
     * Add store filter
     *
     * @param int $storeId
     * @return Mage_Review_Model_Resource_Review_Summary_Collection
     */
    public function addStoreFilter($storeId)
    {
        $this->_select->where('store_id = ?', $storeId);
        return $this;
    }
}
