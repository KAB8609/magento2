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
 * Locale country source
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Backend\Model\Config\Source\Locale;

class Country implements \Magento\Core\Model\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return \Mage::app()->getLocale()->getOptionCountries();
    }
}
