<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Sales
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Order model
 *
 * Order Attributes
 *  entity_id (id)
 *  order_status_id
 *  is_virtual
 *  is_multi_payment
 *
 *  base_currency_code
 *  store_currency_code
 *  order_currency_code
 *  store_to_base_rate
 *  store_to_order_rate
 *
 *  remote_ip
 *  quote_id
 *  quote_address_id
 *  billing_address_id
 *  shipping_address_id
 *  coupon_code
 *  giftcert_code
 *  weight
 *  shipping_method
 *  shipping_description
 *
 *  subtotal
 *  tax_amount
 *  shipping_amount
 *  discount_amount
 *  giftcert_amount
 *  custbalance_amount
 *  grand_total
 *
 *  total_paid
 *  total_due
 *  total_qty_ordered
 *  applied_rule_ids
 *
 *  customer_id
 *  customer_group_id
 *  customer_email
 *  customer_note
 *  customer_note_notify
 *
 * Supported events:
 *  sales_order_load_after
 *  sales_order_save_before
 *  sales_order_save_after
 *  sales_order_delete_before
 *  sales_order_delete_after
 *
 * @author  Moshe Gurvich <moshe@varien.com>
 * @author  Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Sales_Model_Order extends Mage_Core_Model_Abstract
{
    /**
     * XML configuration paths
     */
    const XML_PATH_NEW_ORDER_EMAIL_TEMPLATE     = 'sales/new_order/email_template';
    const XML_PATH_NEW_ORDER_EMAIL_IDENTITY     = 'sales/new_order/email_identity';
    const XML_PATH_UPDATE_ORDER_EMAIL_TEMPLATE  = 'sales/order_update/email_template';
    const XML_PATH_UPDATE_ORDER_EMAIL_IDENTITY  = 'sales/order_update/email_identity';

    const STATUS_NEW        = 1;
    const STATUS_PROCESSING = 2;
    const STATUS_COMPLETE   = 3;
    const STATUS_CLOSED     = 4;
    const STATUS_VOID       = 5;
    const STATUS_CANCELLED  = 6;

    const PAYMENT_STATUS_PENDING        = 1;
    const PAYMENT_STATUS_NOT_AUTHORIZED = 2;
    const PAYMENT_STATUS_AUTHORIZED     = 3;
    const PAYMENT_STATUS_PARTIAL        = 4;
    const PAYMENT_STATUS_PAID           = 5;

    const SHIPPING_STATUS_PENDING   = 1;
    const SHIPPING_STATUS_PARTIAL   = 2;
    const SHIPPING_STATUS_SHIPPED   = 3;

    const REFUND_STATUS_NOT_REFUND  = 1;
    const REFUND_STATUS_PANDING     = 2;
    const REFUND_STATUS_PARTIAL     = 3;
    const REFUND_STATUS_REFUNDED    = 4;

    protected $_eventPrefix = 'sales_order';
    protected $_eventObject = 'order';

    protected $_addresses;
    protected $_items;
    protected $_payments;
    protected $_statusHistory;
    protected $_orderCurrency = null;

    /**
     * Initialize resource model
     */
    protected function _construct()
    {
        $this->_init('sales/order');
    }

    /**
     * Retrieve order cancel availability
     *
     * @return bool
     */
    public function canCancel()
    {
        return true;
    }

    /**
     * Retrieve order invoice availability
     *
     * @return bool
     */
    public function canInvoice()
    {
        return true;
    }

    /**
     * Retrieve order credit memo (refund) availability
     *
     * @return bool
     */
    public function canCreditmemo()
    {
        return true;
    }

    /**
     * Retrieve order hold availability
     *
     * @return bool
     */
    public function canHold()
    {
        return false;
    }

    /**
     * Retrieve order unhold availability
     *
     * @return bool
     */
    public function canUnhold()
    {
        return false;
    }

    /**
     * Retrieve order shipment availability
     *
     * @return bool
     */
    public function canShip()
    {
        return true;
    }

    /**
     * Retrieve order edit availability
     *
     * @return bool
     */
    public function canEdit()
    {
        return true;
    }

    /**
     * Retrieve order reorder availability
     *
     * @return bool
     */
    public function canReorder()
    {
        return true;
    }

    /**
     * Place order
     *
     * @return Mage_Sales_Model_Order
     */
    public function place()
    {
        $this->_placePayment();
        return $this;
    }

    /**
     * Place order payments
     *
     * @return unknown
     */
    protected function _placePayment()
    {
        $this->getPayment()->place();
        return $this;
    }

    /**
     * Retrieve order payment model object
     *
     * @return Mage_Sales_Model_Order_Payment
     */
    public function getPayment()
    {
        foreach ($this->getPaymentsCollection() as $payment) {
            if (!$payment->isDeleted()) {
                return $payment;
            }
        }
        return false;
    }

    /**
     * Declare order billing address
     *
     * @param   Mage_Sales_Model_Order_Address $address
     * @return  Mage_Sales_Model_Order
     */
    public function setBillingAddress(Mage_Sales_Model_Order_Address $address)
    {
        $old = $this->getBillingAddress();
        if (!empty($old)) {
            $address->setId($old->getId());
        }
        $this->addAddress($address->setAddressType('billing'));
        return $this;
    }

    /**
     * Declare order shipping address
     *
     * @param   Mage_Sales_Model_Order_Address $address
     * @return  Mage_Sales_Model_Order
     */
    public function setShippingAddress(Mage_Sales_Model_Order_Address $address)
    {
        $old = $this->getShippingAddress();
        if (!empty($old)) {
            $address->setId($old->getId());
        }
        $this->addAddress($address->setAddressType('shipping'));
        return $this;
    }

    /**
     * Retrieve order billing address
     *
     * @return Mage_Sales_Model_Order_Address
     */
    public function getBillingAddress()
    {
        foreach ($this->getAddressesCollection() as $address) {
            if ($address->getAddressType()=='billing' && !$address->isDeleted()) {
                return $address;
            }
        }
        return false;
    }

    /**
     * Retrieve order shipping address
     *
     * @return Mage_Sales_Model_Order_Address
     */
    public function getShippingAddress()
    {
        foreach ($this->getAddressesCollection() as $address) {
            if ($address->getAddressType()=='shipping' && !$address->isDeleted()) {
                return $address;
            }
        }
        return false;
    }


































    public function processPayments()
    {
        $method = $this->getPayment()->getMethod();

        if (!($modelName = Mage::getStoreConfig('payment/'.$method.'/model'))
            ||!($model = Mage::getModel($modelName))) {
            return $this;
        }

        $this->setDocument($this->getOrder());

        $model->onOrderValidate($this->getPayment());

        if ($this->getPayment()->getStatus()!=='APPROVED') {
            $errors = $this->getErrors();
            $errors[] = $this->getPayment()->getStatusDescription();
            $this->setErrors($errors);
        }

        return $this;
    }































    /**
     * Sending email with order data
     *
     * @return Mage_Sales_Model_Order
     */
    public function sendNewOrderEmail()
    {
        $itemsBlock = Mage::getHelper('sales/order_email_items')
            ->setOrder($this);
        $paymentBlock = Mage::helper('payment')->getInfoBlock($this->getPayment());

        Mage::getModel('core/email_template')->sendTransactional(
            Mage::getStoreConfig(self::XML_PATH_NEW_ORDER_EMAIL_TEMPLATE),
            Mage::getStoreConfig(self::XML_PATH_NEW_ORDER_EMAIL_IDENTITY),
            $this->getCustomerEmail(),
            $this->getBillingAddress()->getName(),
            array(
              'order'       => $this,
              'billing'     => $this->getBillingAddress(),
              'payment_html'=> $paymentBlock->toHtml(),
              'items_html'  => $itemsBlock->toHtml(),
            )
        );
        return $this;
    }

    /**
     * Sending email with order update information
     *
     * @return Mage_Sales_Model_Order
     */
    public function sendOrderUpdateEmail($comment='')
    {
        Mage::getModel('core/email_template')
            ->sendTransactional(
                Mage::getStoreConfig(self::XML_PATH_UPDATE_ORDER_EMAIL_TEMPLATE),
                Mage::getStoreConfig(self::XML_PATH_UPDATE_ORDER_EMAIL_IDENTITY),
                $this->getCustomerEmail(),
                $this->getBillingAddress()->getName(),
                array(
                    'order'=>$this,
                    'billing'=>$this->getBillingAddress(),
                    'comment'=>$comment
                )
            );
        return $this;
    }

    /**
     * Validate order
     *
     * @return Mage_Sales_Model_Order
     */
    public function validate()
    {
        $this->setErrors(array());
        $this->processPayments();

        if ($this->getErrors()) {
            throw Mage::exception('Mage_Sales', Mage::helper('sales')->__('Errors during order creation'));
        }

        return $this;
    }


/*********************** ADDRESSES ***************************/

    public function getAddressesCollection()
    {
        if (is_null($this->_addresses)) {
            $this->_addresses = Mage::getResourceModel('sales/order_address_collection');

            if ($this->getId()) {
                $this->_addresses
                    ->addAttributeToSelect('*')
                    ->setOrderFilter($this->getId())
                    ->load();
                foreach ($this->_addresses as $address) {
                    $address->setOrder($this);
                }
            }
        }

        return $this->_addresses;
    }

    public function getAddressById($addressId)
    {
        foreach ($this->getAddressesCollection() as $address) {
            if ($address->getId()==$addressId) {
                return $address;
            }
        }
        return false;
    }

    public function addAddress(Mage_Sales_Model_Order_Address $address)
    {
        $address->setOrder($this)->setParentId($this->getId());
        if (!$address->getId()) {
            $this->getAddressesCollection()->addItem($address);
        }
        return $this;
    }

/*********************** ITEMS ***************************/

    public function getItemsCollection()
    {
        if (is_null($this->_items)) {
            $this->_items = Mage::getResourceModel('sales/order_item_collection');

            if ($this->getId()) {
                $this->_items
                    ->addAttributeToSelect('*')
                    ->setOrderFilter($this->getId())
                    ->load();
                foreach ($this->_items as $item) {
                    $item->setOrder($this);
                }
            }
        }
        return $this->_items;
    }

    public function getAllItems()
    {
        $items = array();
        foreach ($this->getItemsCollection() as $item) {
            if (!$item->isDeleted()) {
                $items[] =  $item;
            }
        }
        return $items;
    }

    public function getItemById($itemId)
    {
        foreach ($this->getItemsCollection() as $item) {
            if ($item->getId()==$itemId) {
                return $item;
            }
        }
        return false;
    }

    public function addItem(Mage_Sales_Model_Order_Item $item)
    {
        $item->setOrder($this)->setParentId($this->getId());
        if (!$item->getId()) {
            $this->getItemsCollection()->addItem($item);
        }
        return $this;
    }

/*********************** PAYMENTS ***************************/

    public function getPaymentsCollection()
    {
        if (is_null($this->_payments)) {
            $this->_payments = Mage::getResourceModel('sales/order_payment_collection');

            if ($this->getId()) {
                $this->_payments
                    ->addAttributeToSelect('*')
                    ->setOrderFilter($this->getId())
                    ->load();
                foreach ($this->_payments as $payment) {
                    $payment->setOrder($this);
                }
            }
        }
        return $this->_payments;
    }

    public function getAllPayments()
    {
        $payments = array();
        foreach ($this->getPaymentsCollection() as $payment) {
            if (!$payment->isDeleted()) {
                $payments[] =  $payment;
            }
        }
        return $payments;
    }


    public function getPaymentById($paymentId)
    {
        foreach ($this->getPaymentsCollection() as $payment) {
            if ($payment->getId()==$paymentId) {
                return $payment;
            }
        }
        return false;
    }

    public function addPayment(Mage_Sales_Model_Order_Payment $payment)
    {
        $payment->setOrder($this)
            ->setParentId($this->getId());
        if (!$payment->getId()) {
            $this->getPaymentsCollection()->addItem($payment);
        }
        return $this;
    }

    public function setPayment(Mage_Sales_Model_Order_Payment $payment)
    {
        if (!$this->getIsMultiPayment() && ($old = $this->getPayment())) {
            $payment->setId($old->getId());
        }
        $this->addPayment($payment);

        return $payment;
    }

/*********************** STATUSES ***************************/

    /**
     * Enter description here...
     *
     * @return Mage_Sales_Model_Entity_Order_Status_History_Collection
     */
    public function getStatusHistoryCollection()
    {
        if (is_null($this->_statusHistory)) {
            $this->_statusHistory = Mage::getResourceModel('sales/order_status_history_collection');

            if ($this->getId()) {
                $this->_statusHistory
                    ->addAttributeToSelect('*')
                    ->setOrderFilter($this->getId())
                    ->load();
                foreach ($this->_statusHistory as $status) {
                    $status->setOrder($this);
                }
            }
        }
        return $this->_statusHistory;
    }

    /**
     * Enter description here...
     *
     * @return array
     */
    public function getAllStatusHistory()
    {
        $history = array();
        foreach ($this->getStatusHistoryCollection() as $status) {
            if (!$status->isDeleted()) {
                $history[] =  $status;
            }
        }
        return $history;
    }

    /**
     * Enter description here...
     *
     * @return array
     */
    public function getVisibleStatusHistory()
    {
        $history = array();
        foreach ($this->getStatusHistoryCollection() as $status) {
            if (!$status->isDeleted() && $status->getComments() && $status->getIsCustomerNotified()) {
                $history[] =  $status;
            }
        }
        return $history;
    }

    public function getStatusHistoryById($statusId)
    {
        foreach ($this->getStatusHistoryCollection() as $status) {
            if ($status->getId()==$statusId) {
                return $status;
            }
        }
        return false;
    }

    public function addStatusHistory(Mage_Sales_Model_Order_Status_History $status)
    {
        $status->setOrder($this)->setParentId($this->getId())->setStoreId($this->getStoreId());
        $this->setOrderStatusId($status->getOrderStatusId());
        if (!$status->getId()) {
            $this->getStatusHistoryCollection()->addItem($status);
        }
        return $this;
    }

    /**
     * Enter description here...
     *
     * @param int $statusId
     * @param string $comments
     * @param boolean $is_customer_notified
     * @return Mage_Sales_Model_Order
     */
    public function addStatus($statusId, $comments='', $isCustomerNotified=false)
    {
        $status = Mage::getModel('sales/order_status_history')
            ->setOrderStatusId($statusId)
            ->setComments($comments)
            ->setIsCustomerNotified($isCustomerNotified);
        $this->addStatusHistory($status);

        return $this;
    }

    /**
     * Adding new order status
     *
     * @param   string $comment
     * @param   bool $notifyCustomer
     * @return  Mage_Sales_Model_Order
     */
    public function addStatusNewOrder($comment='', $notifyCustomer=false)
    {
        //return $this->addStatus(self::ORDER_STATUS_NEW, $comment, $notifyCustomer);
    }

    /**
     * Enter description here...
     *
     * @return string
     */
    public function getRealOrderId()
    {
        return $this->getIncrementId();
    }

    /**
     * Enter description here...
     *
     * @return Mage_Directory_Model_Currency
     */
    public function getOrderCurrency()
    {
        if (is_null($this->_orderCurrency)) {
            $this->_orderCurrency = Mage::getModel('directory/currency')->load($this->getOrderCurrencyCode());
        }
        return $this->_orderCurrency;
    }

    /**
     * Retrieve formated price value includeing order rate
     *
     * @param   float $price
     * @return  string
     */
    public function formatPrice($price)
    {
        if (!($rate = floatval($this->getStoreToOrderRate()))) {
            $rate = 1;
        }
        //$price = $price*$rate;
        return $this->getOrderCurrency()->format($price);
    }

    /**
     * Enter description here...
     *
     * @return Mage_Sales_Model_Order_Status
     */
    public function getStatus()
    {
        return Mage::getModel('sales/order_status')->load($this->getOrderStatusId());
    }

    public function _afterSave()
    {
        Mage::dispatchEvent('sales_quote_save_after', array('order'=>$this));
        parent::_afterSave();
    }

    /**
     * Enter description here...
     *
     * @return Mage_Sales_Model_Order
     */
    public function calcTotalDue()
    {
        $this->setTotalDue(max($this->getGrandTotal() - $this->getTotalPaid(), 0));
        return $this;
    }

    /**
     * Enter description here...
     *
     * @return float
     */
    public function getTotalDue()
    {
        $this->calcTotalDue();
        return $this->getData('total_due');
    }

    public function getCreatedAtFormated($format)
    {
        return Mage::getHelper('core/text')->formatDate($this->getCreatedAt(), $format);
    }

    public function getEmailCustomerNote()
    {
        if ($this->getCustomerNoteNotify()) {
            return $this->getCustomerNote();
        }
        return '';
    }
}
