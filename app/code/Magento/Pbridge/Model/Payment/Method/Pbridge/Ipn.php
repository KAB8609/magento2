<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Pbridge
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * PayPal Instant Payment Notification processor model
 */
class Magento_Pbridge_Model_Payment_Method_Pbridge_Ipn
{
    const STATUS_CREATED      = 'Created';
    const STATUS_COMPLETED    = 'Completed';
    const STATUS_DENIED       = 'Denied';
    const STATUS_FAILED       = 'Failed';
    const STATUS_REVERSED     = 'Reversed';
    const STATUS_REFUNDED     = 'Refunded';
    const STATUS_CANCELED_REV = 'Canceled_Reversal';
    const STATUS_PENDING      = 'Pending';
    const STATUS_PROCESSED    = 'Processed';
    const STATUS_EXPIRED      = 'Expired';
    const STATUS_VOIDED       = 'Voided';

    const AUTH_STATUS_IN_PROGRESS = 'In_Progress';
    const AUTH_STATUS_COMPLETED   = 'Completed';

    /*
     * @param Magento_Sales_Model_Order
     */
    protected $_order = null;

    /**
     *
     * @var Magento_Paypal_Model_Config
     */
    protected $_config = null;

    /**
     * IPN request data
     * @var array
     */
    protected $_ipnFormData = array();

    /**
     * Fields that should be replaced in debug with '***'
     *
     * @var array
     */
    protected $_debugReplacePrivateDataKeys = array();

    /**
     * Config model setter
     * @param Magento_Paypal_Model_Config $config
     * @return Magento_Paypal_Model_Ipn
     */
    public function setConfig(Magento_Paypal_Model_Config $config)
    {
        $this->_config = $config;
        return $this;
    }

    /**
     * IPN request data setter
     * @param array $data
     * @return Magento_Paypal_Model_Ipn
     */
    public function setIpnFormData(array $data)
    {
        $this->_ipnFormData = $data;
        return $this;
    }

    /**
     * IPN request data getter
     * @param string $key
     * @return array|string
     */
    public function getIpnFormData($key = null)
    {
        if (null === $key) {
            return $this->_ipnFormData;
        }
        return isset($this->_ipnFormData[$key]) ? $this->_ipnFormData[$key] : null;
    }

    /**
     * Get ipn data, send verification to PayPal, run corresponding handler
     */
    public function processIpnRequest()
    {
        if (!$this->_ipnFormData) {
            return;
        }

        $sReq = '';

        foreach ($this->_ipnFormData as $k => $v) {
            $sReq .= '&'.$k.'='.urlencode(stripslashes($v));
        }
        // append ipn command
        $sReq .= "&cmd=_notify-validate";
        $sReq = substr($sReq, 1);

        $helper = Mage::helper('Magento_Pbridge_Helper_Data');
        $url = rtrim($helper->getBridgeBaseUrl(), '/') . '/ipn.php?action=PaypalIpn';

        try {
            $http = new Magento_HTTP_Adapter_Curl();
            $http->write(Zend_Http_Client::POST, $url, '1.1', array(), $sReq);
            $response = $http->read();
        } catch (Exception $e) {
            throw $e;
        }

        if ($error = $http->getError()) {
            $this->_notifyAdmin(__('IPN postback HTTP error: %1', $error));
            return;
        }

        if (false !== preg_match('~VERIFIED~si', $response)) {
            $this->processIpnVerified();
        } else {
            // TODO: possible PCI compliance issue - the $sReq may contain data that is supposed to be encrypted
            $this->_notifyAdmin(__('IPN postback Validation error: %1', $sReq));
        }
    }

    /**
     * Load and validate order
     *
     * @return Magento_Sales_Model_Order
     * @throws Exception
     */
    protected function _getOrder()
    {
        if (empty($this->_order)) {
            // get proper order
            $id = $this->getIpnFormData('invoice');
            $order = Mage::getModel('Magento_Sales_Model_Order');
            $order->loadByIncrementId($id);
            if (!$order->getId()) {
                // throws Exception intentionally, because cannot be logged to order comments
                throw new Exception(__('A wrong Order ID (%1) is specified.', $id));
            }
            $this->_order = $order;
        }
        return $this->_order;
    }

