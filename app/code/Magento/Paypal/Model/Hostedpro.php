<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Paypal
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Website Payments Pro Hosted Solution payment gateway model
 *
 * @category    Magento
 * @package     Magento_Paypal
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Magento_Paypal_Model_Hostedpro extends Magento_Paypal_Model_Direct
{
    /**
     * Button code
     *
     * @var string
     */
    const BM_BUTTON_CODE    = 'TOKEN';

    /**
     * Button type
     *
     * @var string
     */
    const BM_BUTTON_TYPE    = 'PAYMENT';

    /**
     * Paypal API method name for button creation
     *
     * @var string
     */
    const BM_BUTTON_METHOD  = 'BMCreateButton';

    /**
     * Payment method code
     */
    protected $_code = Magento_Paypal_Model_Config::METHOD_HOSTEDPRO;

    protected $_formBlockType = 'Magento_Paypal_Block_Hosted_Pro_Form';
    protected $_infoBlockType = 'Magento_Paypal_Block_Hosted_Pro_Info';

    /**
     * Availability options
     */
    protected $_canUseInternal          = false;
    protected $_canUseForMultishipping  = false;
    protected $_canSaveCc               = false;
    protected $_isInitializeNeeded      = true;

    /**
     * Return available CC types for gateway based on merchant country.
     * We do not have to check the availability of card types.
     *
     * @return bool
     */
    public function getAllowedCcTypes()
    {
        return true;
    }

    /**
     * Return merchant country code from config,
     * use default country if it not specified in General settings
     *
     * @return string
     */
    public function getMerchantCountry()
    {
        return $this->_pro->getConfig()->getMerchantCountry();
    }

    /**
     * Do not validate payment form using server methods
     *
     * @return  bool
     */
    public function validate()
    {
        return true;
    }

    /**
     * Instantiate state and set it to state object
     *
     * @param string $paymentAction
     * @param Magento_Object $stateObject
     */
    public function initialize($paymentAction, $stateObject)
    {
        switch ($paymentAction) {
            case Magento_Paypal_Model_Config::PAYMENT_ACTION_AUTH:
            case Magento_Paypal_Model_Config::PAYMENT_ACTION_SALE:
                $payment = $this->getInfoInstance();
                $order = $payment->getOrder();
                $order->setCanSendNewEmailFlag(false);
                $payment->setAmountAuthorized($order->getTotalDue());
                $payment->setBaseAmountAuthorized($order->getBaseTotalDue());

                $this->_setPaymentFormUrl($payment);

                $stateObject->setState(Mage_Sales_Model_Order::STATE_PENDING_PAYMENT);
                $stateObject->setStatus('pending_payment');
                $stateObject->setIsNotified(false);
                break;
            default:
                break;
        }
    }

    /**
     * Sends API request to PayPal to get form URL, then sets this URL to $payment object.
     *
     * @param Magento_Payment_Model_Info $payment
     */
    protected function _setPaymentFormUrl(Magento_Payment_Model_Info $payment)
    {
        $request = $this->_buildFormUrlRequest($payment);
        $response = $this->_sendFormUrlRequest($request);
        if ($response) {
            $payment->setAdditionalInformation('secure_form_url', $response);
        } else {
            Mage::throwException('Cannot get secure form URL from PayPal');
        }
    }

    /**
     * Returns request object with needed data for API request to PayPal to get form URL.
     *
     * @param Magento_Payment_Model_Info $payment
     * @return Magento_Paypal_Model_Hostedpro_Request
     */
    protected function _buildFormUrlRequest(Magento_Payment_Model_Info $payment)
    {
        $request = $this->_buildBasicRequest()
            ->setOrder($payment->getOrder())
            ->setPaymentMethod($this);

        return $request;
    }

    /**
     * Returns form URL from request to PayPal.
     *
     * @param Magento_Paypal_Model_Hostedpro_Request $request
     * @return string | false
     */
    protected function _sendFormUrlRequest(Magento_Paypal_Model_Hostedpro_Request $request)
    {
        $api = $this->_pro->getApi();
        $response = $api->call(self::BM_BUTTON_METHOD, $request->getRequestData());

        if (!isset($response['EMAILLINK'])) {
            return false;
        }
        return $response['EMAILLINK'];
    }

    /**
     * Return request object with basic information
     *
     * @return Magento_Paypal_Model_Hostedpro_Request
     */
    protected function _buildBasicRequest()
    {
        $request = Mage::getModel('Magento_Paypal_Model_Hostedpro_Request');
        $request->setData(array(
            'METHOD'     => self::BM_BUTTON_METHOD,
            'BUTTONCODE' => self::BM_BUTTON_CODE,
            'BUTTONTYPE' => self::BM_BUTTON_TYPE
        ));
        return $request;
    }

    /**
     * Get return URL
     *
     * @param int $storeId
     * @return string
     */
    public function getReturnUrl($storeId = null)
    {
        return $this->_getUrl('paypal/hostedpro/return', $storeId);
    }

    /**
     * Get notify (IPN) URL
     *
     * @param int $storeId
     * @return string
     */
    public function getNotifyUrl($storeId = null)
    {
        return $this->_getUrl('paypal/ipn', $storeId, false);
    }

    /**
     * Get cancel URL
     *
     * @param int $storeId
     * @return string
     */
    public function getCancelUrl($storeId = null)
    {
        return $this->_getUrl('paypal/hostedpro/cancel', $storeId);
    }

    /**
     * Build URL for store
     *
     * @param string $path
     * @param int $storeId
     * @param bool $secure
     * @return string
     */
    protected function _getUrl($path, $storeId, $secure = null)
    {
        $store = Mage::app()->getStore($storeId);
        return Mage::getUrl($path, array(
            "_store"   => $store,
            "_secure"  => is_null($secure) ? $store->isCurrentlySecure() : $secure
        ));
    }
}
