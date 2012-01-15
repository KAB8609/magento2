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
 * Product options abstract type block
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
abstract class Mage_Catalog_Block_Product_View_Options_Abstract extends Mage_Core_Block_Template
{
    /**
     * Product object
     *
     * @var Mage_Catalog_Model_Product
     */
    protected $_product;

    /**
     * Product option object
     *
     * @var Mage_Catalog_Model_Product_Option
     */
    protected $_option;

    /**
     * Set Product object
     *
     * @param Mage_Catalog_Model_Product $product
     * @return Mage_Catalog_Block_Product_View_Options_Abstract
     */
    public function setProduct(Mage_Catalog_Model_Product $product = null)
    {
        $this->_product = $product;
        return $this;
    }

    /**
     * Retrieve Product object
     *
     * @return Mage_Catalog_Model_Product
     */
    public function getProduct()
    {
        return $this->_product;
    }

    /**
     * Set option
     *
     * @param Mage_Catalog_Model_Product_Option $option
     * @return Mage_Catalog_Block_Product_View_Options_Abstract
     */
    public function setOption(Mage_Catalog_Model_Product_Option $option)
    {
        $this->_option = $option;
        return $this;
    }

    /**
     * Get option
     *
     * @return Mage_Catalog_Model_Product_Option
     */
    public function getOption()
    {
        return $this->_option;
    }

    public function getFormatedPrice()
    {
        if ($option = $this->getOption()) {
            return $this->_formatPrice(array(
                'is_percent' => ($option->getPriceType() == 'percent') ? true : false,
                'pricing_value' => $option->getPrice(true)
            ));
        }
        return '';
    }

    /**
     * Return formated price
     *
     * @param array $value
     * @return string
     */
    protected function _formatPrice($value, $flag=true)
    {
        if ($value['pricing_value'] == 0) {
            return '';
        }

        $taxHelper = Mage::helper('Mage_Tax_Helper_Data');
        $store = $this->getProduct()->getStore();

        $sign = '+';
        if ($value['pricing_value'] < 0) {
            $sign = '-';
            $value['pricing_value'] = 0 - $value['pricing_value'];
        }
        if (!empty($value['is_percent'])) {
            $priceStr = $sign . $this->helper('Mage_Core_Helper_Data')->currencyByStore($value['pricing_value'], $store, false, $flag)
                . '%';
        } else {
            $priceStr = $sign;
            $_priceInclTax = $this->getPrice($value['pricing_value'], true);
            $_priceExclTax = $this->getPrice($value['pricing_value']);
            if ($taxHelper->displayPriceIncludingTax()) {
                $priceStr .= $this->helper('Mage_Core_Helper_Data')->currencyByStore($_priceInclTax, $store, true, $flag);
            } elseif ($taxHelper->displayPriceExcludingTax()) {
                $priceStr .= $this->helper('Mage_Core_Helper_Data')->currencyByStore($_priceExclTax, $store, true, $flag);
            } elseif ($taxHelper->displayBothPrices()) {
                $priceStr .= $this->helper('Mage_Core_Helper_Data')->currencyByStore($_priceExclTax, $store, true, $flag);
                if ($_priceInclTax != $_priceExclTax) {
                    $priceStr .= ' ('.$sign.$this->helper('core')
                        ->currencyByStore($_priceInclTax, $store, true, $flag).' '.$this->__('Incl. Tax').')';
                }
            }
        }

        if ($flag) {
            $priceStr = '<span class="price-notice">'.$priceStr.'</span>';
        }

        return $priceStr;
    }

    /**
     * Get price with including/excluding tax
     *
     * @param decimal $price
     * @param bool $includingTax
     * @return decimal
     */
    public function getPrice($price, $includingTax = null)
    {
        if (!is_null($includingTax)) {
            $price = Mage::helper('Mage_Tax_Helper_Data')->getPrice($this->getProduct(), $price, true);
        } else {
            $price = Mage::helper('Mage_Tax_Helper_Data')->getPrice($this->getProduct(), $price);
        }
        return $price;
    }

    /**
     * Returns price converted to current currency rate
     *
     * @param float $price
     * @return float
     */
    public function getCurrencyPrice($price)
    {
        $store = $this->getProduct()->getStore();
        return $this->helper('Mage_Core_Helper_Data')->currencyByStore($price, $store, false);
    }
}