    /**
     * Validate incoming request data, as PayPal recommends
     *
     * @param Magento_Sales_Model_Order $order
     * @throws Magento_Core_Exception
     */
    protected function _verifyOrder(Magento_Sales_Model_Order $order)
    {
        // verify merchant email intended to receive notification
        $merchantEmail = $this->_config->businessAccount;
        if ($merchantEmail) {
            $receiverEmail = $this->getIpnFormData('business');
            if (!$receiverEmail) {
                $receiverEmail = $this->getIpnFormData('receiver_email');
            }
            if ($merchantEmail != $receiverEmail) {
                Mage::throwException(__('Requested %1 and configured %2 merchant emails do not match.', $receiverEmail, $merchantEmail));
            }
        }
    }

    /**
     * IPN workflow implementation
     * Everything should be added to order comments. In positive processing cases customer will get email notifications.
     * Admin will be notified on errors.
     */
    public function processIpnVerified()
    {
        $wasPaymentInformationChanged = false;
        try {
            try {
                $order = $this->_getOrder();
                $wasPaymentInformationChanged = $this->_importPaymentInformation($order->getPayment());
                $paymentStatus = $this->getIpnFormData('payment_status');
                switch ($paymentStatus) {
                    // paid with german bank
                    case self::STATUS_CREATED:
                        // break intentionally omitted
                    // paid with PayPal
                    case self::STATUS_COMPLETED:
                        $this->_registerPaymentCapture();
                        break;

                    // the holded payment was denied on paypal side
                    case self::STATUS_DENIED:
                        $this->_registerPaymentFailure(
                            __('The merchant denied this pending payment.')
                        );
                        break;
                    // customer attempted to pay via bank account, but failed
                    case self::STATUS_FAILED:
                        // cancel order
                        $this->_registerPaymentFailure(__('This customer did not pay.'));
                        break;

                    // refund forced by PayPal
                    case self::STATUS_REVERSED:
                        // break intentionally omitted
                    // refund by merchant on PayPal side
                    case self::STATUS_REFUNDED:
                        $this->_registerPaymentRefund();
                        break;

                    // refund that was forced by PayPal, returnred back.
                    case self::STATUS_CANCELED_REV:
                        // Magento cannot handle this for now. Just notify admin.
                        // potentially @see Magento_Sales_Model_Order_Creditmemo::cancel()
                        $history = $this->_explainRefundReason()->save();
                        $this->_notifyAdmin($history->getComment());
                        break;

                    // payment was obtained, but money were not captured yet
                    case self::STATUS_PENDING:
                        $this->_registerPaymentPending();
                        break;

                    // no really useful information here, just add status comment
                    case self::STATUS_PROCESSED:
                        $this->_createIpnComment('');
                        break;

                    // authorization expired, must void
                    case self::STATUS_EXPIRED:
                        $this->_registerPaymentVoid(__('Authorization expired'));
                        break;
                    // void by merchant on PayPal side
                    case self::STATUS_VOIDED:
                        $this->_registerPaymentVoid(__('The merchant voided the authorization.'));
                        break;
                }
            }
            catch (Magento_Core_Exception $e) {
                $history = $this->_createIpnComment(__('Note: %1', $e->getMessage()))
                    ->save();
                $this->_notifyAdmin($history->getComment(), $e);
            }
        } catch (Exception $e) {
            Mage::logException($e);
        }
        if ($wasPaymentInformationChanged) {
            $order->getPayment()->save();
        }
    }

    /**
     * Process completed payment
     * If an existing authorized invoice with specified txn_id exists - mark it as paid and save,
     * otherwise create a completely authorized/captured invoice
     *
     * Everything after saving order is not critical, thus done outside the transaction.
     *
     * @throws Magento_Core_Exception
     */
    protected function _registerPaymentCapture()
    {
        $order = $this->_getOrder();
        $payment = $order->getPayment();
        $payment->setTransactionId($this->getIpnFormData('transaction_id'))
            ->setCurrencyCode($this->getIpnFormData('mc_currency'))
            ->setPreparedMessage($this->_createIpnComment('', false))
            ->setParentTransactionId($this->getIpnFormData('parent_txn_id'))
            ->setShouldCloseParentTransaction(self::AUTH_STATUS_COMPLETED === $this->getIpnFormData('auth_status'))
            ->setIsTransactionClosed(0)
            ->registerCaptureNotification($this->getIpnFormData('mc_gross'));
        $order->save();

        // notify customer
        if ($invoice = $payment->getCreatedInvoice()) {
            $comment = $order->sendNewOrderEmail()->addStatusHistoryComment(
                    __('Notified customer about invoice #%1.', $invoice->getIncrementId())
                )
                ->setIsCustomerNotified(true)
                ->save();
        }
    }

