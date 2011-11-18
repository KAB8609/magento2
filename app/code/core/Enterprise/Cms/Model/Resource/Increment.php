<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Cms
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Increment resource model
 *
 * @category    Enterprise
 * @package     Enterprise_Cms
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Cms_Model_Resource_Increment extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Resource initialization
     */
    protected function _construct()
    {
        $this->_init('enterprise_cms_increment', 'increment_id');
    }

    /**
     * Load increment counter by passed node and level
     *
     * @param Mage_Core_Model_Abstract $object
     * @param int $type
     * @param int $node
     * @param int $level
     * @return bool
     */
    public function loadByTypeNodeLevel(Mage_Core_Model_Abstract $object, $type, $node, $level)
    {
        $read = $this->_getReadAdapter();

        $select = $read->select()->from($this->getMainTable())
            ->forUpdate(true)
            ->where(implode(' AND ', array(
                'increment_type  = :increment_type',
                'increment_node  = :increment_node',
                'increment_level = :increment_level'
             )));

        $bind = array(':increment_type'  => $type,
                      ':increment_node'  => $node,
                      ':increment_level' => $level);

        $data = $read->fetchRow($select, $bind);

        if (!$data) {
            return false;
        }

        $object->setData($data);

        $this->_afterLoad($object);

        return true;
    }

    /**
     * Remove unneeded increment record.
     *
     * @param int $type
     * @param int $node
     * @param int $level
     * @return Enterprise_Cms_Model_Resource_Increment
     */
    public function cleanIncrementRecord($type, $node, $level)
    {
        $this->_getWriteAdapter()->delete($this->getMainTable(),
            array('increment_type = ?'  => $type,
                  'increment_node = ?'  => $node,
                  'increment_level = ?' => $level));

        return $this;
    }
}
