<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Directory
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Directory module observer
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Directory_Model_Observer
{
    const CRON_STRING_PATH = 'crontab/jobs/currency_rates_update/schedule/cron_expr';
    const IMPORT_ENABLE = 'currency/import/enabled';
    const IMPORT_SERVICE = 'currency/import/service';

    const XML_PATH_ERROR_TEMPLATE = 'currency/import/error_email_template';
    const XML_PATH_ERROR_IDENTITY = 'currency/import/error_email_identity';
    const XML_PATH_ERROR_RECIPIENT = 'currency/import/error_email';

    public function scheduledUpdateCurrencyRates($schedule)
    {
        $importWarnings = array();
        if(!Mage::getStoreConfig(self::IMPORT_ENABLE) || !Mage::getStoreConfig(self::CRON_STRING_PATH)) {
            return;
        }

        $service = Mage::getStoreConfig(self::IMPORT_SERVICE);
        if( !$service ) {
            $importWarnings[] = __('FATAL ERROR:') . ' ' . __('Please specify the correct Import Service.');
        }

        try {
            $importModel = Mage::getModel(
                Mage::getConfig()->getNode('global/currency/import/services/' . $service . '/model')->asArray()
            );
        } catch (Exception $e) {
            $importWarnings[] = __('FATAL ERROR:') . ' ' . Mage::throwException(__("We can't initialize the import model."));
        }

        $rates = $importModel->fetchRates();
        $errors = $importModel->getMessages();

        if( sizeof($errors) > 0 ) {
            foreach ($errors as $error) {
                $importWarnings[] = __('WARNING:') . ' ' . $error;
            }
        }

        if (sizeof($importWarnings) == 0) {
            Mage::getModel('Magento_Directory_Model_Currency')->saveRates($rates);
        }
        else {
            $translate = Mage::getSingleton('Magento_Core_Model_Translate');
            /* @var $translate Magento_Core_Model_Translate */
            $translate->setTranslateInline(false);

            /* @var $mailTemplate Magento_Core_Model_Email_Template */
            $mailTemplate = Mage::getModel('Magento_Core_Model_Email_Template');
            $mailTemplate->setDesignConfig(array(
                'area' => Magento_Core_Model_App_Area::AREA_FRONTEND,
                'store' => Mage::app()->getStore()->getId()
            ))
                ->sendTransactional(
                    Mage::getStoreConfig(self::XML_PATH_ERROR_TEMPLATE),
                    Mage::getStoreConfig(self::XML_PATH_ERROR_IDENTITY),
                    Mage::getStoreConfig(self::XML_PATH_ERROR_RECIPIENT),
                    null,
                    array('warnings'    => join("\n", $importWarnings),
                )
            );

            $translate->setTranslateInline(true);
        }
    }
}