<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Dataflow
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * DataFlow Import resource model
 *
 * @category    Mage
 * @package     Mage_Dataflow
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Dataflow_Model_Resource_Import extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Define main table
     *
     */
    protected function _construct()
    {
        $this->_init('dataflow_import_data', 'import_id');
    }

    /**
     * Returns all import data select by session id
     *
     * @param int $sessionId
     * @return Varien_Db_Select
     */
    public function select($sessionId)
    {
        $select = $this->_getReadAdapter()->select()
            ->from($this->getMainTable())
            ->where('session_id=?', $sessionId)
            ->where('status=?', 0);
        return $select;
    }

    /**
     * Load all import data by session id
     *
     * @param int $sessionId
     * @param int $min
     * @param int $max
     * @return array
     */
    public function loadBySessionId($sessionId, $min = 0, $max = 100)
    {
        if (!is_numeric($min) || !is_numeric($max)) {
            return array();
        }
        $bind = array(
            'status'     => 0,
            'session_id' => $sessionId,
            'min_id'     => (int)$min,
            'max_id'     => (int)$max,
        );
        $read = $this->_getReadAdapter();
        $select = $read->select()
            ->from($this->getTable('dataflow_import_data'))
            ->where('import_id >= :min_id')
            ->where('import_id >= :max_id')
            ->where('status= :status')
            ->where('session_id = :session_id');
        return $read->fetchAll($select, $bind);
    }

    /**
     * Load total import data by session id
     *
     * @param int $sessionId
     * @return array
     */
    public function loadTotalBySessionId($sessionId)
    {
        $bind = array(
            'status'    => 0,
            'session_id' => $sessionId
        );
        $read = $this->_getReadAdapter();
        $select = $read->select()
            ->from($this->getTable('dataflow_import_data'),
                array('max'=>'max(import_id)', 'min'=>'min(import_id)', 'cnt'=>'count(*)'))
            ->where('status = :status')
            ->where('session_id = :$session_id');
        return $read->fetchRow($select, $bind);
    }

    /**
     * Load import data by id
     *
     * @param int $importId
     * @return array
     */
    public function loadById($importId)
    {
        $bind = array(
            'status'    => 0,
            'import_id' => $importId
        );
        $read = $this->_getReadAdapter();
        $select = $read->select()
            ->from($this->getTable('dataflow_import_data'))
            ->where('status = :status')
            ->where('import_id = :import_id');
        return $read->fetchRow($select, $bind);
    }
}
