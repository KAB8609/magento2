<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Backend grid item renderer date
 *
 * @category   Magento
 * @package    Magento_Backend
 * @author     Magento Core Team <core@magentocommerce.com>
 */

namespace Magento\Backend\Block\Widget\Grid\Column\Renderer;

class Date
    extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    protected $_defaultWidth = 160;
    /**
     * Date format string
     */
    protected static $_format = null;

    /**
     * Retrieve date format
     *
     * @return string
     */
    protected function _getFormat()
    {
        $format = $this->getColumn()->getFormat();
        if (!$format) {
            if (is_null(self::$_format)) {
                try {
                    self::$_format = \Mage::app()->getLocale()->getDateFormat(
                        \Magento\Core\Model\LocaleInterface::FORMAT_TYPE_MEDIUM
                    );
                }
                catch (\Exception $e) {
                    \Mage::logException($e);
                }
            }
            $format = self::$_format;
        }
        return $format;
    }

    /**
     * Renders grid column
     *
     * @param   \Magento\Object $row
     * @return  string
     */
    public function render(\Magento\Object $row)
    {
        if ($data = $row->getData($this->getColumn()->getIndex())) {
            $format = $this->_getFormat();
            try {
                if ($this->getColumn()->getGmtoffset()) {
                    $data = \Mage::app()->getLocale()
                        ->date($data, \Magento\Date::DATETIME_INTERNAL_FORMAT)->toString($format);
                } else {
                    $data = \Mage::getSingleton('Magento\Core\Model\LocaleInterface')
                        ->date($data, \Zend_Date::ISO_8601, null, false)->toString($format);
                }
            }
            catch (\Exception $e)
            {
                if ($this->getColumn()->getTimezone()) {
                    $data = \Mage::app()->getLocale()
                        ->date($data, \Magento\Date::DATETIME_INTERNAL_FORMAT)->toString($format);
                } else {
                    $data = \Mage::getSingleton('Magento\Core\Model\LocaleInterface')
                        ->date($data, null, null, false)
                        ->toString($format);
                }
            }
            return $data;
        }
        return $this->getColumn()->getDefault();
    }
}
