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
 * Payflow Pro payment gateway model
 *
 * @category    Magento
 * @package     Magento_Paypal
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Magento_Paypal_Model_Payflowpro extends  Magento_Payment_Model_Method_Cc
{
    /**
     * Transaction action codes
     */
    const TRXTYPE_AUTH_ONLY         = 'A';
    const TRXTYPE_SALE              = 'S';
    const TRXTYPE_CREDIT            = 'C';
    const TRXTYPE_DELAYED_CAPTURE   = 'D';
    const TRXTYPE_DELAYED_VOID      = 'V';
    const TRXTYPE_DELAYED_VOICE     = 'F';
    const TRXTYPE_DELAYED_INQUIRY   = 'I';

    /**
     * Tender type codes
     */
    const TENDER_CC = 'C';

    /**
     * Gateway request URLs
     */
    const TRANSACTION_URL           = 'https://payflowpro.paypal.com/transaction';
    const TRANSACTION_URL_TEST_MODE = 'https://pilot-payflowpro.paypal.com/transaction';

    /**
     * Response codes
     */
    const RESPONSE_CODE_APPROVED                = 0;
    const RESPONSE_CODE_INVALID_AMOUNT          = 4;
    const RESPONSE_CODE_FRAUDSERVICE_FILTER     = 126;
    const RESPONSE_CODE_DECLINED                = 12;
    const RESPONSE_CODE_DECLINED_BY_FILTER      = 125;
    const RESPONSE_CODE_DECLINED_BY_MERCHANT    = 128;
    const RESPONSE_CODE_CAPTURE_ERROR           = 111;
    const RESPONSE_CODE_VOID_ERROR              = 108;

    /**
     * Payment method code
     */
    protected $_code = Magento_Paypal_Model_Config::METHOD_PAYFLOWPRO;

    /**
     * Availability options
     */
    protected $_isGateway               = true;
    protected $_canAuthorize            = true;
    protected $_canCapture              = true;
    protected $_canCapturePartial       = false;
    protected $_canRefund               = true;
    protected $_canVoid                 = true;
    protected $_canUseInternal          = true;
    protected $_canUseCheckout          = true;
    protected $_canUseForMultishipping  = true;
    protected $_canSaveCc = false;
    protected $_isProxy = false;
    protected $_canFetchTransactionInfo = true;

    /**
     * Gateway request timeout
     */
    protected $_clientTimeout = 45;

    /**
     * Fields that should be replaced in debug with '***'
     *
     * @var array
     */
    protected $_debugReplacePrivateDataKeys = array('user', 'pwd', 'acct', 'expdate', 'cvv2');

    /**
     * Centinel cardinal fields map
     *
     * @var string
     */
    protected $_centinelFieldMap = array(
        'centinel_mpivendor'    => 'MPIVENDOR3DS',
        'centinel_authstatus'   => 'AUTHSTATUS3DS',
        'centinel_cavv'         => 'CAVV',
        'centinel_eci'          => 'ECI',
        'centinel_xid'          => 'XID',
    );

    /**
     * Check whether payment method can be used
     *
     * @param Magento_Sales_Model_Quote
     * @return bool
     */
    public function isAvailable($quote = null)
    {
        $storeId = Mage::app()->getStore($this->getStore())->getId();
        $config = Mage::getModel('Magento_Paypal_Model_Config')->setStoreId($storeId);
        if (parent::isAvailable($quote) && $config->isMethodAvailable($this->getCode())) {
            return true;
        }
        return false;
    }

    /**
     * Payment action getter compatible with payment model
     *
     * @see Magento_Sales_Model_Payment::place()
     * @return string
     */
    public function getConfigPaymentAction()
    {
        switch ($this->getConfigData('payment_action')) {
            case Magento_Paypal_Model_Config::PAYMENT_ACTION_AUTH:
                return Magento_Payment_Model_Method_Abstract::ACTION_AUTHORIZE;
            case Magento_Paypal_Model_Config::PAYMENT_ACTION_SALE:
                return Magento_Payment_Model_Method_Abstract::ACTION_AUTHORIZE_CAPTURE;
        }
    }

    /**
     * Authorize payment
     *
     * @param Magento_Sales_Model_Order_Payment $payment
     * @return Magento_Paypal_Model_Payflowpro
     */
    public function authorize(Magento_Object $payment, $amount)
    {
        $request = $this->_buildPlaceRequest($payment, $amount);
        $request->setTrxtype(self::TRXTYPE_AUTH_ONLY);
        $this->_setReferenceTransaction($payment, $request);
        $response = $this->_postRequest($request);
        $this->_processErrors($response);

        switch ($response->getResultCode()){
            case self::RESPONSE_CODE_APPROVED:
                $payment->setTransactionId($response->getPnref())->setIsTransactionClosed(0);
                break;
            case self::RESPONSE_CODE_FRAUDSERVICE_FILTER:
                $payment->setTransactionId($response->getPnref())->setIsTransactionClosed(0);
                $payment->setIsTransactionPending(true);
                $payment->setIsFraudDetected(true);
                break;
        }
        return $this;
    }

    /**
     * Capture payment
     *
     * @param Magento_Sales_Model_Order_Payment $payment
     * @return Magento_Paypal_Model_Payflowpro
     */
    public function capture(Magento_Object $payment, $amount)
    {
        if ($payment->getReferenceTransactionId()) {
            $request = $this->_buildPlaceRequest($payment, $amount);
            $request->setTrxtype(self::TRXTYPE_SALE);
            $request->setOrigid($payment->getReferenceTransactionId());
        } elseif ($payment->getParentTransactionId()) {
            $request = $this->_buildBasicRequest($payment);
            $request->setTrxtype(self::TRXTYPE_DELAYED_CAPTURE);
            $request->setOrigid($payment->getParentTransactionId());
        } else {
            $request = $this->_buildPlaceRequest($payment, $amount);
            $request->setTrxtype(self::TRXTYPE_SALE);
        }

        $response = $this->_postRequest($request);
        $this->_processErrors($response);

        switch ($response->getResultCode()){
            case self::RESPONSE_CODE_APPROVED:
                $payment->setTransactionId($response->getPnref())->setIsTransactionClosed(0);
                break;
            case self::RESPONSE_CODE_FRAUDSERVICE_FILTER:
                $payment->setTransactionId($response->getPnref())->setIsTransactionClosed(0);
                $payment->setIsTransactionPending(true);
                $payment->setIsFraudDetected(true);
                break;
        }
        return $this;
    }

    /**
     * Void payment
     *
     * @param Magento_Sales_Model_Order_Payment $payment
     * @return Magento_Paypal_Model_Payflowpro
     */
    public function void(Magento_Object $payment)
    {
        $request = $this->_buildBasicRequest($payment);
        $request->setTrxtype(self::TRXTYPE_DELAYED_VOID);
        $request->setOrigid($payment->getParentTransactionId());
        $response = $this->_postRequest($request);
        $this->_processErrors($response);

        if ($response->getResultCode() == self::RESPONSE_CODE_APPROVED){
            $payment->setTransactionId($response->getPnref())
                ->setIsTransactionClosed(1)
                ->setShouldCloseParentTransaction(1);
        }

        return $this;
    }

    /**
     * Attempt to void the authorization on cancelling
     *
     * @param Magento_Object $payment
     * @return Magento_Paypal_Model_Payflowpro
     */
    public function cancel(Magento_Object $payment)
    {
        return $this->void($payment);
    }

    /**
     * Refund capture
     *
     * @param Magento_Sales_Model_Order_Payment $payment
     * @return Magento_Paypal_Model_Payflowpro
     */
    public function refund(Magento_Object $payment, $amount)
    {
        $request = $this->_buildBasicRequest($payment);
        $request->setTrxtype(self::TRXTYPE_CREDIT);
        $request->setOrigid($payment->getParentTransactionId());
        $request->setAmt(round($amount,2));
        $response = $this->_postRequest($request);
        $this->_processErrors($response);

        if ($response->getResultCode() == self::RESPONSE_CODE_APPROVED){
            $payment->setTransactionId($response->getPnref())
                ->setIsTransactionClosed(1);
        }
        return $this;
    }

    /**
     * Fetch transaction details info
     *
     * @param Magento_Payment_Model_Info $payment
     * @param string $transactionId
     * @return array
     */
    public function fetchTransactionInfo(Magento_Payment_Model_Info $payment, $transactionId)
    {
        $request = $this->_buildBasicRequest($payment);
        $request->setTrxtype(self::TRXTYPE_DELAYED_INQUIRY);
        $request->setOrigid($transactionId);
        $response = $this->_postRequest($request);

        $this->_processErrors($response);

        if (!$this->_isTransactionUnderReview($response->getOrigresult())) {
            $payment->setTransactionId($response->getOrigpnref())
                ->setIsTransactionClosed(0);
            if ($response->getOrigresult() == self::RESPONSE_CODE_APPROVED) {
                $payment->setIsTransactionApproved(true);
            } else if ($response->getOrigresult() == self::RESPONSE_CODE_DECLINED_BY_MERCHANT) {
                $payment->setIsTransactionDenied(true);
            }
        }

        $rawData = $response->getData();
        return ($rawData) ? $rawData : array();
    }

    /**
     * Check whether the transaction is in payment review status
     *
     * @param string $statusCode
     * @return bool
     */
    protected static function _isTransactionUnderReview($status)
    {
        if (in_array($status, array(self::RESPONSE_CODE_APPROVED, self::RESPONSE_CODE_DECLINED_BY_MERCHANT))) {
            return false;
        }
        return true;
    }

    /**
     * Getter for URL to perform Payflow requests, based on test mode by default
     *
     * @param bool $testMode Ability to specify test mode using
     * @return string
     */
    protected function _getTransactionUrl($testMode = null)
    {
        $testMode = is_null($testMode) ? $this->getConfigData('sandbox_flag') : (bool)$testMode;
        if ($testMode) {
            return self::TRANSACTION_URL_TEST_MODE;
        }
        return self::TRANSACTION_URL;
    }

    /**
     * Post request to gateway and return response
     *
     * @param Magento_Object $request
     * @return Magento_Object
     */
    protected function _postRequest(Magento_Object $request)
    {
        $debugData = array('request' => $request->getData());

        $client = new Magento_HTTP_ZendClient();
        $result = new Magento_Object();

        $_config = array(
            'maxredirects' => 5,
            'timeout'    => 30,
            'verifypeer' => $this->getConfigData('verify_peer')
        );

        $_isProxy = $this->getConfigData('use_proxy', false);
        if ($_isProxy){
            $_config['proxy'] = $this->getConfigData('proxy_host')
                . ':'
                . $this->getConfigData('proxy_port');//http://proxy.shr.secureserver.net:3128',
            $_config['httpproxytunnel'] = true;
            $_config['proxytype'] = CURLPROXY_HTTP;
        }

        $client->setUri($this->_getTransactionUrl())
            ->setConfig($_config)
            ->setMethod(Zend_Http_Client::POST)
            ->setParameterPost($request->getData())
            ->setHeaders('X-VPS-VIT-CLIENT-CERTIFICATION-ID: 33baf5893fc2123d8b191d2d011b7fdc')
            ->setHeaders('X-VPS-Request-ID: ' . $request->getRequestId())
            ->setHeaders('X-VPS-CLIENT-TIMEOUT: ' . $this->_clientTimeout);

        try {
           /**
            * we are sending request to payflow pro without url encoding
            * so we set up _urlEncodeBody flag to false
            */
            $response = $client->setUrlEncodeBody(false)->request();
        }
        catch (Exception $e) {
            $result->setResponseCode(-1)
                ->setResponseReasonCode($e->getCode())
                ->setResponseReasonText($e->getMessage());

            $debugData['result'] = $result->getData();
            $this->_debug($debugData);
            throw $e;
        }



        $response = strstr($response->getBody(), 'RESULT');
        $valArray = explode('&', $response);

        foreach($valArray as $val) {
                $valArray2 = explode('=', $val);
                $result->setData(strtolower($valArray2[0]), $valArray2[1]);
        }

        $result->setResultCode($result->getResult())
                ->setRespmsg($result->getRespmsg());

        $debugData['result'] = $result->getData();
        $this->_debug($debugData);

        return $result;
    }

     /**
      * Return request object with information for 'authorization' or 'sale' action
      *
      * @param Magento_Sales_Model_Order_Payment $payment
      * @param float $amount
      * @return Magento_Object
      */
    protected function _buildPlaceRequest(Magento_Object $payment, $amount)
    {
        $request = $this->_buildBasicRequest($payment);
        $request->setAmt(round($amount,2));
        $request->setAcct($payment->getCcNumber());
        $request->setExpdate(sprintf('%02d',$payment->getCcExpMonth()) . substr($payment->getCcExpYear(),-2,2));
        $request->setCvv2($payment->getCcCid());

        if ($this->getIsCentinelValidationEnabled()){
            $params = array();
            $params = $this->getCentinelValidator()->exportCmpiData($params);
            $request = Magento_Object_Mapper::accumulateByMap($params, $request, $this->_centinelFieldMap);
        }

        $order = $payment->getOrder();
        if(!empty($order)){
            $request->setCurrency($order->getBaseCurrencyCode());

            $orderIncrementId = $order->getIncrementId();
            $request->setCustref($orderIncrementId)
                ->setComment1($orderIncrementId);

            $billing = $order->getBillingAddress();
            if (!empty($billing)) {
                $request->setFirstname($billing->getFirstname())
                    ->setLastname($billing->getLastname())
                    ->setStreet(implode(' ', $billing->getStreet()))
                    ->setCity($billing->getCity())
                    ->setState($billing->getRegionCode())
                    ->setZip($billing->getPostcode())
                    ->setCountry($billing->getCountry())
                    ->setEmail($payment->getOrder()->getCustomerEmail());
            }
            $shipping = $order->getShippingAddress();
            if (!empty($shipping)) {
                $this->_applyCountryWorkarounds($shipping);
                $request->setShiptofirstname($shipping->getFirstname())
                    ->setShiptolastname($shipping->getLastname())
                    ->setShiptostreet(implode(' ', $shipping->getStreet()))
                    ->setShiptocity($shipping->getCity())
                    ->setShiptostate($shipping->getRegionCode())
                    ->setShiptozip($shipping->getPostcode())
                    ->setShiptocountry($shipping->getCountry());
            }
        }
        return $request;
    }

     /**
      * Return request object with basic information for gateway request
      *
      * @param Magento_Sales_Model_Order_Payment $payment
      * @return Magento_Object
      */
    protected function _buildBasicRequest(Magento_Object $payment)
    {
        $request = new Magento_Object();
        $request
            ->setUser($this->getConfigData('user'))
            ->setVendor($this->getConfigData('vendor'))
            ->setPartner($this->getConfigData('partner'))
            ->setPwd($this->getConfigData('pwd'))
            ->setVerbosity($this->getConfigData('verbosity'))
            ->setTender(self::TENDER_CC)
            ->setRequestId($this->_generateRequestId());
        return $request;
    }

     /**
      * Return unique value for request
      *
      * @return string
      */
    protected function _generateRequestId()
    {
        return Mage::helper('Magento_Core_Helper_Data')->uniqHash();
    }

     /**
      * If response is failed throw exception
      *
      * @throws Magento_Core_Exception
      */
    protected function _processErrors(Magento_Object $response)
    {
        if ($response->getResultCode() == self::RESPONSE_CODE_VOID_ERROR) {
            throw new Magento_Paypal_Exception(__('You cannot void a verification transaction.'));
        } elseif ($response->getResultCode() != self::RESPONSE_CODE_APPROVED
            && $response->getResultCode() != self::RESPONSE_CODE_FRAUDSERVICE_FILTER) {
            Mage::throwException($response->getRespmsg());
        }
    }

    /**
     * Adopt specified address object to be compatible with Paypal
     * Puerto Rico should be as state of USA and not as a country
     *
     * @param Magento_Object $address
     */
    protected function _applyCountryWorkarounds(Magento_Object $address)
    {
        if ($address->getCountry() == 'PR') {
            $address->setCountry('US');
            $address->setRegionCode('PR');
        }
    }

    /**
     * Set reference transaction data into request
     *
     * @param Magento_Object $payment
     * @param Magento_Object $request
     * @return Magento_Paypal_Model_Payflowpro
     */
    protected function _setReferenceTransaction(Magento_Object $payment, $request)
    {
        return $this;
    }
}