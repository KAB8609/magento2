<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Logging
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Custom date column filter for logging archive grid
 *
 * @category   Magento
 * @package    Magento_Logging
 */
class Magento_Logging_Block_Adminhtml_Archive_Grid_Filter_Date extends Magento_Adminhtml_Block_Widget_Grid_Column_Filter_Date
{
    /**
     * Convert date from localized to internal format
     *
     * @param string $date
     * @param string $locale
     * @return string
     */
    protected function _convertDate($date, $locale)
    {
        $filterInput = new Zend_Filter_LocalizedToNormalized(array(
            'date_format' => Mage::app()->getLocale()->getDateFormat(Magento_Core_Model_LocaleInterface::FORMAT_TYPE_SHORT)
        ));
        $filterInternal = new Zend_Filter_NormalizedToLocalized(array(
            'date_format' => \Magento\Date::DATE_INTERNAL_FORMAT
        ));
        $date = $filterInput->filter($date);
        $date = $filterInternal->filter($date);

        return $date;
    }
}
