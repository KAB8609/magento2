<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Product abstract block
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
abstract class Mage_Catalog_Block_Product_Abstract extends Mage_Core_Block_Template
{
    private $_priceBlock = null;
    private $_priceBlockDefaultTemplate = 'catalog/product/price.phtml';

    /**
     * Enter description here...
     *
     * @param Mage_Catalog_Model_Product $product
     * @param array $additional
     * @return string
     */
    public function getAddToCartUrl($product, $additional = array())
    {
        return $this->helper('checkout/cart')->getAddUrl($product, $additional);
    }

    /**
     * Enter description here...
     *
     * @param Mage_Catalog_Model_Product $product
     * @return string
     */
    public function getAddToWishlistUrl($product)
    {
        return $this->getUrl('wishlist/index/add',array('product'=>$product->getId()));
    }

    /**
     * Enter description here...
     *
     * @param Mage_Catalog_Model_Product $product
     * @return string
     */
    public function getAddToCompareUrl($product)
    {
        return $this->helper('catalog/product_compare')->getAddUrl($product);
    }


    protected function _getPriceBlock()
    {
        if (is_null($this->_priceBlock)) {
            $className = Mage::getConfig()->getBlockClassName('catalog/product_price');
            $block = new $className();
            $block->setType('catalog/product_price')
                ->setIsAnonymous(true);

            $this->_priceBlock = $block;
        }
        return $this->_priceBlock;
    }

    protected function _getPriceBlockTemplate()
    {
        if (!is_null($this->getPriceBlockTemplate()))
            return $this->getPriceBlockTemplate();

        return $this->_priceBlockDefaultTemplate;
    }

    /**
     * Returns product price block html
     *
     * @param Mage_Catalog_Model_Product $product
     * @param boolean $displayMinimalPrice
     */
    public function getPriceHtml($product, $displayMinimalPrice = false)
    {
        return $this->_getPriceBlock()
            ->setTemplate($this->_getPriceBlockTemplate())
            ->setProduct($product)
            ->setDisplayMinimalPrice($displayMinimalPrice)
            ->toHtml();
    }
}