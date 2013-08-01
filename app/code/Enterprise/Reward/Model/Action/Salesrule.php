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
 * Reward action for updating balance by salesrule
 *
 * @category    Enterprise
 * @package     Enterprise_Reward
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Reward_Model_Action_Salesrule extends Enterprise_Reward_Model_Action_Abstract
{
     /**
     * Quote instance, required for estimating checkout reward (rule defined static value)
     *
     * @var Mage_Sales_Model_Quote
     */
    protected $_quote = null;

     /**
     * Retrieve points delta for action
     *
     * @param int $websiteId
     * @return int
     */
    public function getPoints($websiteId) {
        $pointsDelta = 0;
        if ($this->_quote) {
            // known issue: no support for multishipping quote // copied  comment, not checked
            if ($this->_quote->getAppliedRuleIds()) { 
                $ruleIds = explode(',', $this->_quote->getAppliedRuleIds());
                $ruleIds = array_unique($ruleIds);
                $data = Mage::getResourceModel('Enterprise_Reward_Model_Resource_Reward')->getRewardSalesrule($ruleIds);
                foreach ($data as $rule) {
                    $pointsDelta += (int)$rule['points_delta'];
                }
            }
        }
        return $pointsDelta;
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
     * Check whether rewards can be added for action
     *
     * @return bool
     */
    public function canAddRewardPoints()
    {
        return true;
    }

    /**
     * Return action message for history log
     *
     * @param array $args Additional history data
     * @return string
     */
    public function getHistoryMessage($args = array())
    {
        $incrementId = isset($args['increment_id']) ? $args['increment_id'] : '';
        return Mage::helper('Enterprise_Reward_Helper_Data')->__('Earned promotion extra points from order #%1', $incrementId);
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
}
