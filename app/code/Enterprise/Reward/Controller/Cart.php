<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Reward
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Enterprise_Reward_Controller_Cart extends Mage_Core_Controller_Front_Action
{
    /**
     * Only logged in users can use this functionality,
     * this function checks if user is logged in before all other actions
     *
     */
    public function preDispatch()
    {
        parent::preDispatch();

        if (!Mage::getSingleton('Mage_Customer_Model_Session')->authenticate($this)) {
            $this->setFlag('', self::FLAG_NO_DISPATCH, true);
        }
    }

    /**
     * Remove Reward Points payment from current quote
     *
     */
    public function removeAction()
    {
        if (!Mage::helper('Enterprise_Reward_Helper_Data')->isEnabledOnFront()
            || !Mage::helper('Enterprise_Reward_Helper_Data')->getHasRates()) {
            return $this->_redirect('customer/account/');
        }

        $quote = Mage::getSingleton('Mage_Checkout_Model_Session')->getQuote();

        if ($quote->getUseRewardPoints()) {
            $quote->setUseRewardPoints(false)->collectTotals()->save();
            Mage::getSingleton('Mage_Checkout_Model_Session')->addSuccess(
                $this->__('You removed the reward points from this order.')
            );
        } else {
            Mage::getSingleton('Mage_Checkout_Model_Session')->addError(
                $this->__('Reward points will not be used in this order.')
            );
        }

        $this->_redirect('checkout/cart');
    }
}
