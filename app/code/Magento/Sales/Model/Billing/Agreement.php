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
 * Billing Agreement abstract model
 *
 * @method \Magento\Sales\Model\Resource\Billing\Agreement _getResource()
 * @method \Magento\Sales\Model\Resource\Billing\Agreement getResource()
 * @method int getCustomerId()
 * @method \Magento\Sales\Model\Billing\Agreement setCustomerId(int $value)
 * @method string getMethodCode()
 * @method \Magento\Sales\Model\Billing\Agreement setMethodCode(string $value)
 * @method string getReferenceId()
 * @method \Magento\Sales\Model\Billing\Agreement setReferenceId(string $value)
 * @method string getStatus()
 * @method \Magento\Sales\Model\Billing\Agreement setStatus(string $value)
 * @method string getCreatedAt()
 * @method \Magento\Sales\Model\Billing\Agreement setCreatedAt(string $value)
 * @method string getUpdatedAt()
 * @method \Magento\Sales\Model\Billing\Agreement setUpdatedAt(string $value)
 * @method int getStoreId()
 * @method \Magento\Sales\Model\Billing\Agreement setStoreId(int $value)
 * @method string getAgreementLabel()
 * @method \Magento\Sales\Model\Billing\Agreement setAgreementLabel(string $value)
 *
 * @category    Magento
 * @package     Magento_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Sales\Model\Billing;

class Agreement extends \Magento\Payment\Model\Billing\AgreementAbstract
{
    const STATUS_ACTIVE     = 'active';
    const STATUS_CANCELED   = 'canceled';

    /**
     * Related agreement orders
     *
     * @var array
     */
    protected $_relatedOrders = array();

    /**
     * Init model
     *
     */
    protected function _construct()
    {
        $this->_init('\Magento\Sales\Model\Resource\Billing\Agreement');
    }

    /**
     * Set created_at parameter
     *
     * @return \Magento\Core\Model\AbstractModel
     */
    protected function _beforeSave()
    {
        $date = \Mage::getModel('Magento\Core\Model\Date')->gmtDate();
        if ($this->isObjectNew() && !$this->getCreatedAt()) {
            $this->setCreatedAt($date);
        } else {
            $this->setUpdatedAt($date);
        }
        return parent::_beforeSave();
    }

    /**
     * Save agreement order relations
     *
     * @return \Magento\Core\Model\AbstractModel
     */
    protected function _afterSave()
    {
        if (!empty($this->_relatedOrders)) {
            $this->_saveOrderRelations();
        }
        return parent::_afterSave();
    }

    /**
     * Retrieve billing agreement status label
     *
     * @return string
     */
    public function getStatusLabel()
    {
        switch ($this->getStatus()) {
            case self::STATUS_ACTIVE:
                return __('Active');
            case self::STATUS_CANCELED:
                return __('Canceled');
        }
    }

    /**
     * Initialize token
     *
     * @return string
     */
    public function initToken()
    {
        $this->getPaymentMethodInstance()
            ->initBillingAgreementToken($this);
        return $this->getRedirectUrl();
    }

    /**
     * Get billing agreement details
     * Data from response is inside this object
     *
     * @return \Magento\Sales\Model\Billing\Agreement
     */
    public function verifyToken()
    {
        $this->getPaymentMethodInstance()
            ->getBillingAgreementTokenInfo($this);
        return $this;
    }

    /**
     * Create billing agreement
     *
     * @return \Magento\Sales\Model\Billing\Agreement
     */
    public function place()
    {
        $this->verifyToken();

        $paymentMethodInstance = $this->getPaymentMethodInstance()
            ->placeBillingAgreement($this);

        $this->setCustomerId($this->getCustomer()->getId())
            ->setMethodCode($this->getMethodCode())
            ->setReferenceId($this->getBillingAgreementId())
            ->setStatus(self::STATUS_ACTIVE)
            ->setAgreementLabel($paymentMethodInstance->getTitle())
            ->save();
        return $this;
    }

    /**
     * Cancel billing agreement
     *
     * @return \Magento\Sales\Model\Billing\Agreement
     */
    public function cancel()
    {
        $this->setStatus(self::STATUS_CANCELED);
        $this->getPaymentMethodInstance()->updateBillingAgreementStatus($this);
        return $this->save();
    }

    /**
     * Check whether can cancel billing agreement
     *
     * @return bool
     */
    public function canCancel()
    {
        return ($this->getStatus() != self::STATUS_CANCELED);
    }

    /**
     * Retrieve billing agreement statuses array
     *
     * @return array
     */
    public function getStatusesArray()
    {
        return array(
            self::STATUS_ACTIVE     => __('Active'),
            self::STATUS_CANCELED   => __('Canceled')
        );
    }

    /**
     * Validate data
     *
     * @return bool
     */
    public function isValid()
    {
        $result = parent::isValid();
        if (!$this->getCustomerId()) {
            $this->_errors[] = __('The customer ID is not set.');
        }
        if (!$this->getStatus()) {
            $this->_errors[] = __('The Billing Agreement status is not set.');
        }
        return $result && empty($this->_errors);
    }

    /**
     * Import payment data to billing agreement
     *
     * $payment->getBillingAgreementData() contains array with following structure :
     *  [billing_agreement_id]  => string
     *  [method_code]           => string
     *
     * @param \Magento\Sales\Model\Order\Payment $payment
     * @return \Magento\Sales\Model\Billing\Agreement
     */
    public function importOrderPayment(\Magento\Sales\Model\Order\Payment $payment)
    {
        $baData = $payment->getBillingAgreementData();

        $this->_paymentMethodInstance = (isset($baData['method_code']))
            ? \Mage::helper('Magento\Payment\Helper\Data')->getMethodInstance($baData['method_code'])
            : $payment->getMethodInstance();
        if ($this->_paymentMethodInstance) {
            $this->_paymentMethodInstance->setStore($payment->getMethodInstance()->getStore());
            $this->setCustomerId($payment->getOrder()->getCustomerId())
                ->setMethodCode($this->_paymentMethodInstance->getCode())
                ->setReferenceId($baData['billing_agreement_id'])
                ->setStatus(self::STATUS_ACTIVE);
        }
        return $this;
    }

    /**
     * Retrieve available customer Billing Agreements
     *
     * @param int $customer
     * @return \Magento\Paypal\Controller\Express\AbstractExpress
     */
    public function getAvailableCustomerBillingAgreements($customerId)
    {
        $collection = \Mage::getResourceModel('Magento\Sales\Model\Resource\Billing\Agreement\Collection');
        $collection->addFieldToFilter('customer_id', $customerId)
            ->addFieldToFilter('status', self::STATUS_ACTIVE)
            ->setOrder('agreement_id');
        return $collection;
    }

    /**
     * Check whether need to create billing agreement for customer
     *
     * @param int $customerId
     * @return bool
     */
    public function needToCreateForCustomer($customerId)
    {
        return $customerId ? count($this->getAvailableCustomerBillingAgreements($customerId)) == 0 : false;
    }

    /**
     * Add order relation to current billing agreement
     *
     * @param int|\Magento\Sales\Model\Order $orderId
     * @return \Magento\Sales\Model\Billing\Agreement
     */
    public function addOrderRelation($orderId)
    {
        $this->_relatedOrders[] = $orderId;
        return $this;
    }

    /**
     * Save related orders
     */
    protected function _saveOrderRelations()
    {
        foreach ($this->_relatedOrders as $order) {
            $orderId = $order instanceof \Magento\Sales\Model\Order ? $order->getId() : (int) $order;
            $this->getResource()->addOrderRelation($this->getId(), $orderId);
        }
    }

}
