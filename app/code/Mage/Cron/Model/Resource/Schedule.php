<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Cron
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Schedule resource
 *
 * @category    Mage
 * @package     Mage_Cron
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Cron_Model_Resource_Schedule extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Initialize resource
     *
     */
    public function _construct()
    {
        $this->_init('cron_schedule', 'schedule_id');
    }

    /**
     * If job is currently in $currentStatus, set it to $newStatus
     * and return true. Otherwise, return false and do not change the job.
     * This method is used to implement locking for cron jobs.
     *
     * @param unknown_type $scheduleId
     * @param String $newStatus
     * @param String $currentStatus
     * @return unknown
     */
    public function trySetJobStatusAtomic($scheduleId, $newStatus, $currentStatus)
    {
        $write = $this->_getWriteAdapter();
        $result = $write->update(
            $this->getTable('cron_schedule'),
            array('status' => $newStatus),
            array('schedule_id = ?' => $scheduleId, 'status = ?' => $currentStatus)
        );
        if ($result == 1) {
            return true;
        }
        return false;
    }
}