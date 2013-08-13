<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Paypal
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * HSS iframe block
 *
 * @category   Mage
 * @package    Mage_Paypal
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Paypal_Block_Iframe extends Magento_Payment_Block_Form
{
    /**
     * Whether the block should be eventually rendered
     *
     * @var bool
     */
    protected $_shouldRender = false;

    /**
     * Order object
     *
     * @var Mage_Sales_Model_Order
     */
    protected $_order;

    /**
     * Payment method code
     *
     * @var string
     */
    protected $_paymentMethodCode;

    /**
     * Current iframe block instance
     *
     * @var Magento_Payment_Block_Form
     */
    protected $_block;


    protected $_template = 'hss/js.phtml';

    /**
     * Internal constructor
     *
     */
    protected function _construct()
    {
        parent::_construct();
        $paymentCode = $this->_getCheckout()
            ->getQuote()
            ->getPayment()
            ->getMethod();
        if (in_array($paymentCode, $this->helper('Mage_Paypal_Helper_Hss')->getHssMethods())) {
            $this->_paymentMethodCode = $paymentCode;
            $templatePath = str_replace('_', '', $paymentCode);
            $templateFile = "{$templatePath}/iframe.phtml";
            if (file_exists($this->_viewFileSystem->getFilename($templateFile))) {
                $this->setTemplate($templateFile);
            } else {
                $this->setTemplate('hss/iframe.phtml');
            }
        }
    }

    /**
     * Get current block instance
     *
     * @return Mage_Paypal_Block_Iframe
     */
    protected function _getBlock()
    {
        if (!$this->_block) {
            $this->_block = $this->getLayout()
                ->createBlock('Mage_Paypal_Block_'
                    . str_replace(' ', '_', ucwords(str_replace('_', ' ', $this->_paymentMethodCode)))
                    . '_Iframe');
            if (!$this->_block instanceof Mage_Paypal_Block_Iframe) {
                Mage::throwException('Invalid block type');
            }
        }

        return $this->_block;
    }

    /**
     * Get order object
     *
     * @return Mage_Sales_Model_Order
     */
    protected function _getOrder()
    {
        if (!$this->_order) {
            $incrementId = $this->_getCheckout()->getLastRealOrderId();
            $this->_order = Mage::getModel('Mage_Sales_Model_Order')
                ->loadByIncrementId($incrementId);
        }
        return $this->_order;
    }

    /**
     * Get frontend checkout session object
     *
     * @return Magento_Checkout_Model_Session
     */
    protected function _getCheckout()
    {
        return Mage::getSingleton('Magento_Checkout_Model_Session');
    }

    /**
     * Before rendering html, check if is block rendering needed
     *
     * @return Magento_Core_Block_Abstract
     */
    protected function _beforeToHtml()
    {
        if ($this->_getOrder()->getId() &&
            $this->_getOrder()->getQuoteId() == $this->_getCheckout()->getLastQuoteId() &&
            $this->_paymentMethodCode) {
            $this->_shouldRender = true;
        }

        if ($this->getGotoSection() || $this->getGotoSuccessPage()) {
            $this->_shouldRender = true;
        }

        return parent::_beforeToHtml();
    }

    /**
     * Render the block if needed
     *
     * @return string
     */
    protected function _toHtml()
    {
        if ($this->_isAfterPaymentSave()) {
            $this->setTemplate('hss/js.phtml');
            return parent::_toHtml();
        }
        if (!$this->_shouldRender) {
            return '';
        }
        return parent::_toHtml();
    }

    /**
     * Check whether block is rendering after save payment
     *
     * @return bool
     */
    protected function _isAfterPaymentSave()
    {
        $quote = $this->_getCheckout()->getQuote();
        if ($quote->getPayment()->getMethod() == $this->_paymentMethodCode &&
            $quote->getIsActive() &&
            $this->getTemplate() &&
            $this->getRequest()->getActionName() == 'savePayment') {
            return true;
        }

        return false;
    }

    /**
     * Get iframe action URL
     *
     * @return string
     */
    public function getFrameActionUrl()
    {
        return $this->_getBlock()->getFrameActionUrl();
    }

    /**
     * Get secure token
     *
     * @return string
     */
    public function getSecureToken()
    {
        return $this->_getBlock()->getSecureToken();
    }

    /**
     * Get secure token ID
     *
     * @return string
     */
    public function getSecureTokenId()
    {
        return $this->_getBlock()->getSecureTokenId();
    }

    /**
     * Get payflow transaction URL
     *
     * @return string
     */
    public function getTransactionUrl()
    {
        return $this->_getBlock()->getTransactionUrl();
    }

    /**
     * Check sandbox mode
     *
     * @return bool
     */
    public function isTestMode()
    {
        return $this->_getBlock()->isTestMode();
    }
}
