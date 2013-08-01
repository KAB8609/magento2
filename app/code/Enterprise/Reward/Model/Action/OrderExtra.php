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
 * Reward action for converting spent money to points
 *
 * @category    Enterprise
 * @package     Enterprise_Reward
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Reward_Model_Action_OrderExtra extends Enterprise_Reward_Model_Action_Abstract
{
    /**
     * Quote instance, required for estimating checkout reward (order subtotal - discount)
     *
     * @var Mage_Sales_Model_Quote
     */
    protected $_quote = null;

    /**
     * Return action message for history log
     *
     * @param array $args Additional history data
     * @return string
     */
    public function getHistoryMessage($args = array())
    {
        $incrementId = isset($args['increment_id']) ? $args['increment_id'] : '';
        return Mage::helper('Enterprise_Reward_Helper_Data')->__('Earned points for order #%1', $incrementId);
    }

    /**
     * Setter for $_entity and add some extra data to history
     *
     * @param Varien_Object $entity
     * @return Enterprise_Reward_Model_Action_Abstract
     */
    public function setEntity($entity)
    {
        parent::setEntity($entity);
        $this->getHistory()->addAdditionalData(array(
            'increment_id' => $this->getEntity()->getIncrementId()
        ));
        return $this;
    }

    /**
     * Quote setter
     *
     * @param Mage_Sales_Model_Quote $quote
     * @return Enterprise_Reward_Model_Action_OrderExtra
     */
    public function setQuote(Mage_Sales_Model_Quote $quote)
    {
        $this->_quote = $quote;
        return $this;
    }

    /**
     * Retrieve points delta for action
     *
     * @param int $websiteId
     * @return int
     */
    public function getPoints($websiteId)
    {
        if (!Mage::helper('Enterprise_Reward_Helper_Data')->isOrderAllowed($this->getReward()->getWebsiteId())) {
            return 0;
        }
        if ($this->_quote) {
            $quote = $this->_quote;
            // known issue: no support for multishipping quote
            $address = $quote->getIsVirtual() ? $quote->getBillingAddress() : $quote->getShippingAddress();
            // use only money customer spend - shipping & tax
            $monetaryAmount = $quote->getBaseGrandTotal()
                - $address->getBaseShippingAmount()
                - $address->getBaseTaxAmount();
            $monetaryAmount = $monetaryAmount < 0 ? 0 : $monetaryAmount;
        } else {
            $monetaryAmount = $this->getEntity()->getBaseTotalPaid()
                - $this->getEntity()->getBaseShippingAmount()
                - $this->getEntity()->getBaseTaxAmount();
        }
        $pointsDelta = $this->getReward()->getRateToPoints()->calculateToPoints((float)$monetaryAmount);
        return $pointsDelta;
    }

    /**
     * Check whether rewards can be added for action
     * Checking for the history records is intentionaly omitted
     *
     * @return bool
     *
     */
    public function canAddRewardPoints()
    {
        return parent::canAddRewardPoints()
            && Mage::helper('Enterprise_Reward_Helper_Data')->isOrderAllowed($this->getReward()->getWebsiteId());
    }
}
