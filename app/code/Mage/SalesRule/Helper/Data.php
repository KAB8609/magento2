<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_SalesRule
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * SalesRule data helper
 */
class Mage_SalesRule_Helper_Data extends Magento_Core_Helper_Abstract
{
    /**
     * Set store and base price which will be used during discount calculation to item object
     *
     * @param   Mage_Sales_Model_Quote_Item_Abstract $item
     * @param   float $basePrice
     * @param   float $price
     * @return  Mage_SalesRule_Helper_Data
     */
    public function setItemDiscountPrices(Mage_Sales_Model_Quote_Item_Abstract $item, $basePrice, $price)
    {
        $item->setDiscountCalculationPrice($price);
        $item->setBaseDiscountCalculationPrice($basePrice);
        return $this;
    }

    /**
     * Add additional amounts to discount calculation prices
     *
     * @param   Mage_Sales_Model_Quote_Item_Abstract $item
     * @param   float $basePrice
     * @param   float $price
     * @return  Mage_SalesRule_Helper_Data
     */
    public function addItemDiscountPrices(Mage_Sales_Model_Quote_Item_Abstract $item, $basePrice, $price)
    {
        $discountPrice      = $item->getDiscountCalculationPrice();
        $baseDiscountPrice  = $item->getBaseDiscountCalculationPrice();

        if ($discountPrice || $baseDiscountPrice || $basePrice || $price) {
            $discountPrice      = $discountPrice ? $discountPrice : $item->getCalculationPrice();
            $baseDiscountPrice  = $baseDiscountPrice ? $baseDiscountPrice : $item->getBaseCalculationPrice();
            $this->setItemDiscountPrices($item, $baseDiscountPrice+$basePrice, $discountPrice+$price);
        }
        return $this;
    }
}
