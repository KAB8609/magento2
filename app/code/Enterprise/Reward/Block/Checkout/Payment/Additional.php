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
 * Checkout reward payment block
 *
 * @category    Enterprise
 * @package     Enterprise_Reward
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Reward_Block_Checkout_Payment_Additional extends Magento_Core_Block_Template
{
    /**
     * Getter
     *
     * @return Magento_Customer_Model_Customer
     */
    public function getCustomer()
    {
        return Mage::getSingleton('Magento_Customer_Model_Session')->getCustomer();
    }

    /**
     * Getter
     *
     * @return Mage_Sales_Model_Quote
     */
    public function getQuote()
    {
        return Mage::getSingleton('Magento_Checkout_Model_Session')->getQuote();
    }

    /**
     * Getter
     *
     * @return Enterprise_Reward_Model_Reward
     */
    public function getReward()
    {
        if (!$this->getData('reward')) {
            $reward = Mage::getModel('Enterprise_Reward_Model_Reward')
                ->setCustomer($this->getCustomer())
                ->setWebsiteId(Mage::app()->getStore()->getWebsiteId())
                ->loadByCustomer();
            $this->setData('reward', $reward);
        }
        return $this->getData('reward');
    }

    /**
     * Return flag from quote to use reward points or not
     *
     * @return boolean
     */
    public function useRewardPoints()
    {
        return (bool)$this->getQuote()->getUseRewardPoints();
    }

    /**
     * Return true if customer can use his reward points.
     * In case if currency amount of his points is more than zero and
     * is not contrary to the Minimum Reward Points Balance to Be Able to Redeem limit.
     *
     * @return bool
     */
    public function getCanUseRewardPoints()
    {
        /** @var $helper Enterprise_Reward_Helper_Data */
        $helper = Mage::helper('Enterprise_Reward_Helper_Data');
        if (!$helper->getHasRates() || !$helper->isEnabledOnFront()) {
            return false;
        }

        $minPointsToUse = $helper->getGeneralConfig('min_points_balance', (int)Mage::app()->getWebsite()->getId());
        return (float)$this->getCurrencyAmount() > 0 && $this->getPointsBalance() >= $minPointsToUse;
    }

    /**
     * Getter
     *
     * @return integer
     */
    public function getPointsBalance()
    {
        return $this->getReward()->getPointsBalance();
    }

    /**
     * Getter
     *
     * @return float
     */
    public function getCurrencyAmount()
    {
        return $this->getReward()->getCurrencyAmount();
    }

    /**
     * Check if customer has enough points to cover total
     *
     * @return bool
     */
    public function isEnoughPoints()
    {
        $baseGrandTotal = $this->getQuote()->getBaseGrandTotal() + $this->getQuote()->getBaseRewardCurrencyAmount();
        return $this->getReward()->isEnoughPointsToCoverAmount($baseGrandTotal);
    }
}
