<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Backend Model for Currency import options
 *
 * @category   Magento
 * @package    Magento_Backend
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Backend_Model_Config_Backend_Currency_Cron extends Magento_Core_Model_Config_Data
{

    const CRON_STRING_PATH = 'crontab/jobs/currency_rates_update/schedule/cron_expr';

    protected function _afterSave()
    {
        $time = $this->getData('groups/import/fields/time/value');
        $frequency = $this->getData('groups/import/fields/frequency/value');

        $frequencyWeekly = Magento_Cron_Model_Config_Source_Frequency::CRON_WEEKLY;
        $frequencyMonthly = Magento_Cron_Model_Config_Source_Frequency::CRON_MONTHLY;

        $cronExprArray = array(
            intval($time[1]),                                   # Minute
            intval($time[0]),                                   # Hour
            ($frequency == $frequencyMonthly) ? '1' : '*',       # Day of the Month
            '*',                                                # Month of the Year
            ($frequency == $frequencyWeekly) ? '1' : '*',        # Day of the Week
        );

        $cronExprString = join(' ', $cronExprArray);

        try {
            Mage::getModel('Magento_Core_Model_Config_Data')
                ->load(self::CRON_STRING_PATH, 'path')
                ->setValue($cronExprString)
                ->setPath(self::CRON_STRING_PATH)
                ->save();
        } catch (Exception $e) {
            throw new Exception(Mage::helper('Magento_Cron_Helper_Data')->__('We can\'t save the Cron expression.'));
        }
    }

}
