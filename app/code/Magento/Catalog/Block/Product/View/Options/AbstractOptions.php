<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Product options abstract type block
 *
 * @category   Magento
 * @package    Magento_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Catalog\Block\Product\View\Options;

abstract class AbstractOptions extends \Magento\View\Block\Template
{
    /**
     * Product object
     *
     * @var \Magento\Catalog\Model\Product
     */
    protected $_product;

    /**
     * Product option object
     *
     * @var \Magento\Catalog\Model\Product\Option
     */
    protected $_option;

    /**
     * Tax data
     *
     * @var \Magento\Tax\Helper\Data
     */
    protected $_taxData = null;

    /**
     * @param \Magento\Tax\Helper\Data $taxData
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\View\Block\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Tax\Helper\Data $taxData,
        \Magento\Core\Helper\Data $coreData,
        \Magento\View\Block\Template\Context $context,
        array $data = array()
    ) {
        $this->_taxData = $taxData;
        parent::__construct($coreData, $context, $data);
    }

    /**
     * Set Product object
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return \Magento\Catalog\Block\Product\View\Options\AbstractOptions
     */
    public function setProduct(\Magento\Catalog\Model\Product $product = null)
    {
        $this->_product = $product;
        return $this;
    }

    /**
     * Retrieve Product object
     *
     * @return \Magento\Catalog\Model\Product
     */
    public function getProduct()
    {
        return $this->_product;
    }

    /**
     * Set option
     *
     * @param \Magento\Catalog\Model\Product\Option $option
     * @return \Magento\Catalog\Block\Product\View\Options\AbstractOptions
     */
    public function setOption(\Magento\Catalog\Model\Product\Option $option)
    {
        $this->_option = $option;
        return $this;
    }

    /**
     * Get option
     *
     * @return \Magento\Catalog\Model\Product\Option
     */
    public function getOption()
    {
        return $this->_option;
    }

    public function getFormatedPrice()
    {
        if ($option = $this->getOption()) {
            return $this->_formatPrice(array(
                'is_percent'    => ($option->getPriceType() == 'percent'),
                'pricing_value' => $option->getPrice($option->getPriceType() == 'percent')
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

        $store = $this->getProduct()->getStore();

        $sign = '+';
        if ($value['pricing_value'] < 0) {
            $sign = '-';
            $value['pricing_value'] = 0 - $value['pricing_value'];
        }

        $priceStr = $sign;
        $_priceInclTax = $this->getPrice($value['pricing_value'], true);
        $_priceExclTax = $this->getPrice($value['pricing_value']);
        if ($this->_taxData->displayPriceIncludingTax()) {
            $priceStr .= $this->helper('Magento\Core\Helper\Data')->currencyByStore($_priceInclTax, $store, true, $flag);
        } elseif ($this->_taxData->displayPriceExcludingTax()) {
            $priceStr .= $this->helper('Magento\Core\Helper\Data')->currencyByStore($_priceExclTax, $store, true, $flag);
        } elseif ($this->_taxData->displayBothPrices()) {
            $priceStr .= $this->helper('Magento\Core\Helper\Data')->currencyByStore($_priceExclTax, $store, true, $flag);
            if ($_priceInclTax != $_priceExclTax) {
                $priceStr .= ' ('.$sign.$this->helper('Magento\Core\Helper\Data')
                    ->currencyByStore($_priceInclTax, $store, true, $flag).' '.__('Incl. Tax').')';
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
            $price = $this->_taxData->getPrice($this->getProduct(), $price, true);
        } else {
            $price = $this->_taxData->getPrice($this->getProduct(), $price);
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
        return $this->helper('Magento\Core\Helper\Data')->currencyByStore($price, $store, false);
    }
}