    /**
     * Treat failed payment as order cancellation
     */
    protected function _registerPaymentFailure($explanationMessage = '')
    {
        $order = $this->_getOrder();
        $order->registerCancellation($this->_createIpnComment($explanationMessage, false), false)
            ->save();
    }

    /**
     *
     *
     */
    protected function _registerPaymentRefund()
    {
        // refund issued by merchant, cannot be reversed in future. Unlike reversals
        $isRefundFinal = (int)(self::STATUS_REVERSED !== $this->getIpnFormData('payment_status'));

        $order = $this->_getOrder();
        $payment = $order->getPayment()
            ->setPreparedMessage($this->_explainRefundReason(false))
            ->setTransactionId($this->getIpnFormData('transaction_id'))
            ->setParentTransactionId($this->getIpnFormData('parent_txn_id'))
            ->setIsTransactionClosed($isRefundFinal)
            ->registerRefundNotification(-1 * $this->getIpnFormData('mc_gross'));
        $order->save();

        // TODO: there is no way to close a capture right now

        if ($creditmemo = $payment->getCreatedCreditmemo()) {
            $creditmemo->sendEmail();
            $comment = $order->addStatusHistoryComment(
                    __('Notified customer about creditmemo #%1.', $creditmemo->getIncrementId())
                )
                ->setIsCustomerNotified(true)
                ->save();
        }
    }

    /**
     * @see pending_reason at https://cms.paypal.com/us/cgi-bin/?&cmd=_render-content&content_ID=developer/e_howto_admin_IPNReference
     */
    public function _registerPaymentPending()
    {
        $order = $this->_getOrder();
        $message = null;
        switch ($this->getIpnFormData('pending_reason')) {
            case 'address': // for some reason PayPal gives "address" reason, when Fraud Management Filter triggered
                $message = __('This customer used a non-confirmed address.');
                break;
            case 'echeck':
                $message = __('Waiting for customer\'s eCheck to be cleared.');
                // possible requires processing on our side as well
                break;
            case 'intl':
                $message = __('This merchant account does not have a withdrawal mechanism. You can accept or deny this payment in your PayPal account overview.');
                break;
            case 'multi-currency':
                $message = __('This payment includes multiple currencies. You can accept or deny this payment in your PayPal account overview.');
                break;
            case 'order':
                Mage::throwException('"Order" authorizations are not implemented. Please use "simple" authorization.');
            case 'authorization':
                $this->_registerPaymentAuthorization();
                break;
            case 'paymentreview':
                $message = __('Your payment is being reviewed by PayPal for your security.');
                break;
            case 'unilateral':
                $message = __('The payment was made to an email address that is not yet registered or confirmed.');
                break;
            case 'upgrade':
                $message = __('The merchant must upgrade the account to Business or Premier status.');
                break;
            case 'verify':
                $message = __('This merchant account is not verified.');
                break;
            case 'other':
                $message = __('Please contact PayPal Customer Service.');
                break;
        }
        if ($message) {
            $history = $this->_createIpnComment($message);
            $history->save();
        }
    }

    /**
     * Register authorization of a payment: create a non-paid invoice
     */
    protected function _registerPaymentAuthorization()
    {
        // authorize payment
        $order = $this->_getOrder();
        $payment = $order->getPayment()
            ->setPreparedMessage($this->_createIpnComment('', false))
            ->setTransactionId($this->getIpnFormData('transaction_id'))
            ->setParentTransactionId($this->getIpnFormData('parent_txn_id'))
            ->setIsTransactionClosed(0)
            ->registerAuthorizationNotification($this->getIpnFormData('mc_gross'));

        $order->save();
    }

