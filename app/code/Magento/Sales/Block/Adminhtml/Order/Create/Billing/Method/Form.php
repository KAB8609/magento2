<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml sales order create payment method form block
 *
 * @category   Magento
 * @package    Magento_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Sales\Block\Adminhtml\Order\Create\Billing\Method;

class Form extends \Magento\Payment\Block\Form\Container
{
    /**
     * @var \Magento\Adminhtml\Model\Session\Quote
     */
    protected $_sessionQuote;

    /**
     * @param \Magento\View\Element\Template\Context $context
     * @param \Magento\Adminhtml\Model\Session\Quote $sessionQuote
     * @param array $data
     */
    public function __construct(
        \Magento\View\Element\Template\Context $context,
        \Magento\Adminhtml\Model\Session\Quote $sessionQuote,
        array $data = array()
    ) {
        $this->_sessionQuote = $sessionQuote;
        parent::__construct($context, $data);
    }

    /**
     * Check payment method model
     *
     * @param \Magento\Payment\Model\Method\AbstractMethod|null $method
     * @return bool
     */
    protected function _canUseMethod($method)
    {
        return $method && $method->canUseInternal() && parent::_canUseMethod($method);
    }

    /**
     * Check existing of payment methods
     *
     * @return bool
     */
    public function hasMethods()
    {
        $methods = $this->getMethods();
        if (is_array($methods) && count($methods)) {
            return true;
        }
        return false;
    }

    /**
     * Get current payment method code or the only available, if there is only one method
     *
     * @return string|false
     */
    public function getSelectedMethodCode()
    {
        // One available method. Return this method as selected, because no other variant is possible.
        $methods = $this->getMethods();
        if (count($methods) == 1) {
            foreach ($methods as $method) {
                return $method->getCode();
            }
        }

        // Several methods. If user has selected some method - then return it.
        $currentMethodCode = $this->getQuote()->getPayment()->getMethod();
        if ($currentMethodCode) {
            return $currentMethodCode;
        }

        // Several methods, but no preference for one of them.
        return false;
    }

    /**
     * Enter description here...
     *
     * @return \Magento\Sales\Model\Quote
     */
    public function getQuote()
    {
        return $this->_sessionQuote->getQuote();
    }

    /*
    * Whether switch/solo card type available
    */
    public function hasSsCardType()
    {
        $availableTypes = explode(',', $this->getQuote()->getPayment()->getMethod()->getConfigData('cctypes'));
        $ssPresenations = array_intersect(array('SS', 'SM', 'SO'), $availableTypes);
        if ($availableTypes && count($ssPresenations) > 0) {
            return true;
        }
        return false;
    }

}
