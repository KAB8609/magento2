<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Sitemap
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Sitemap module observer
 *
 * @category   Mage
 * @package    Mage_Sitemap
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Sitemap_Model_Observer
{

    /**
     * Enable/disable configuration
     */
    const XML_PATH_GENERATION_ENABLED = 'sitemap/generate/enabled';

    /**
     * Cronjob expression configuration
     */
    const XML_PATH_CRON_EXPR = 'crontab/jobs/generate_sitemaps/schedule/cron_expr';

    /**
     * Error email template configuration
     */
    const XML_PATH_ERROR_TEMPLATE  = 'sitemap/generate/error_email_template';

    /**
     * Error email identity configuration
     */
    const XML_PATH_ERROR_IDENTITY  = 'sitemap/generate/error_email_identity';

    /**
     * 'Send error emails to' configuration
     */
    const XML_PATH_ERROR_RECIPIENT = 'sitemap/generate/error_email';

    /**
     * Generate sitemaps
     *
     * @param Mage_Cron_Model_Schedule $schedule
     */
    public function scheduledGenerateSitemaps($schedule)
    {
        $errors = array();

        // check if scheduled generation enabled
        if (!Mage::getStoreConfigFlag(self::XML_PATH_GENERATION_ENABLED)) {
            return;
        }

        $collection = Mage::getModel('Mage_Sitemap_Model_Sitemap')->getCollection();
        /* @var $collection Mage_Sitemap_Model_Resource_Sitemap_Collection */
        foreach ($collection as $sitemap) {
            /* @var $sitemap Mage_Sitemap_Model_Sitemap */

            try {
                $sitemap->generateXml();
            }
            catch (Exception $e) {
                $errors[] = $e->getMessage();
            }
        }

        if ($errors && Mage::getStoreConfig(self::XML_PATH_ERROR_RECIPIENT)) {
            $translate = Mage::getSingleton('Mage_Core_Model_Translate');
            /* @var $translate Mage_Core_Model_Translate */
            $translate->setTranslateInline(false);

            $emailTemplate = Mage::getModel('Mage_Core_Model_Email_Template');
            /* @var $emailTemplate Mage_Core_Model_Email_Template */
            $emailTemplate->setDesignConfig(array('area' => 'backend'))
                ->sendTransactional(
                    Mage::getStoreConfig(self::XML_PATH_ERROR_TEMPLATE),
                    Mage::getStoreConfig(self::XML_PATH_ERROR_IDENTITY),
                    Mage::getStoreConfig(self::XML_PATH_ERROR_RECIPIENT),
                    null,
                    array('warnings' => join("\n", $errors))
                );

            $translate->setTranslateInline(true);
        }
    }
}
