<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftRegistry
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * HTML select element block
 *
 * @category   Magento
 * @package    Magento_GiftRegistry
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_GiftRegistry_Block_Customer_Date extends Magento_Core_Block_Html_Date
{
    /**
     * Return escaped value
     * Overriding parent method undesired behaviour
     *
     * @param int $index
     *
     * @return string
     */
    public function getEscapedValue($index=null)
    {
        return $this->escapeHtml($this->getValue());
    }
}