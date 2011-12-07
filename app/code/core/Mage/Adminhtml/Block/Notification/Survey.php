<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml AdminNotification survey question block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Notification_Survey extends Mage_Adminhtml_Block_Template
{
    /**
     * Check whether survey question can show
     *
     * @return boolean
     */
    public function canShow()
    {
        $adminSession = Mage::getSingleton('Mage_Admin_Model_Session');
        $seconds = intval(date('s', time()));
        if ($adminSession->getHideSurveyQuestion() || !$adminSession->isAllowed('all')
            || Mage_AdminNotification_Model_Survey::isSurveyViewed()
            || !Mage_AdminNotification_Model_Survey::isSurveyUrlValid())
        {
            return false;
        }
        return true;
    }

    /**
     * Return survey url
     *
     * @return string
     */
    public function getSurveyUrl()
    {
        return Mage_AdminNotification_Model_Survey::getSurveyUrl();
    }
}
