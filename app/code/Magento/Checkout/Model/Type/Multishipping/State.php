<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Checkout
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Multishipping checkout state model
 *
 * @category   Magento
 * @package    Magento_Checkout
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Checkout_Model_Type_Multishipping_State extends \Magento\Object
{
    const STEP_SELECT_ADDRESSES = 'multishipping_addresses';
    const STEP_SHIPPING         = 'multishipping_shipping';
    const STEP_BILLING          = 'multishipping_billing';
    const STEP_OVERVIEW         = 'multishipping_overview';
    const STEP_SUCCESS          = 'multishipping_success';

    /**
     * Allow steps array
     *
     * @var array
     */
    protected $_steps;

    /**
     * Checkout model
     *
     * @var Magento_Checkout_Model_Type_Multishipping
     */
    protected $_checkout;

    /**
     * Init model, steps
     *
     */
    public function __construct()
    {
        parent::__construct();
        $this->_steps = array(
            self::STEP_SELECT_ADDRESSES => new \Magento\Object(array(
                'label' => __('Select Addresses')
            )),
            self::STEP_SHIPPING => new \Magento\Object(array(
                'label' => __('Shipping Information')
            )),
            self::STEP_BILLING => new \Magento\Object(array(
                'label' => __('Billing Information')
            )),
            self::STEP_OVERVIEW => new \Magento\Object(array(
                'label' => __('Place Order')
            )),
            self::STEP_SUCCESS => new \Magento\Object(array(
                'label' => __('Order Success')
            )),
        );

        foreach ($this->_steps as $step) {
            $step->setIsComplete(false);
        }

        $this->_checkout = Mage::getSingleton('Magento_Checkout_Model_Type_Multishipping');
        $this->_steps[$this->getActiveStep()]->setIsActive(true);
    }

    /**
     * Retrieve checkout model
     *
     * @return Magento_Checkout_Model_Type_Multishipping
     */
    public function getCheckout()
    {
        return $this->_checkout;
    }

    /**
     * Retrieve available checkout steps
     *
     * @return array
     */
    public function getSteps()
    {
        return $this->_steps;
    }

    /**
     * Retrieve active step code
     *
     * @return string
     */
    public function getActiveStep()
    {
        $step = $this->getCheckoutSession()->getCheckoutState();
        if (isset($this->_steps[$step])) {
            return $step;
        }
        return self::STEP_SELECT_ADDRESSES;
    }

    public function setActiveStep($step)
    {
        if (isset($this->_steps[$step])) {
            $this->getCheckoutSession()->setCheckoutState($step);
        }
        else {
            $this->getCheckoutSession()->setCheckoutState(self::STEP_SELECT_ADDRESSES);
        }

        // Fix active step changing
        if(!$this->_steps[$step]->getIsActive()) {
            foreach($this->getSteps() as $stepObject) {
                $stepObject->unsIsActive();
            }
            $this->_steps[$step]->setIsActive(true);
        }
        return $this;
    }

    /**
     * Mark step as completed
     *
     * @param string $step
     * @return Magento_Checkout_Model_Type_Multishipping_State
     */
    public function setCompleteStep($step)
    {
        if (isset($this->_steps[$step])) {
            $this->getCheckoutSession()->setStepData($step, 'is_complete', true);
        }
        return $this;
    }

    /**
     * Retrieve step complete status
     *
     * @param string $step
     * @return bool
     */
    public function getCompleteStep($step)
    {
        if (isset($this->_steps[$step])) {
            return $this->getCheckoutSession()->getStepData($step, 'is_complete');
        }
        return false;
    }

    /**
     * Unset complete status from step
     *
     * @param string $step
     * @return Magento_Checkout_Model_Type_Multishipping_State
     */
    public function unsCompleteStep($step)
    {
        if (isset($this->_steps[$step])) {
            $this->getCheckoutSession()->setStepData($step, 'is_complete', false);
        }
        return $this;
    }

    public function canSelectAddresses()
    {

    }

    public function canInputShipping()
    {

    }

    public function canSeeOverview()
    {

    }

    public function canSuccess()
    {

    }

    /**
     * Retrieve checkout session
     *
     * @return Magento_Checkout_Model_Session
     */
    public function getCheckoutSession()
    {
        return Mage::getSingleton('Magento_Checkout_Model_Session');
    }
}
