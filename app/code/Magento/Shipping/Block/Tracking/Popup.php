<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Shipping
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Shipping\Block\Tracking;

class Popup extends \Magento\View\Block\Template
{
    /**
     * Core registry
     *
     * @var \Magento\Core\Model\Registry
     */
    protected $_coreRegistry;

    /**
     * Core registry
     *
     * @var \Magento\Core\Model\LocaleInterface
     */
    protected $_locale;

    /**
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Core\Block\Template\Context $context
     * @param \Magento\Core\Model\Registry $registry
     * @param \Magento\Core\Model\LocaleInterface $locale
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Helper\Data $coreData,
        \Magento\Core\Block\Template\Context $context,
        \Magento\Core\Model\Registry $registry,
        \Magento\Core\Model\LocaleInterface $locale,
        array $data = array()
    ) {
        $this->_coreRegistry = $registry;
        $this->_locale = $locale;
        parent::__construct($coreData, $context, $data);
    }

    /**
     * Retrieve array of tracking info
     *
     * @return array
     */
    public function getTrackingInfo()
    {
        /* @var $info \Magento\Shipping\Model\Info */
        $info = $this->_coreRegistry->registry('current_shipping_info');

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
        $format = $this->_locale->getDateFormat(\Magento\Core\Model\LocaleInterface::FORMAT_TYPE_MEDIUM);
        return $this->_locale->date(strtotime($date), \Zend_Date::TIMESTAMP, null, false)
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

        $format = $this->_locale->getTimeFormat(\Magento\Core\Model\LocaleInterface::FORMAT_TYPE_SHORT);
        return $this->_locale->date(strtotime($time), \Zend_Date::TIMESTAMP, null, false)
            ->toString($format);
    }

    /**
     * Is 'contact us' option enabled?
     *
     * @return boolean
     */
    public function getContactUsEnabled()
    {
        return (bool) $this->_storeConfig->getConfig('contacts/contacts/enabled');
    }

    public function getStoreSupportEmail()
    {
        return $this->_storeConfig->getConfig('trans_email/ident_support/email');
    }

    public function getContactUs()
    {
        return $this->getUrl('contacts');
    }

}
