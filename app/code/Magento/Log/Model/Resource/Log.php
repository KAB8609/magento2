<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Log
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Log Resource Model
 *
 * @category    Magento
 * @package     Magento_Log
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Log_Model_Resource_Log extends Magento_Core_Model_Resource_Db_Abstract
{
    /**
     * Init Resource model and connection
     *
     */
    protected function _construct()
    {
        $this->_init('log_visitor', 'visitor_id');
    }

    /**
     * Clean logs
     *
     * @param Magento_Log_Model_Log $object
     * @return Magento_Log_Model_Resource_Log
     */
    public function clean(Magento_Log_Model_Log $object)
    {
        $cleanTime = $object->getLogCleanTime();

        Mage::dispatchEvent('log_log_clean_before', array(
            'log'   => $object
        ));

        $this->_cleanVisitors($cleanTime);
        $this->_cleanCustomers($cleanTime);
        $this->_cleanUrls();

        Mage::dispatchEvent('log_log_clean_after', array(
            'log'   => $object
        ));

        return $this;
    }

    /**
     * Clean visitors table
     *
     * @param int $time
     * @return Magento_Log_Model_Resource_Log
     */
    protected function _cleanVisitors($time)
    {
        $readAdapter    = $this->_getReadAdapter();
        $writeAdapter   = $this->_getWriteAdapter();

        $timeLimit = $this->formatDate(Mage::getModel('Magento_Core_Model_Date')->gmtTimestamp() - $time);

        while (true) {
            $select = $readAdapter->select()
                ->from(
                    array('visitor_table' => $this->getTable('log_visitor')),
                    array('visitor_id' => 'visitor_table.visitor_id'))
                ->joinLeft(
                    array('customer_table' => $this->getTable('log_customer')),
                    'visitor_table.visitor_id = customer_table.visitor_id AND customer_table.log_id IS NULL',
                    array())
                ->where('visitor_table.last_visit_at < ?', $timeLimit)
                ->limit(100);

            $visitorIds = $readAdapter->fetchCol($select);

            if (!$visitorIds) {
                break;
            }

            $condition = array('visitor_id IN (?)' => $visitorIds);
            
            // remove visitors from log/quote
            $writeAdapter->delete($this->getTable('log_quote'), $condition);

            // remove visitors from log/url
            $writeAdapter->delete($this->getTable('log_url'), $condition);

            // remove visitors from log/visitor_info
            $writeAdapter->delete($this->getTable('log_visitor_info'), $condition);

            // remove visitors from log/visitor
            $writeAdapter->delete($this->getTable('log_visitor'), $condition);
        }

        return $this;
    }

    /**
     * Clean customer table
     *
     * @param int $time
     * @return Magento_Log_Model_Resource_Log
     */
    protected function _cleanCustomers($time)
    {
        $readAdapter    = $this->_getReadAdapter();
        $writeAdapter   = $this->_getWriteAdapter();

        $timeLimit = $this->formatDate(Mage::getModel('Magento_Core_Model_Date')->gmtTimestamp() - $time);

        // retrieve last active customer log id
        $lastLogId = $readAdapter->fetchOne(
            $readAdapter->select()
                ->from($this->getTable('log_customer'), 'log_id')
                ->where('login_at < ?', $timeLimit)
                ->order('log_id DESC')
                ->limit(1)
        );

        if (!$lastLogId) {
            return $this;
        }

        // Order by desc log_id before grouping (within-group aggregates query pattern)
        $select = $readAdapter->select()
            ->from(
                array('log_customer_main' => $this->getTable('log_customer')),
                array('log_id'))
            ->joinLeft(
                array('log_customer' => $this->getTable('log_customer')),
                'log_customer_main.customer_id = log_customer.customer_id '
                    . 'AND log_customer_main.log_id < log_customer.log_id',
                array())
            ->where('log_customer.customer_id IS NULL')
            ->where('log_customer_main.log_id < ?', $lastLogId + 1);

        $needLogIds = array();
        $query = $readAdapter->query($select);
        while ($row = $query->fetch()) {
            $needLogIds[$row['log_id']] = 1;
        }

        $customerLogId = 0;
        while (true) {
            $visitorIds = array();
            $select = $readAdapter->select()
                ->from(
                    $this->getTable('log_customer'),
                    array('log_id', 'visitor_id'))
                ->where('log_id > ?', $customerLogId)
                ->where('log_id < ?', $lastLogId + 1)
                ->order('log_id')
                ->limit(100);

            $query = $readAdapter->query($select);
            $count = 0;
            while ($row = $query->fetch()) {
                $count++;
                $customerLogId = $row['log_id'];
                if (!isset($needLogIds[$row['log_id']])) {
                    $visitorIds[] = $row['visitor_id'];
                }
            }

            if (!$count) {
                break;
            }

            if ($visitorIds) {
                $condition = array('visitor_id IN (?)' => $visitorIds);

                // remove visitors from log/quote
                $writeAdapter->delete($this->getTable('log_quote'), $condition);

                // remove visitors from log/url
                $writeAdapter->delete($this->getTable('log_url'), $condition);

                // remove visitors from log/visitor_info
                $writeAdapter->delete($this->getTable('log_visitor_info'), $condition);

                // remove visitors from log/visitor
                $writeAdapter->delete($this->getTable('log_visitor'), $condition);

                // remove customers from log/customer
                $writeAdapter->delete($this->getTable('log_customer'), $condition);
            }

            if ($customerLogId == $lastLogId) {
                break;
            }
        }

        return $this;
    }

    /**
     * Clean url table
     *
     * @return Magento_Log_Model_Resource_Log
     */
    protected function _cleanUrls()
    {
        $readAdapter    = $this->_getReadAdapter();
        $writeAdapter   = $this->_getWriteAdapter();

        while (true) {
            $select = $readAdapter->select()
                ->from(
                    array('url_info_table' => $this->getTable('log_url_info')),
                    array('url_id'))
                ->joinLeft(
                    array('url_table' => $this->getTable('log_url')),
                    'url_info_table.url_id = url_table.url_id',
                    array())
                ->where('url_table.url_id IS NULL')
                ->limit(100);

            $urlIds = $readAdapter->fetchCol($select);

            if (!$urlIds) {
                break;
            }

            $writeAdapter->delete(
                $this->getTable('log_url_info'),
                array('url_id IN (?)' => $urlIds)
            );
        }

        return $this;
    }
}