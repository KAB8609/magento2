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
 */
namespace Magento\Backend\Block\System\Config\Form\Field;

class Notification extends \Magento\Backend\Block\System\Config\Form\Field
{
    protected function _getElementHtml(\Magento\Data\Form\Element\AbstractElement $element)
    {
        $element->setValue($this->_app->loadCache('admin_notifications_lastcheck'));
        $format = $this->_app->getLocale()->getDateTimeFormat(
            \Magento\Core\Model\LocaleInterface::FORMAT_TYPE_MEDIUM
        );
        return $this->_app->getLocale()->date(intval($element->getValue()))->toString($format);
    }
}
