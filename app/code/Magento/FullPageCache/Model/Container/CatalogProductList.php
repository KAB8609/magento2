<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_FullPageCache
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * Placeholder container for catalog product lists
 */
class Magento_FullPageCache_Model_Container_CatalogProductList
    extends Magento_FullPageCache_Model_Container_Advanced_Quote
{
    /**
     * Render block that was not cached
     *
     * @return false|string
     */
    protected function _renderBlock()
    {
        $productId = $this->_getProductId();
        if ($productId && !Mage::registry('product')) {
            $product = Mage::getModel('Magento_Catalog_Model_Product')
                ->setStoreId(Mage::app()->getStore()->getId())
                ->load($productId);
            if ($product) {
                Mage::register('product', $product);
            }
        }

        if (Mage::registry('product')) {
            $block = $this->_getPlaceHolderBlock();
            Mage::dispatchEvent('render_block', array('block' => $block, 'placeholder' => $this->_placeholder));
            return $block->toHtml();
        }

        return '';
    }
}