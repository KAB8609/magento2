<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Backend Model for Currency import options
 *
 * @category   Mage
 * @package    Mage_Backend
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Backend_Model_Config_Backend_Sitemap_Cron extends Mage_Core_Model_Config_Data
{

    const CRON_STRING_PATH = 'crontab/jobs/sitemap_generate/schedule/cron_expr';
    const CRON_MODEL_PATH = 'crontab/jobs/sitemap_generate/run/model';

    protected function _afterSave()
    {
        $enabled = $this->getData('groups/generate/enabled/value');
        //$service = $this->getData('groups/import/fields/service/value');
        $time = $this->getData('groups/generate/fields/time/value');
        $frequncy = $this->getData('groups/generate/frequency/value');
        $errorEmail = $this->getData('groups/generate/error_email/value');

        $frequencyDaily = Mage_Cron_Model_Config_Source_Frequency::CRON_DAILY;
        $frequencyWeekly = Mage_Cron_Model_Config_Source_Frequency::CRON_WEEKLY;
        $frequencyMonthly = Mage_Cron_Model_Config_Source_Frequency::CRON_MONTHLY;

        $cronDayOfWeek = date('N');

        $cronExprArray = array(
            intval($time[1]),                                   # Minute
            intval($time[0]),                                   # Hour
            ($frequncy == $frequencyMonthly) ? '1' : '*',       # Day of the Month
            '*',                                                # Month of the Year
            ($frequncy == $frequencyWeekly) ? '1' : '*',        # Day of the Week
        );

        $cronExprString = join(' ', $cronExprArray);

        try {
            Mage::getModel('Mage_Core_Model_Config_Data')
                ->load(self::CRON_STRING_PATH, 'path')
                ->setValue($cronExprString)
                ->setPath(self::CRON_STRING_PATH)
                ->save();
            Mage::getModel('Mage_Core_Model_Config_Data')
                ->load(self::CRON_MODEL_PATH, 'path')
                ->setValue((string) Mage::getConfig()->getNode(self::CRON_MODEL_PATH))
                ->setPath(self::CRON_MODEL_PATH)
                ->save();
        } catch (Exception $e) {
            throw new Exception(Mage::helper('Mage_Cron_Helper_Data')->__('Unable to save the cron expression.'));
        }
    }

}
