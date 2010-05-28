<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Paypal
 * @copyright   Copyright (c) 2010 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/*
 *
 * Paypal Settlement Report — writing side.
 * The resource model
 *
 */
class Mage_Paypal_Model_Mysql4_Report_Settlement extends Mage_Core_Model_Mysql4_Abstract
{
    protected $_rowsTable;
    protected $_read;
    protected $_write;

    protected function _construct()
    {
        $this->_init('paypal/settlement_report', 'report_id');
        $this->_rowsTable = Mage::getSingleton('core/resource')->getTableName('paypal/settlement_report_row');
        $this->_read = $this->_getReadAdapter();
        $this->_write = $this->_getWriteAdapter();
    }

    /**
     *
     * Override parent's save() to skip saving if we need to skip the report.
     * _beforeSave() does not fit as it's result is irrelevant to save().
     *
     */
    public function save(Mage_Core_Model_Abstract $object)
    {
        if ($object->getMaySkipExistingReports() && $this->_exists($object)) {
            return $this;
        } else {
            return parent::save($object);
        }

    }

    protected function _afterSave(Mage_Core_Model_Abstract $object)
    {
        $rows = $object->getRows();
        if (is_array($rows)){
            try {
                $this->_getWriteAdapter()->beginTransaction();
                if ($object->hasData('id')) {
                    $this->_getWriteAdapter()->query(sprintf('DELETE FROM %s WHERE report_id = :report', $this->_rowsTable), array('report' => $object->getId()));
                }
                foreach ($rows as $key => $row) {
                    $rows[$key]['report_id'] = $object->getId();
                }
                $this->_getWriteAdapter()->insertMultiple($this->_rowsTable, $rows);
                $this->_getWriteAdapter()->commit();
            }
            catch (Exception $e) {
                $this->_getWriteAdapter()->rollback();
            }
        }
        return $this;
    }

    /**
     * Load report by account ID and date
     *
     * @param string $accountID
     * @param string $reportDate
     * @return array
     */
    public function loadByAccountAndDate($accountID, $reportDate)
    {
        $select = $this->_getReadAdapter()->select()
            ->from($this->getTable('paypal/settlement_report'))
            ->where('account_id=?',$accountID)
            ->where('report_date=?',$reportDate);

        $result = $this->_getReadAdapter()->fetchRow($select);

        if(!$result) {
            return array();
        }

        return $result;
    }

    protected function _exists($report)
    {
        $report = $this->loadByAccountAndDate($report->getAccountID(), $report->getReportDate());
        return (bool) count($report);
    }
}
