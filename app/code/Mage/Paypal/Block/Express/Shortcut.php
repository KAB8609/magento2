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
 * Paypal expess checkout shortcut link
 */
class Mage_Paypal_Block_Express_Shortcut extends Magento_Core_Block_Template
{
    /**
     * Position of "OR" label against shortcut
     */
    const POSITION_BEFORE = 'before';
    const POSITION_AFTER = 'after';

    /**
     * Whether the block should be eventually rendered
     *
     * @var bool
     */
    protected $_shouldRender = true;

    /**
     * Payment method code
     *
     * @var string
     */
    protected $_paymentMethodCode = Mage_Paypal_Model_Config::METHOD_WPP_EXPRESS;

    /**
     * Start express action
     *
     * @var string
     */
    protected $_startAction = 'paypal/express/start';

    /**
     * Express checkout model factory name
     *
     * @var string
     */
    protected $_checkoutType = 'Mage_Paypal_Model_Express_Checkout';

    protected function _beforeToHtml()
    {
        $result = parent::_beforeToHtml();
        $params = array($this->_paymentMethodCode);
        $config = Mage::getModel('Mage_Paypal_Model_Config', array('params' => $params));
        $isInCatalog = $this->getIsInCatalogProduct();
        $quote = ($isInCatalog || '' == $this->getIsQuoteAllowed())
            ? null : Mage::getSingleton('Magento_Checkout_Model_Session')->getQuote();

        // check visibility on cart or product page
        $context = $isInCatalog ? 'visible_on_product' : 'visible_on_cart';
        if (!$config->$context) {
            $this->_shouldRender = false;
            return $result;
        }

        if ($isInCatalog) {
            // Show PayPal shortcut on a product view page only if product has nonzero price
            /** @var $currentProduct Magento_Catalog_Model_Product */
            $currentProduct = Mage::registry('current_product');
            if (!is_null($currentProduct)) {
                $productPrice = (float)$currentProduct->getFinalPrice();
                if (empty($productPrice) && !$currentProduct->isGrouped()) {
                    $this->_shouldRender = false;
                    return $result;
                }
            }
        }
        // validate minimum quote amount and validate quote for zero grandtotal
        if (null !== $quote && (!$quote->validateMinimumAmount()
            || (!$quote->getGrandTotal() && !$quote->hasNominalItems()))) {
            $this->_shouldRender = false;
            return $result;
        }

        // check payment method availability
        $methodInstance = Mage::helper('Magento_Payment_Helper_Data')->getMethodInstance($this->_paymentMethodCode);
        if (!$methodInstance || !$methodInstance->isAvailable($quote)) {
            $this->_shouldRender = false;
            return $result;
        }

        // set misc data
        $this->setShortcutHtmlId($this->helper('Magento_Core_Helper_Data')->uniqHash('ec_shortcut_'))
            ->setCheckoutUrl($this->getUrl($this->_startAction))
        ;

        // use static image if in catalog
        if ($isInCatalog || null === $quote) {
            $this->setImageUrl($config->getExpressCheckoutShortcutImageUrl(Mage::app()->getLocale()->getLocaleCode()));
        } else {
            $parameters = array(
                'params' => array(
                    'quote' => $quote,
                    'config' => $config,
                ),
            );
            $checkoutModel = Mage::getModel($this->_checkoutType, $parameters);
            $this->setImageUrl($checkoutModel->getCheckoutShortcutImageUrl());
        }

        // ask whether to create a billing agreement
        $customerId = Mage::getSingleton('Magento_Customer_Model_Session')->getCustomerId(); // potential issue for caching
        if (Mage::helper('Mage_Paypal_Helper_Data')->shouldAskToCreateBillingAgreement($config, $customerId)) {
            $this->setConfirmationUrl($this->getUrl($this->_startAction,
                array(Mage_Paypal_Model_Express_Checkout::PAYMENT_INFO_TRANSPORT_BILLING_AGREEMENT => 1)
            ));
            $this->setConfirmationMessage(Mage::helper('Mage_Paypal_Helper_Data')->__('Would you like to sign a billing agreement to streamline further purchases with PayPal?'));
        }

        return $result;
    }

    /**
     * Render the block if needed
     *
     * @return string
     */
    protected function _toHtml()
    {
        if (!$this->_shouldRender) {
            return '';
        }
        return parent::_toHtml();
    }

    /**
     * Check is "OR" label position before shortcut
     *
     * @return bool
     */
    public function isOrPositionBefore()
    {
        return ($this->getIsInCatalogProduct() && !$this->getShowOrPosition())
            || ($this->getShowOrPosition() && $this->getShowOrPosition() == self::POSITION_BEFORE);

    }

    /**
     * Check is "OR" label position after shortcut
     *
     * @return bool
     */
    public function isOrPositionAfter()
    {
        return (!$this->getIsInCatalogProduct() && !$this->getShowOrPosition())
            || ($this->getShowOrPosition() && $this->getShowOrPosition() == self::POSITION_AFTER);
    }
}
