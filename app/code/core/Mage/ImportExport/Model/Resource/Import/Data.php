<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_ImportExport
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * ImportExport import data resource model
 *
 * @category    Mage
 * @package     Mage_ImportExport
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_ImportExport_Model_Resource_Import_Data
    extends Mage_Core_Model_Resource_Db_Abstract
    implements IteratorAggregate
{
    /**
     * @var IteratorIterator
     */
    protected $_iterator = null;

    /**
     * Resource initialization
     */
    protected function _construct()
    {
        $this->_init('importexport_importdata', 'id');
    }

    /**
     * Retrieve an external iterator
     *
     * @return IteratorIterator
     */
    public function getIterator()
    {
        $adapter = $this->_getWriteAdapter();
        $select = $adapter->select()
            ->from($this->getMainTable(), array('data'))
            ->order('id ASC');
        $stmt = $adapter->query($select);

        $stmt->setFetchMode(Zend_Db::FETCH_NUM);
        if ($stmt instanceof IteratorAggregate) {
            $iterator = $stmt->getIterator();
        } else {
            // Statement doesn't support iterating, so fetch all records and create iterator ourself
            $rows = $stmt->fetchAll();
            $iterator = new ArrayIterator($rows);
        }

        return $iterator;
    }

    /**
     * Clean all bunches from table.
     *
     * @return Varien_Db_Adapter_Interface
     */
    public function cleanBunches()
    {
        return $this->_getWriteAdapter()->delete($this->getMainTable());
    }

    /**
     * Return behavior from import data table.
     *
     * @return string
     */
    public function getBehavior()
    {
        return $this->getRequestData('behavior');
    }

    /**
     * Return entity type code from import data table.
     *
     * @return string
     */
    public function getEntityTypeCode()
    {
        return $this->getRequestData('entity');
    }

    /**
     * Return request data from import data table
     *
     * @throws Mage_Core_Exception
     *
     * @param string $code parameter name
     * @return string
     */
    public function getRequestData($code)
    {
        $adapter = $this->_getReadAdapter();
        $values = array_unique($adapter->fetchCol(
            $adapter->select()
                ->from($this->getMainTable(), array($code))
        ));

        if (count($values) != 1) {
            Mage::throwException(
                Mage::helper('Mage_ImportExport_Helper_Data')->__('Error in data structure: '.$code.' values are mixed')
            );
        }
        return $values[0];
    }

    /**
     * Get next bunch of validated rows.
     *
     * @return array|null
     */
    public function getNextBunch()
    {
        if (null === $this->_iterator) {
            $this->_iterator = $this->getIterator();
            $this->_iterator->rewind();
        }
        if ($this->_iterator->valid()) {
            $dataRow = $this->_iterator->current();
            $dataRow = Mage::helper('Mage_Core_Helper_Data')->jsonDecode($dataRow[0]);
            $this->_iterator->next();
        } else {
            $this->_iterator = null;
            $dataRow = null;
        }
        return $dataRow;
    }

    /**
     * Save import rows bunch.
     *
     * @param string $entity
     * @param string $behavior
     * @param array $data
     * @param string|null $entity_subtype
     * @return int
     */
    public function saveBunch($entity, $behavior, array $data, $entity_subtype = null)
    {
        return $this->_getWriteAdapter()->insert(
            $this->getMainTable(),
            array(
                'behavior'       => $behavior,
                'entity'         => $entity,
                'entity_subtype' => $entity_subtype,
                'data'           => Mage::helper('Mage_Core_Helper_Data')->jsonEncode($data)
            )
        );
    }
}