    /**
     * Process transaction voiding.
     * We just can void only authorized transaction
     * Check if transaction authorized and not captured
     */
    protected function _registerPaymentVoid($explanationMessage = '')
    {
        $order = $this->_getOrder();

        $txnId = $this->getIpnFormData('transaction_id'); // this is the authorization transaction ID
        $order->getPayment()
            ->setPreparedMessage($this->_createIpnComment($explanationMessage, false))
            ->setParentTransactionId($txnId)
            ->registerVoidNotification();
        $order->save();
    }

    /**
     * Generate a "PayPal Verified" comment with additional explanation.
     * Returns the generated comment or order status history object
     *
     * @param string $comment
     * @param bool $addToHistory
     * @return string|Magento_Sales_Model_Order_Status_History
     */
    protected function _createIpnComment($comment = '', $addToHistory = true)
    {
        $paymentStatus = $this->getIpnFormData('payment_status');
        $message = __('IPN verification "%1".', $paymentStatus);
        if ($this->getIpnFormData('txn_id')) {
            $message .= ' ' . __('Original gateway transaction id: #%1.', $this->getIpnFormData('txn_id'));
        }
        if ($comment) {
            $message .= ' ' . $comment;
        }
        if ($addToHistory) {
            $message = $this->_getOrder()->addStatusHistoryComment($message);
            $message->setIsCustomerNotified(null);
        }
        return $message;
    }

    /**
     * Notify Administrator about exceptional situation
     *
     * @param $message
     * @param Exception $exception
     */
    protected function _notifyAdmin($message, Exception $exception = null)
    {
        // prevent notification failure cause order procesing failure
        try {
            Mage::log($message);
            if ($exception) {
                Mage::logException($exception);
            }
            // @TODO: dump the message and IPN form data
        } catch (Exception $e) {
            Mage::logException($e);
        }
    }

    /**
     * Generate a message basing on request reason_code
     * Should be invoked only on refunds
     * @see payment_status at https://cms.paypal.com/us/cgi-bin/?&cmd=_render-content&content_ID=developer/e_howto_admin_IPNReference
     *
     * @return Magento_Sales_Model_Order_Status_History
     */
    private function _explainRefundReason($addToHistory = true)
    {
        $message = __('unknown reason');
        switch ($this->getIpnFormData('reason_code')) {
            case 'adjustment_reversal':
                $message = __('reversal of an adjustment');
                break;
            case 'buyer-complaint':
                $message = __('customer complaint');
                break;
            case 'chargeback':
                $message = __('customer triggered a chargeback');
                break;
            case 'chargeback_reimbursement':
                $message = __('chargeback reimbursed');
                break;
            case 'chargeback_settlement':
                $message = __('chargeback settled');
                break;
            case 'guarantee':
                $message = __('customer triggered money-back guarantee');
                break;
            case 'other':
                $message = __('no reason');
                break;
            case 'refund':
                $message = __('merchant refunded payment');
                break;
        }
        return $this->_createIpnComment(__('Explanation: %1.', $message), $addToHistory);
    }

    /**
     * Map payment information from IPN to payment object
     * Returns true if there were changes in information
     *
     * @param Magento_Payment_Model_Info $payment
     * @return bool
     */
    protected function _importPaymentInformation(Magento_Payment_Model_Info $payment)
    {
        $was = $payment->getAdditionalInformation();

        $from = array();
        foreach (array(
            Magento_Paypal_Model_Info::PAYER_ID,
            'payer_email' => Magento_Paypal_Model_Info::PAYER_EMAIL,
            Magento_Paypal_Model_Info::PAYER_STATUS,
            Magento_Paypal_Model_Info::ADDRESS_STATUS,
            Magento_Paypal_Model_Info::PROTECTION_EL,
        ) as $privateKey => $publicKey) {
            if (is_int($privateKey)) {
                $privateKey = $publicKey;
            }
            $value = $this->getIpnFormData($privateKey);
            if ($value) {
                $from[$publicKey] = $value;
            }
        }

        // collect fraud filters
        $fraudFilters = array();
        for ($i = 1; $value = $this->getIpnFormData("fraud_management_pending_filters_{$i}"); $i++) {
            $fraudFilters[] = $value;
        }
        if ($fraudFilters) {
            $from[Magento_Paypal_Model_Info::FRAUD_FILTERS] = $fraudFilters;
        }

        Mage::getSingleton('Magento_Paypal_Model_Info')->importToPayment($from, $payment);
        return $was != $payment->getAdditionalInformation();
    }

}