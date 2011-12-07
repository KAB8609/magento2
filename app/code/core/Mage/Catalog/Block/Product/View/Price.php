<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Catalog product price block
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
 class Mage_Catalog_Block_Product_View_Price extends Mage_Core_Block_Template
 {
    public function getPrice()
    {
        $product = Mage::registry('product');
        /*if($product->isConfigurable()) {
            $price = $product->getCalculatedPrice((array)$this->getRequest()->getParam('super_attribute', array()));
            return Mage::app()->getStore()->formatPrice($price);
        }*/

        return $product->getFormatedPrice();
    }
 }
