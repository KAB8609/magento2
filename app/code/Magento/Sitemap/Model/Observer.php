<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sitemap
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Sitemap module observer
 *
 * @category   Magento
 * @package    Magento_Sitemap
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Sitemap\Model;

class Observer
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
     * @param \Magento\Cron\Model\Schedule $schedule
     */
    public function scheduledGenerateSitemaps($schedule)
    {
        $errors = array();

        // check if scheduled generation enabled
        if (!\Mage::getStoreConfigFlag(self::XML_PATH_GENERATION_ENABLED)) {
            return;
        }

        $collection = \Mage::getModel('\Magento\Sitemap\Model\Sitemap')->getCollection();
        /* @var $collection \Magento\Sitemap\Model\Resource\Sitemap\Collection */
        foreach ($collection as $sitemap) {
            /* @var $sitemap \Magento\Sitemap\Model\Sitemap */

            try {
                $sitemap->generateXml();
            }
            catch (\Exception $e) {
                $errors[] = $e->getMessage();
            }
        }

        if ($errors && \Mage::getStoreConfig(self::XML_PATH_ERROR_RECIPIENT)) {
            $translate = \Mage::getSingleton('Magento\Core\Model\Translate');
            /* @var $translate \Magento\Core\Model\Translate */
            $translate->setTranslateInline(false);

            $emailTemplate = \Mage::getModel('\Magento\Core\Model\Email\Template');
            /* @var $emailTemplate \Magento\Core\Model\Email\Template */
            $emailTemplate->setDesignConfig(array('area' => 'backend'))
                ->sendTransactional(
                    \Mage::getStoreConfig(self::XML_PATH_ERROR_TEMPLATE),
                    \Mage::getStoreConfig(self::XML_PATH_ERROR_IDENTITY),
                    \Mage::getStoreConfig(self::XML_PATH_ERROR_RECIPIENT),
                    null,
                    array('warnings' => join("\n", $errors))
                );

            $translate->setTranslateInline(true);
        }
    }
}
