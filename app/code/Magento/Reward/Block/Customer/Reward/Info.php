<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Reward
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Customer account reward points balance block
 *
 * @category    Magento
 * @package     Magento_Reward
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Reward_Block_Customer_Reward_Info extends Magento_Core_Block_Template
{
    /**
     * Reward pts model instance
     *
     * @var Magento_Reward_Model_Reward
     */
    protected $_rewardInstance = null;

    /**
     * Render if all there is a customer and a balance
     *
     * @return string
     */
    protected function _toHtml()
    {
        $customer = Mage::getSingleton('Magento_Customer_Model_Session')->getCustomer();
        if ($customer && $customer->getId()) {
            $this->_rewardInstance = Mage::getModel('Magento_Reward_Model_Reward')
                ->setCustomer($customer)
                ->setWebsiteId(Mage::app()->getWebsite()->getId())
                ->loadByCustomer();
            if ($this->_rewardInstance->getId()) {
                $this->_prepareTemplateData();
                return parent::_toHtml();
            }
        }
        return '';
    }

    /**
     * Set various variables requested by template
     */
    protected function _prepareTemplateData()
    {
        $helper = Mage::helper('Magento_Reward_Helper_Data');
        $maxBalance = (int)$helper->getGeneralConfig('max_points_balance');
        $minBalance = (int)$helper->getGeneralConfig('min_points_balance');
        $balance = $this->_rewardInstance->getPointsBalance();
        $this->addData(array(
            'points_balance' => $balance,
            'currency_balance' => $this->_rewardInstance->getCurrencyAmount(),
            'pts_to_amount_rate_pts' => $this->_rewardInstance->getRateToCurrency()->getPoints(true),
            'pts_to_amount_rate_amount' => $this->_rewardInstance->getRateToCurrency()->getCurrencyAmount(),
            'amount_to_pts_rate_amount' => $this->_rewardInstance->getRateToPoints()->getCurrencyAmount(),
            'amount_to_pts_rate_pts' => $this->_rewardInstance->getRateToPoints()->getPoints(true),
            'max_balance' => $maxBalance,
            'is_max_balance_reached' => $balance >= $maxBalance,
            'min_balance' => $minBalance,
            'is_min_balance_reached' => $balance >= $minBalance,
            'expire_in' => (int)$helper->getGeneralConfig('expiration_days'),
            'is_history_published' => (int)$helper->getGeneralConfig('publish_history'),
        ));
    }
}