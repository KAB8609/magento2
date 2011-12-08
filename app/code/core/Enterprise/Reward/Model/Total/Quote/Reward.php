<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Reward
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Reward sales quote total model
 *
 * @category    Enterprise
 * @package     Enterprise_Reward
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Reward_Model_Total_Quote_Reward extends Mage_Sales_Model_Quote_Address_Total_Abstract
{
    public function __construct()
    {
        $this->setCode('reward');
    }

    /**
     * Collect reward totals
     *
     * @param Mage_Sales_Model_Quote_Address $address
     * @return Enterprise_Reward_Model_Total_Quote_Reward
     */
    public function collect(Mage_Sales_Model_Quote_Address $address)
    {
        /* @var $quote Mage_Sales_Model_Quote */
        $quote = $address->getQuote();
        if (!Mage::helper('Enterprise_Reward_Helper_Data')->isEnabledOnFront($quote->getStore()->getWebsiteId())) {
            return $this;
        }

        if (!$quote->getRewardPointsTotalReseted() && $address->getBaseGrandTotal() > 0) {
            $quote->setRewardPointsBalance(0)
                ->setRewardCurrencyAmount(0)
                ->setBaseRewardCurrencyAmount(0);
            $address->setRewardPointsBalance(0)
                ->setRewardCurrencyAmount(0)
                ->setBaseRewardCurrencyAmount(0);
            $quote->setRewardPointsTotalReseted(true);
        }

        if ($address->getBaseGrandTotal() && $quote->getCustomer()->getId() && $quote->getUseRewardPoints()) {
            /* @var $reward Enterprise_Reward_Model_Reward */
            $reward = $quote->getRewardInstance();
            if (!$reward || !$reward->getId()) {
                $reward = Mage::getModel('Enterprise_Reward_Model_Reward')
                    ->setCustomer($quote->getCustomer())
                    ->setCustomerId($quote->getCustomer()->getId())
                    ->setWebsiteId($quote->getStore()->getWebsiteId())
                    ->loadByCustomer();
            }
            $pointsLeft = $reward->getPointsBalance() - $quote->getRewardPointsBalance();
            $rewardCurrencyAmountLeft = ($quote->getStore()->convertPrice($reward->getCurrencyAmount())) - $quote->getRewardCurrencyAmount();
            $baseRewardCurrencyAmountLeft = $reward->getCurrencyAmount() - $quote->getBaseRewardCurrencyAmount();
            if ($baseRewardCurrencyAmountLeft >= $address->getBaseGrandTotal()) {
                $pointsBalanceUsed = $reward->getPointsEquivalent($address->getBaseGrandTotal());
                $pointsCurrencyAmountUsed = $address->getGrandTotal();
                $basePointsCurrencyAmountUsed = $address->getBaseGrandTotal();

                $address->setGrandTotal(0);
                $address->setBaseGrandTotal(0);
            } else {
                $pointsBalanceUsed = $reward->getPointsEquivalent($baseRewardCurrencyAmountLeft);
                if ($pointsBalanceUsed > $pointsLeft) {
                    $pointsBalanceUsed = $pointsLeft;
                }
                $pointsCurrencyAmountUsed = $rewardCurrencyAmountLeft;
                $basePointsCurrencyAmountUsed = $baseRewardCurrencyAmountLeft;

                $address->setGrandTotal($address->getGrandTotal() - $pointsCurrencyAmountUsed);
                $address->setBaseGrandTotal($address->getBaseGrandTotal() - $basePointsCurrencyAmountUsed);
            }
            $quote->setRewardPointsBalance($quote->getRewardPointsBalance() + $pointsBalanceUsed);
            $quote->setRewardCurrencyAmount($quote->getRewardCurrencyAmount() + $pointsCurrencyAmountUsed);
            $quote->setBaseRewardCurrencyAmount($quote->getBaseRewardCurrencyAmount() + $basePointsCurrencyAmountUsed);

            $address->setRewardPointsBalance($pointsBalanceUsed);
            $address->setRewardCurrencyAmount($pointsCurrencyAmountUsed);
            $address->setBaseRewardCurrencyAmount($basePointsCurrencyAmountUsed);
        }
        return $this;
    }

    /**
     * Retrieve reward total data and set it to quote address
     *
     * @param Mage_Sales_Model_Quote_Address $address
     * @return Enterprise_Reward_Model_Total_Quote_Reward
     */
    public function fetch(Mage_Sales_Model_Quote_Address $address)
    {
        $websiteId = $address->getQuote()->getStore()->getWebsiteId();
        if (!Mage::helper('Enterprise_Reward_Helper_Data')->isEnabledOnFront($websiteId)) {
            return $this;
        }
        if ($address->getRewardCurrencyAmount()) {
            $address->addTotal(array(
                'code'  => $this->getCode(),
                'title' => Mage::helper('Enterprise_Reward_Helper_Data')->formatReward($address->getRewardPointsBalance()),
                'value' => -$address->getRewardCurrencyAmount()
            ));
        }
        return $this;
    }
}
