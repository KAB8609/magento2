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
 * Enterprise SalesArchive Mysql resource helper model
 *
 * @category    Magento
 * @package     Magento_SalesArchive
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_SalesArchive_Model_Resource_Helper_Mysql4 extends Magento_Core_Model_Resource_Helper_Mysql4
{
    /**
     * @param string $modulePrefix
     */
    public function __construct($modulePrefix = 'SalesArchive')
    {
        parent::__construct($modulePrefix);
    }

    /**
     * Change columns position
     *
     * @param string $table
     * @param string $column
     * @param boolean $after
     * @param boolean $first
     * @return Magento_SalesArchive_Model_Resource_Helper_Mysql4
     */
    public function changeColumnPosition($table, $column, $after = false, $first = false)
    {
        if ($after && $first) {
            if (is_string($after)) {
                $first = false;
            } else {
                $after = false;
            }
        } elseif (!$after && !$first) {
            // If no new position specified
            return $this;
        }

        if (!$this->_getWriteAdapter()->isTableExists($table)) {
            throw new Magento_Core_Exception(__("We can't find the table."));
        }

        $columns = array();
        $adapter = $this->_getWriteAdapter();
        $description = $adapter->describeTable($table);
        foreach ($description as $columnDescription) {
            $columns[$columnDescription['COLUMN_NAME']] = $adapter->getColumnDefinitionFromDescribe($columnDescription);
        }

        if (!isset($columns[$column])) {
            throw new Magento_Core_Exception(__('Column not found'));
        } elseif ($after && !isset($columns[$after])) {
            throw new Magento_Core_Exception(__('Positioning column not found'));
        }

        if ($after) {
            $sql = sprintf(
                'ALTER TABLE %s MODIFY COLUMN %s %s AFTER %s',
                $adapter->quoteIdentifier($table),
                $adapter->quoteIdentifier($column),
                $columns[$column],
                $adapter->quoteIdentifier($after)
            );
        } else {
            $sql = sprintf(
                'ALTER TABLE %s MODIFY COLUMN %s %s FIRST',
                $adapter->quoteIdentifier($table),
                $adapter->quoteIdentifier($column),
                $columns[$column]
            );
        }

        $adapter->query($sql);

        return $this;
    }
}
