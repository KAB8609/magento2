<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Active record implementation
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Core_Model_Resource_Iterator extends Magento_Object
{
    /**
     * Walk over records fetched from query one by one using callback function
     *
     * @param Zend_Db_Statement_Interface|Zend_Db_Select|string $query
     * @param array|string $callbacks
     * @param array $args
     * @param Magento_DB_Adapter_Interface $adapter
     * @return Magento_Core_Model_Resource_Iterator
     */
    public function walk($query, array $callbacks, array $args=array(), $adapter = null)
    {
        $stmt = $this->_getStatement($query, $adapter);
        $args['idx'] = 0;
        while ($row = $stmt->fetch()) {
            $args['row'] = $row;
            foreach ($callbacks as $callback) {
                $result = call_user_func($callback, $args);
                if (!empty($result)) {
                    $args = array_merge($args, (array)$result);
                }
            }
            $args['idx']++;
        }

        return $this;
    }

    /**
     * Fetch Zend statement instance
     *
     * @param Zend_Db_Statement_Interface|Zend_Db_Select|string $query
     * @param Zend_Db_Adapter_Abstract $conn
     * @return Zend_Db_Statement_Interface
     * @throws Magento_Core_Exception
     */
    protected function _getStatement($query, $conn = null)
    {
        if ($query instanceof Zend_Db_Statement_Interface) {
            return $query;
        }

        if ($query instanceof Zend_Db_Select) {
            return $query->query();
        }

        if (is_string($query)) {
            if (!$conn instanceof Zend_Db_Adapter_Abstract) {
                Mage::throwException(__('Invalid connection'));
            }
            return $conn->query($query);
        }

        Mage::throwException(__('Invalid query'));
    }
}