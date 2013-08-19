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
 * Advertising Tooltip block to show different messages for gaining reward points
 *
 * @category    Enterprise
 * @package     Enterprise_Reward
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Reward_Block_Tooltip extends Mage_Core_Block_Template
{
    /**
     * Reward instance
     *
     * @var Enterprise_Reward_Model_Reward
     */
    protected $_rewardInstance = null;

    /**
     * Reward action instance
     *
     * @var Enterprise_Reward_Model_Action_Abstract
     */
    protected $_actionInstance = null;

    public function initRewardType($action)
    {
        if ($action) {
            if (!Mage::helper('Enterprise_Reward_Helper_Data')->isEnabledOnFront()) {
                return $this;
            }
            $customer = Mage::getSingleton('Mage_Customer_Model_Session')->getCustomer();
            $this->_rewardInstance = Mage::getSingleton('Enterprise_Reward_Model_Reward')
                ->setCustomer($customer)
                ->setWebsiteId(Mage::app()->getStore()->getWebsiteId())
                ->loadByCustomer();
            $this->_actionInstance = $this->_rewardInstance->getActionInstance($action, true);
        }
    }

    /**
     * Getter for amount customer may be rewarded for current action
     * Can format as currency
     *
     * @param float $amount
     * @param bool $asCurrency
     * @return string|null
     */
    public function getRewardAmount($amount = null, $asCurrency = false)
    {
        $amount = null === $amount ? $this->_getData('reward_amount') : $amount;
        return Mage::helper('Enterprise_Reward_Helper_Data')->formatAmount($amount, $asCurrency);
    }

    public function renderLearnMoreLink($format = '<a href="%1$s">%2$s</a>', $anchorText = null)
    {
        $anchorText = null === $anchorText ? __('Learn more...') : $anchorText;
        return sprintf($format, $this->getLandingPageUrl(), $anchorText);
    }

    /**
     * Set various template variables
     */
    protected function _prepareTemplateData()
    {
        if ($this->_actionInstance) {
            $this->addData(array(
                'reward_points' => $this->_rewardInstance->estimateRewardPoints($this->_actionInstance),
                'landing_page_url' => Mage::helper('Enterprise_Reward_Helper_Data')->getLandingPageUrl(),
            ));

            if ($this->_rewardInstance->getId()) {
                // estimate qty limitations (actually can be used without customer reward record)
                $qtyLimit = $this->_actionInstance->estimateRewardsQtyLimit();
                if (null !== $qtyLimit) {
                    $this->setData('qty_limit', $qtyLimit);
                }

                if ($this->hasGuestNote()) {
                    $this->unsGuestNote();
                }

                $this->addData(array(
                    'points_balance' => $this->_rewardInstance->getPointsBalance(),
                    'currency_balance' => $this->_rewardInstance->getCurrencyAmount(),
                ));
                // estimate monetary reward
                $amount = $this->_rewardInstance->estimateRewardAmount($this->_actionInstance);
                if (null !== $amount) {
                    $this->setData('reward_amount', $amount);
                }
            } else {
                if ($this->hasIsGuestNote() && !$this->hasGuestNote()) {
                    $this->setGuestNote(__('This applies only to registered users and may vary when a user is logged in.'));
                }
            }
        }
    }

    /**
     * Check whether everything is set for output
     *
     * @return string
     */
    protected function _toHtml()
    {
        $this->_prepareTemplateData();
        if (!$this->_actionInstance || !$this->getRewardPoints() || $this->hasQtyLimit() && !$this->getQtyLimit()) {
            return '';
        }
        return parent::_toHtml();
    }
}
