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
namespace Magento\Logging\Block\Adminhtml\Archive\Grid\Filter;

class Date extends \Magento\Adminhtml\Block\Widget\Grid\Column\Filter\Date
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
        $filterInput = new \Zend_Filter_LocalizedToNormalized(array(
            'date_format' => \Mage::app()->getLocale()->getDateFormat(\Magento\Core\Model\LocaleInterface::FORMAT_TYPE_SHORT)
        ));
        $filterInternal = new \Zend_Filter_NormalizedToLocalized(array(
            'date_format' => \Magento\Date::DATE_INTERNAL_FORMAT
        ));
        $date = $filterInput->filter($date);
        $date = $filterInternal->filter($date);

        return $date;
    }
}
