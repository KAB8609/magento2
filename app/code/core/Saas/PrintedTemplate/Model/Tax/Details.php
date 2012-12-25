<?php
/**
 * {license_notice}
 *
 * @category   Saas
 * @package    Saas_PrintedTemplate
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * Tax details calculation model
 * It allows calculate information about each tax aplied on each quote item, and tax info for shipping method
 *
 * @category    Saas
 * @package     Saas_PrintedTemplate
 * @subpackage  Models
 */
class Saas_PrintedTemplate_Model_Tax_Details
{
    /**
     * Tax calculation model
     *
     * @var Mage_Tax_Model_Calculation
     */
    protected $_calculator;

    /**
     * Tax configuration object
     *
     * @var Mage_Tax_Model_Config
     */
    protected $_config;

    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->_calculator  = Mage::getSingleton('Mage_Tax_Model_Calculation');
        $this->_config      = Mage::getSingleton('Mage_Tax_Model_Config');
    }

    /**
     * Calculate tax details information for quote items
     * Return array with tax rates grouped by item IDs
     *
     * @param Mage_Sales_Model_Quote $quote
     * @return array
     */
    public function calculateItemsTaxInfo(Mage_Sales_Model_Quote $quote)
    {
        $taxRateRequest = $this->_prepareRateRequest($quote);

        $rateInfo = array();
        foreach ($quote->getAllAddresses() as $address) {
            foreach ($address->getAllNonNominalItems() as $item) {
                if ($item->getParentItemId()) {
                    continue;
                }

                if ($item->getHasChildren() && $item->isChildrenCalculated()) {
                    foreach ($item->getChildren() as $child) {
                        if ($child->getProduct()->getTaxClassId()) {
                            $rateInfo[$child->getId()] = array();
                            $taxRateRequest->setProductClassId($child->getProduct()->getTaxClassId());
                            foreach ($this->_getRatesInfo($taxRateRequest) as $rate) {
                                $rateInfo[$child->getId()][] = $rate;
                           }
                        }
                    }
                } else if ($item->getProduct()->getTaxClassId()) {
                    $rateInfo[$item->getId()] = array();
                    $taxRateRequest->setProductClassId($item->getProduct()->getTaxClassId());
                    foreach ($this->_getRatesInfo($taxRateRequest) as $rate) {
                        $rateInfo[$item->getId()][] = $rate;
                    }
                }
            }
        }
        return $rateInfo;
    }

    /**
     * Calculate tax details information for shipping method
     *
     * @param Mage_Sales_Model_Quote $quote
     * @return array
     */
    public function calculateShippingTaxInfo(Mage_Sales_Model_Quote $quote)
    {
        $taxRateRequest = $this->_prepareRateRequest($quote);
        $taxClass = $this->_config->getShippingTaxClass($quote->getStore());
        if (!$taxClass) {
            return array();
        }

        $taxRateRequest->setProductClassId($taxClass);

        return $this->_getRatesInfo($taxRateRequest);
    }

    /**
     * Prepare tax rate request object from quote model
     *
     * @param Mage_Sales_Model_Quote $quote
     * @return Varien_Object
     */
    protected function _prepareRateRequest(Mage_Sales_Model_Quote $quote)
    {
        $taxRateRequest = $this->_calculator->setCustomer($quote->getCustomer())
            ->getRateRequest(
                $quote->getShippingAddress(),
                $quote->getBillingAddress(),
                $quote->getCustomerTaxClassId(),
                $quote->getStore()
            );

        return $taxRateRequest;
    }

    /**
     * Retrieve tax rates information based on request object
     *
     * @param Varien_Object $taxRateRequest
     * @return array
     */
    protected function _getRatesInfo($taxRateRequest)
    {
        $isTaxAppliedAfterDiscount = $this->_config->applyTaxAfterDiscount($taxRateRequest->getStore());
        $isDiscountAppliedOnPriceIncludingTax = $this->_config->discountTax($taxRateRequest->getStore());

        $rateInfo = array();
        foreach ($this->_calculator->getAppliedRates($taxRateRequest) as $process) {
            if (!isset($process['rates']) || !isset($process['rates'][0]) || !isset($process['percent'])) {
                continue;
            }
            if (count($process['rates']) > 1) {
                $totalRealPercent = 0;
                foreach ($process['rates'] as $rate) {
                    $totalRealPercent += $rate['percent'];
                }

                // there can be problems with rounding
                $realRateRatio = $process['percent'] / $totalRealPercent;
                foreach ($process['rates'] as $rate) {
                    $rate['real_percent'] = $rate['percent'] * $realRateRatio;
                    $rate['is_tax_after_discount'] = $isTaxAppliedAfterDiscount;
                    $rate['is_discount_on_incl_tax'] = $isDiscountAppliedOnPriceIncludingTax;
                    $rateInfo[] = $rate;
                }
            } else {
                $rate = $process['rates'][0];
                $rate['real_percent'] = $process['percent'];
                $rate['is_tax_after_discount'] = $isTaxAppliedAfterDiscount;
                $rate['is_discount_on_incl_tax'] = $isDiscountAppliedOnPriceIncludingTax;
                $rateInfo[] = $rate;
            }
        }

        return $rateInfo;
    }
}
