<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_AdvancedCheckout
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * SKU failed products Block
 *
 * @category   Magento
 * @package    Magento_AdvancedCheckout
 */
namespace Magento\AdvancedCheckout\Block\Sku;

class Products extends \Magento\Checkout\Block\Cart
{
    /**
     * Return list of product items
     *
     * @return array
     */
    public function getItems()
    {
        return $this->_getHelper()->getFailedItems();
    }

    /**
     * Retrieve helper instance
     *
     * @return \Magento\AdvancedCheckout\Helper\Data
     */
    protected function _getHelper()
    {
        return \Mage::helper('Magento\AdvancedCheckout\Helper\Data');
    }

    /**
     * Retrieve link for deleting all failed items
     *
     * @return string
     */
    public function getDeleteAllItemsUrl()
    {
        return $this->getUrl('checkout/cart/removeAllFailed');
    }

    /**
     * Retrieve failed items form action URL
     *
     * @return string
     */
    public function getFormActionUrl()
    {
        return $this->getUrl('checkout/cart/addFailedItems');
    }

    /**
     * Prepare cart items URLs
     */
    public function prepareItemUrls()
    {
        $products = array();
        /* @var $item \Magento\Sales\Model\Quote\Item */
        foreach ($this->getItems() as $item) {
            if ($item->getProductType() == 'undefined') {
                continue;
            }
            $product    = $item->getProduct();
            $option     = $item->getOptionByCode('product_type');
            if ($option) {
                $product = $option->getProduct();
            }

            if ($item->getStoreId() != \Mage::app()->getStore()->getId()
                && !$item->getRedirectUrl()
                && !$product->isVisibleInSiteVisibility())
            {
                $products[$product->getId()] = $item->getStoreId();
            }
        }

        if ($products) {
            $products = \Mage::getResourceSingleton('\Magento\Catalog\Model\Resource\Url')
                ->getRewriteByProductStore($products);
            foreach ($this->getItems() as $item) {
                if ($item->getProductType() == 'undefined') {
                    continue;
                }
                $product    = $item->getProduct();
                $option     = $item->getOptionByCode('product_type');
                if ($option) {
                    $product = $option->getProduct();
                }

                if (isset($products[$product->getId()])) {
                    $object = new \Magento\Object($products[$product->getId()]);
                    $item->getProduct()->setUrlDataObject($object);
                }
            }
        }
    }

    /**
     * Get item row html
     *
     * @param \Magento\Sales\Model\Quote\Item $item
     * @return string
     */
    public function getItemHtml(\Magento\Sales\Model\Quote\Item $item)
    {
        /** @var $renderer \Magento\Checkout\Block\Cart\Item\Renderer */
        $renderer = $this->getItemRenderer($item->getProductType())->setQtyMode(false);
        if ($item->getProductType() == 'undefined') {
            $renderer->overrideProductThumbnail($this->helper('\Magento\Catalog\Helper\Image')->init($item, 'thumbnail'));
            $renderer->setProductName('');
        }
        $renderer->setDeleteUrl(
            $this->getUrl('checkout/cart/removeFailed', array(
                'sku' => \Mage::helper('Magento\Core\Helper\Url')->urlEncode($item->getSku())
            ))
        );
        $renderer->setIgnoreProductUrl(!$this->showItemLink($item));

        // Don't display subtotal column
        $item->setNoSubtotal(true);
        return parent::getItemHtml($item);
    }

    /**
     * Check whether item link should be rendered
     *
     * @param \Magento\Sales\Model\Quote\Item $item
     * @return bool
     */
    public function showItemLink(\Magento\Sales\Model\Quote\Item $item)
    {
        $product = $item->getProduct();
        if ($product->isComposite()) {
            $productsByGroups = $product->getTypeInstance()->getProductsToPurchaseByReqGroups($product);
            foreach ($productsByGroups as $productsInGroup) {
                foreach ($productsInGroup as $childProduct) {
                    if (($childProduct->hasStockItem() && $childProduct->getStockItem()->getIsInStock())
                        && !$childProduct->isDisabled()
                    ) {
                        return true;
                    }
                }
            }
            return false;
        }
        return true;
    }

    /**
     * Added failed items existence validation before block html generation
     *
     * @return string
     */
    protected function _toHtml()
    {
        if (\Mage::getSingleton('Magento\AdvancedCheckout\Model\Cart')->getFailedItems()) {
            $html = parent::_toHtml();
        } else {
            $html = '';
        }
        return $html;
    }
}
