<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Paypal
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Paypal_Model_System_Config_Backend_Cron extends Mage_Core_Model_Config_Value
{
    const CRON_STRING_PATH = 'crontab/jobs/paypal_fetch_settlement_reports/schedule/cron_expr';
    const CRON_MODEL_PATH_INTERVAL = 'paypal/fetch_reports/schedule';

    /**
     * Cron settings after save
     * @return void
     */
    protected function _afterSave()
    {
        $cronExprString = '';
        $time = explode(',', Mage::getModel('Mage_Core_Model_Config_Value')->load('paypal/fetch_reports/time', 'path')->getValue());
        if (Mage::getModel('Mage_Core_Model_Config_Value')->load('paypal/fetch_reports/active', 'path')->getValue()) {
            $interval = Mage::getModel('Mage_Core_Model_Config_Value')->load(self::CRON_MODEL_PATH_INTERVAL, 'path')->getValue();
            $cronExprString = "{$time[1]} {$time[0]} */{$interval} * *";
        }

        Mage::getModel('Mage_Core_Model_Config_Value')
            ->load(self::CRON_STRING_PATH, 'path')
            ->setValue($cronExprString)
            ->setPath(self::CRON_STRING_PATH)
            ->save();

        return parent::_afterSave();
    }
}
