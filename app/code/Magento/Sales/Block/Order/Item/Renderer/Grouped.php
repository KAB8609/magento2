<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Order item render block for grouped product type
 *
 * @category    Magento
 * @package     Magento_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Sales\Block\Order\Item\Renderer;

class Grouped extends \Magento\Sales\Block\Order\Item\Renderer\DefaultRenderer
{
    /**
     * Prepare item html
     *
     * This method uses renderer for real product type
     *
     * @return string
     */
    protected function _toHtml()
    {
        if ($this->getItem()->getOrderItem()) {
            $item = $this->getItem()->getOrderItem();
        } else {
            $item = $this->getItem();
        }
        if ($productType = $item->getRealProductType()) {
            $renderer = $this->getRenderedBlock()->getItemRenderer($productType);
            $renderer->setItem($this->getItem());
            return $renderer->toHtml();
        }
        return parent::_toHtml();
    }
}
