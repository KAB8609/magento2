<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Paypal
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Report settlement resource model
 */
namespace Magento\Paypal\Model\Resource\Report;

class Settlement extends \Magento\Core\Model\Resource\Db\AbstractDb
{
    /**
     * Table name
     *
     * @var string
     */
    protected $_rowsTable;

    /**
     * @var \Magento\Core\Model\Date
     */
    protected $_coreDate;

    /**
     * @param \Magento\App\Resource $resource
     * @param \Magento\Core\Model\Date $coreDate
     */
    public function __construct(\Magento\App\Resource $resource, \Magento\Core\Model\Date $coreDate)
    {
        $this->_coreDate = $coreDate;
        parent::__construct($resource);
    }

    /**
     * Init main table
     */
    protected function _construct()
    {
        $this->_init('paypal_settlement_report', 'report_id');
        $this->_rowsTable = $this->getTable('paypal_settlement_report_row');
    }

    /**
     * Save report rows collected in settlement model
     *
     * @param \Magento\Core\Model\AbstractModel|\Magento\Paypal\Model\Report\Settlement $object
     * @return \Magento\Paypal\Model\Resource\Report\Settlement
     */
    protected function _afterSave(\Magento\Core\Model\AbstractModel $object)
    {
        $rows = $object->getRows();
        if (is_array($rows)) {
            $adapter  = $this->_getWriteAdapter();
            $reportId = (int)$object->getId();
            try {
                $adapter->beginTransaction();
                if ($reportId) {
                    $adapter->delete($this->_rowsTable, array('report_id = ?' => $reportId));
                }

                foreach (array_keys($rows) as $key) {
                    /**
                     * Converting dates
                     */
                    $completionDate = new \Zend_Date($rows[$key]['transaction_completion_date']);
                    $rows[$key]['transaction_completion_date'] = $this->_coreDate
                        ->date(null, $completionDate->getTimestamp());
                    $initiationDate = new \Zend_Date($rows[$key]['transaction_initiation_date']);
                    $rows[$key]['transaction_initiation_date'] = $this->_coreDate
                        ->date(null, $initiationDate->getTimestamp());
                    /*
                     * Converting numeric
                     */
                    $rows[$key]['fee_amount'] = (float)$rows[$key]['fee_amount'];
                    /*
                     * Setting reportId
                     */
                    $rows[$key]['report_id'] = $reportId;
                }
                if (!empty($rows)) {
                    $adapter->insertMultiple($this->_rowsTable, $rows);
                }
                $adapter->commit();
            } catch (\Exception $e) {
                $adapter->rollback();
            }
        }

        return $this;
    }

    /**
     * Check if report with same account and report date already fetched
     *
     * @param \Magento\Paypal\Model\Report\Settlement $report
     * @param string $accountId
     * @param string $reportDate
     * @return \Magento\Paypal\Model\Resource\Report\Settlement
     */
    public function loadByAccountAndDate(\Magento\Paypal\Model\Report\Settlement $report, $accountId, $reportDate)
    {
        $adapter = $this->_getReadAdapter();
        $select  = $adapter->select()
            ->from($this->getMainTable())
            ->where('account_id = :account_id')
            ->where('report_date = :report_date');

        $data = $adapter->fetchRow($select, array(':account_id' => $accountId, ':report_date' => $reportDate));
        if ($data) {
            $report->addData($data);
        }

        return $this;
    }
}
