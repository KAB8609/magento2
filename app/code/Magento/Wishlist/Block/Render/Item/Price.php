<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Wishlist
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Wishlist block for rendering price of item with product
 *
 * @category   Magento
 * @package    Magento_Wishlist
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Wishlist\Block\Render\Item;

class Price extends \Magento\View\Block\Template
{
    /**
     * Returns html for rendering non-configured product
     */
    public function getCleanProductPriceHtml()
    {
        $renderer = $this->getCleanRenderer();
        if (!$renderer) {
            return '';
        }

        $product = $this->getProduct();
        if ($product->canConfigure()) {
            $product = clone $product;
            $product->setCustomOptions(array());
        }

        return $renderer->setProduct($product)
            ->setDisplayMinimalPrice($this->getDisplayMinimalPrice())
            ->setIdSuffix($this->getIdSuffix())
            ->toHtml();
    }
}
