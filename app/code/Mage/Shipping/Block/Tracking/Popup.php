<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Shipping
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Shipping_Block_Tracking_Popup extends Mage_Core_Block_Template
{
    /**
     * Retrieve array of tracking info
     *
     * @return array
     */
    public function getTrackingInfo()
    {
        /* @var $info Mage_Shipping_Model_Info */
        $info = Mage::registry('current_shipping_info');

        return $info->getTrackingInfo();
    }

    /**
     * Format given date and time in current locale without changing timezone
     *
     * @param string $date
     * @param string $time
     * @return string
     */
    public function formatDeliveryDateTime($date, $time)
    {
        return $this->formatDeliveryDate($date) . ' ' . $this->formatDeliveryTime($time);
    }

    /**
     * Format given date in current locale without changing timezone
     *
     * @param string $date
     * @return string
     */
    public function formatDeliveryDate($date)
    {
        /* @var $locale Mage_Core_Model_LocaleInterface */
        $locale = Mage::app()->getLocale();
        $format = $locale->getDateFormat(Mage_Core_Model_LocaleInterface::FORMAT_TYPE_MEDIUM);
        return $locale->date(strtotime($date), Zend_Date::TIMESTAMP, null, false)
            ->toString($format);
    }

    /**
     * Format given time [+ date] in current locale without changing timezone
     *
     * @param string $time
     * @param string $date
     * @return string
     */
    public function formatDeliveryTime($time, $date = null)
    {
        if (!empty($date)) {
            $time = $date . ' ' . $time;
        }

        /* @var $locale Mage_Core_Model_LocaleInterface */
        $locale = Mage::app()->getLocale();

        $format = $locale->getTimeFormat(Mage_Core_Model_LocaleInterface::FORMAT_TYPE_SHORT);
        return $locale->date(strtotime($time), Zend_Date::TIMESTAMP, null, false)
            ->toString($format);
    }

    /**
     * Is 'contact us' option enabled?
     *
     * @return boolean
     */
    public function getContactUsEnabled()
    {
        return (bool) Mage::getStoreConfig('contacts/contacts/enabled');
    }

    public function getStoreSupportEmail()
    {
        return Mage::getStoreConfig('trans_email/ident_support/email');
    }

    public function getContactUs()
    {
        return $this->getUrl('contacts');
    }

}
