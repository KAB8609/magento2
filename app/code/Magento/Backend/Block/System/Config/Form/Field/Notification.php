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
 * Backend system config datetime field renderer
 *
 * @category   Magento
 * @package    Magento_Backend
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Backend_Block_System_Config_Form_Field_Notification extends Magento_Backend_Block_System_Config_Form_Field
{
    protected function _getElementHtml(Magento_Data_Form_Element_Abstract $element)
    {
        $element->setValue(Mage::app()->loadCache('admin_notifications_lastcheck'));
        $format = Mage::app()->getLocale()->getDateTimeFormat(Magento_Core_Model_LocaleInterface::FORMAT_TYPE_MEDIUM);
        return Mage::app()->getLocale()->date(intval($element->getValue()))->toString($format);
    }
}