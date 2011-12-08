<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_ImportExport
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Backend model for import/export log cleaning schedule options
 *
 * @category   Enterprise
 * @package    Enterprise_ImportExport
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_ImportExport_Model_System_Config_Backend_Logclean_Cron extends Mage_Core_Model_Config_Data
{
    /**
     * Cron expression configuration path
     */
    const CRON_STRING_PATH = 'crontab/jobs/enterprise_import_export_log_clean/schedule/cron_expr';

    /**
     * Add cron task
     *
     * @return void
     */
    protected function _afterSave()
    {
        $time = $this->getData('groups/enterprise_import_export_log/fields/time/value');
        $frequency = $this->getData('groups/enterprise_import_export_log/fields/frequency/value');

        $frequencyDaily   = Mage_Adminhtml_Model_System_Config_Source_Cron_Frequency::CRON_DAILY;
        $frequencyWeekly  = Mage_Adminhtml_Model_System_Config_Source_Cron_Frequency::CRON_WEEKLY;
        $frequencyMonthly = Mage_Adminhtml_Model_System_Config_Source_Cron_Frequency::CRON_MONTHLY;

        $cronExprArray = array(
            intval($time[1]),                                   # Minute
            intval($time[0]),                                   # Hour
            ($frequency == $frequencyMonthly) ? '1' : '*',      # Day of the Month
            '*',                                                # Month of the Year
            ($frequency == $frequencyWeekly) ? '1' : '*',       # Day of the Week
        );

        $cronExprString = join(' ', $cronExprArray);

        try {
            Mage::getModel('Mage_Core_Model_Config_Data')
                ->load(self::CRON_STRING_PATH, 'path')
                ->setValue($cronExprString)
                ->setPath(self::CRON_STRING_PATH)
                ->save();
        } catch (Exception $e) {
            throw new Exception(Mage::helper('Mage_Cron_Helper_Data')->__('Unable to save the cron expression.'));
        }
    }

}
