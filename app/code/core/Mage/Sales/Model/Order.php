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
    const XML_PATH_NEW_ORDER_EMAIL_TEMPLATE     = 'sales_email/order/template';
    const XML_PATH_NEW_ORDER_EMAIL_IDENTITY     = 'sales_email/order/identity';
    const XML_PATH_NEW_ORDER_EMAIL_COPY_TO      = 'sales_email/order/copy_to';
    const XML_PATH_UPDATE_ORDER_EMAIL_TEMPLATE  = 'sales_email/order/comment_template';
    const XML_PATH_UPDATE_ORDER_EMAIL_IDENTITY  = 'sales_email/order/comment_identity';
    const XML_PATH_UPDATE_ORDER_EMAIL_COPY_TO   = 'sales_email/order/comment_copy_to';

    /**
     * Order states
     */
    const STATE_NEW        = 'new';
    const STATE_PROCESSING = 'processing';
    const STATE_COMPLETE   = 'complete';
    const STATE_CLOSED     = 'closed';
    const STATE_CANCELED   = 'canceled';
    const STATE_HOLDED     = 'holded';

    protected $_eventPrefix = 'sales_order';
    protected $_eventObject = 'order';

    protected $_addresses;
    protected $_items;
    protected $_payments;
    protected $_statusHistory;
    protected $_invoices;
    protected $_tracks;
    protected $_shipments;
    protected $_creditmemos;
    protected $_relatedObjects = array();
    protected $_orderCurrency = null;
    protected $_storeCurrency = null;

    /**
     * Initialize resource model
     */
    protected function _construct()
    {
        $this->_init('sales/order');
    }

    public function unsetData($key=null)
    {
        parent::unsetData($key);
        if (is_null($key)) {
            $this->_items = null;
        }
        return $this;
    }

    public function loadByIncrementId($incrementId)
    {
        return $this->loadByAttribute('increment_id', $incrementId);
    }

    public function loadByAttribute($attribute, $value)
    {
        $collection = $this->getCollection()
            ->addAttributeToSelect('*')
            ->addAttributeToFilter($attribute, $value)
            ->load()
                ->getItems();
        if (sizeof($collection)) {
            reset($collection);
            $order = current($collection);
            $this->setData($order->getData());
        }
        return $this;
    }

    /**
     * Retrieve store model instance
     *
     * @return Mage_Core_Model_Store
     */
    public function getStore()
    {
        if ($storeId = $this->getStoreId()) {
            return Mage::app()->getStore($storeId);
        }
        return Mage::app()->getStore();
    }

    /**
     * Retrieve order cancel availability
     *
     * @return bool
     */
    public function canCancel()
    {
        if ($this->canUnhold()) {
            return false;
        }

        if ($this->getState() === self::STATE_CANCELED ||
            $this->getState() === self::STATE_COMPLETE ||
            $this->getState() === self::STATE_CLOSED) {
            return false;
        }

        foreach ($this->getAllItems() as $item) {
            if ($item->getQtyToCancel()>0) {
                return true;
            }
        }
        return false;
    }

    /**
     * Retrieve order invoice availability
     *
     * @return bool
     */
    public function canInvoice()
    {
        if ($this->canUnhold()) {
            return false;
        }

        if ($this->getState() === self::STATE_CANCELED ||
            $this->getState() === self::STATE_COMPLETE ||
            $this->getState() === self::STATE_CLOSED ) {
            return false;
        }

        foreach ($this->getAllItems() as $item) {
            if ($item->getQtyToInvoice()>0) {
                return true;
            }
        }
        return false;
    }

    /**
     * Retrieve order credit memo (refund) availability
     *
     * @return bool
     */
    public function canCreditmemo()
    {
        if ($this->canUnhold()) {
            return false;
        }

        if ($this->getState() === self::STATE_CANCELED ||
            $this->getState() === self::STATE_CLOSED ) {
            return false;
        }

        /**
         * need use int, becose $a=762.73;$b=762.73; $a-$b!=0;
         */
        $paidCompare = (int) ($this->getTotalPaid() * 1000000);
        $refundedCompare = (int) ($this->getTotalRefunded() * 1000000);
        if ($paidCompare>$refundedCompare) {
            return true;
        }
        /**
         * Moshe: another solution
         */
        /*
        if (abs($this->getTotalPaid()-$this->getTotalRefunded())<.0001) {
            return true;
        }
        */

        return false;
    }

    /**
     * Retrieve order hold availability
     *
     * @return bool
     */
    public function canHold()
    {
        if ($this->getState() === self::STATE_CANCELED ||
            $this->getState() === self::STATE_COMPLETE ||
            $this->getState() === self::STATE_CLOSED ||
            $this->getState() === self::STATE_HOLDED) {
            return false;
        }

        return true;
    }

    /**
     * Retrieve order unhold availability
     *
     * @return bool
     */
    public function canUnhold()
    {
        return $this->getState() === self::STATE_HOLDED;
    }

    /**
     * Retrieve order shipment availability
     *
     * @return bool
     */
    public function canShip()
    {
        if ($this->canUnhold()) {
            return false;
        }

        foreach ($this->getAllItems() as $item) {
            if ($item->getQtyToShip()>0) {
                return true;
            }
        }
        return false;
    }

    /**
     * Retrieve order edit availability
     *
     * @return bool
     */
    public function canEdit()
    {
        if ($this->canUnhold()) {
            return false;
        }

        if ($this->getState() === self::STATE_CANCELED ||
            $this->getState() === self::STATE_COMPLETE ||
            $this->getState() === self::STATE_CLOSED) {
            return false;
        }
        return true;
    }

    /**
     * Retrieve order reorder availability
     *
     * @return bool
     */
    public function canReorder()
    {
        if ($this->canUnhold()) {
            return false;
        }

        $products = array();
        foreach ($this->getItemsCollection() as $item) {
            $products[] = $item->getProductId();
        }
        $productsCollection = Mage::getModel('catalog/product')->getCollection();

        if (!empty($products)) {
            $productsCollection->addIdFilter($products)
                ->load();
            foreach ($productsCollection as $product) {
                if ($product->isSalable()) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Retrieve order configuration model
     *
     * @return Mage_Sales_Model_Order_Config
     */
    public function getConfig()
    {
        return Mage::getSingleton('sales/order_config');
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

    /**
     * Declare order state
     *
     * @param   string $state
     * @return  Mage_Sales_Model_Order
     */
    public function setState($state, $status=false)
    {
        $this->setData('state', $state);
        if ($status) {
            if ($status === true) {
                $status = $this->getConfig()->getStateDefaultStatus($state);
            }
            $this->addStatusToHistory($status);
        }
        return $this;
    }

    /**
     * Retrieve label of order status
     *
     * @return string
     */
    public function getStatusLabel()
    {
        return $this->getConfig()->getStatusLabel($this->getStatus());
    }

    /**
     * Add status change information to history
     *
     * @param   string $status
     * @param   string $comments
     * @param   boolean $is_customer_notified
     * @return  Mage_Sales_Model_Order
     */
    public function addStatusToHistory($status, $comment='', $isCustomerNotified=false)
    {
        $status = Mage::getModel('sales/order_status_history')
            ->setStatus($status)
            ->setComment($comment)
            ->setIsCustomerNotified($isCustomerNotified);
        $this->addStatusHistory($status);
        return $this;
    }


    /**
     * Place order
     *
     * @return Mage_Sales_Model_Order
     */
    public function place()
    {
        $this->_placePayment();
        Mage::dispatchEvent('sales_order_place_after', array('order'=>$this));
        return $this;
    }

    public function hold()
    {
        if (!$this->canHold()) {
            Mage::throwException(Mage::helper('sales')->__('Hold action is not available'));
        }
        $this->setHoldBeforeState($this->getState());
        $this->setHoldBeforeStatus($this->getStatus());
        $this->setState(self::STATE_HOLDED, true);
        return $this;
    }

    public function unhold()
    {
        $this->setState($this->getHoldBeforeState(), $this->getHoldBeforeStatus());
        $this->setHoldBeforeState(null);
        $this->setHoldBeforeStatus(null);
        return $this;
    }

    /**
     * Cancel order
     *
     * @return Mage_Sales_Model_Order
     */
    public function cancel()
    {
        if ($this->canCancel()) {
            $this->getPayment()->cancel();
            $cancelState = self::STATE_CANCELED;
            foreach ($this->getAllItems() as $item) {
                if ($item->getQtyInvoiced()>$item->getQtyRefunded()) {
                    $cancelState = self::STATE_COMPLETE;
                }
                $item->cancel();
            }
            $this->setState($cancelState, true);
        }
        return $this;
    }

    /**
     * Retrieve tracking numbers
     *
     * @return array
     */
    public function getTrackingNumbers()
    {
        if ($this->getData('tracking_numbers')) {
            return explode(',', $this->getData('tracking_numbers'));
        }
        return array();
    }

    public function getShippingCarrier()
    {
        $carrierModel = $this->getData('shipping_carrier');
        if (is_null($carrierModel)) {
            $carrierModel = false;
            /**
             * $method - carrier_method
             */
            if ($method = $this->getShippingMethod()) {
                $data = explode('_', $method);
                $carrierCode = $data[0];
                $className = Mage::getStoreConfig('carriers/'.$carrierCode.'/model');
                if ($className) {
                    $carrierModel = Mage::getModel($className);
                }
            }
            $this->setData('shipping_carrier', $carrierModel);
        }
        return $carrierModel;
    }

    /**
     * Sending email with order data
     *
     * @return Mage_Sales_Model_Order
     */
    public function sendNewOrderEmail()
    {
        $itemsBlock = Mage::getBlockSingleton('sales/order_email_items')->setOrder($this);
        $paymentBlock = Mage::helper('payment')->getInfoBlock($this->getPayment());

        $mailTemplate = Mage::getModel('core/email_template');
        /* @var $mailTemplate Mage_Core_Model_Email_Template */
        if ($bcc = Mage::getStoreConfig(self::XML_PATH_NEW_ORDER_EMAIL_COPY_TO, $this->getStoreId())) {
            $mailTemplate->getMail()->addBcc($bcc);
        }

        $mailTemplate->setDesignConfig(array('area'=>'frontend', 'store'=>$this->getStoreId()))
            ->sendTransactional(
                Mage::getStoreConfig(self::XML_PATH_NEW_ORDER_EMAIL_TEMPLATE, $this->getStoreId()),
                Mage::getStoreConfig(self::XML_PATH_NEW_ORDER_EMAIL_IDENTITY, $this->getStoreId()),
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
        $mailTemplate = Mage::getModel('core/email_template');
        if ($bcc = Mage::getStoreConfig(self::XML_PATH_UPDATE_ORDER_EMAIL_COPY_TO, $this->getStoreId())) {
            $mailTemplate->getMail()->addBcc($bcc);
        }
        $mailTemplate->setDesignConfig(array('area'=>'frontend', 'store' => $this->getStoreId()))
            ->sendTransactional(
                Mage::getStoreConfig(self::XML_PATH_UPDATE_ORDER_EMAIL_TEMPLATE, $this->getStoreId()),
                Mage::getStoreConfig(self::XML_PATH_UPDATE_ORDER_EMAIL_IDENTITY, $this->getStoreId()),
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

/*********************** ADDRESSES ***************************/

    public function getAddressesCollection()
    {
        if (is_null($this->_addresses)) {
            $this->_addresses = Mage::getResourceModel('sales/order_address_collection')
                ->addAttributeToSelect('*')
                ->setOrderFilter($this->getId());

            if ($this->getId()) {
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
            $this->_items = Mage::getResourceModel('sales/order_item_collection')
                ->addAttributeToSelect('*')
                ->setOrderFilter($this->getId());

            if ($this->getId()) {
                foreach ($this->_items as $item) {
                    $item->setOrder($this);
                }
            }
        }
        return $this->_items;
    }

    public function getItemsRandomCollection($limit=1)
    {
        $collection = Mage::getModel('sales/order_item')->getCollection()
            ->addAttributeToSelect('*')
            ->setOrderFilter($this->getId())
            ->setOrder('RAND()')
            ->setPageSize($limit)
            ->load();

        $products = array();
        foreach ($collection as $item) {
            $products[] = $item->getProductId();
        }

        $productsCollection = Mage::getModel('catalog/product')
            ->getCollection()
            ->addIdFilter($products)
            ->load();
        foreach ($collection as $item) {
            $item->setProduct($productsCollection->getItemById($item->getProductId()));
        }
        return $collection;
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
            $this->_payments = Mage::getResourceModel('sales/order_payment_collection')
                ->addAttributeToSelect('*')
                ->setOrderFilter($this->getId());

            if ($this->getId()) {
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
            $this->_statusHistory = Mage::getResourceModel('sales/order_status_history_collection')
                ->addAttributeToSelect('*')
                ->setOrderFilter($this->getId());

            if ($this->getId()) {
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
            if (!$status->isDeleted() && $status->getComment() && $status->getIsCustomerNotified()) {
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
        $status->setOrder($this)
            ->setParentId($this->getId())
            ->setStoreId($this->getStoreId());
        $this->setStatus($status->getStatus());
        if (!$status->getId()) {
            $this->getStatusHistoryCollection()->addItem($status);
        }
        return $this;
    }


    /**
     * Enter description here...
     *
     * @return string
     */
    public function getRealOrderId()
    {
        $id = $this->getData('real_order_id');
        if (is_null($id)) {
            $id = $this->getIncrementId();
        }
        return $id;
    }

    /**
     * Retrieve order currency model instance
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
        return $this->getOrderCurrency()->format($price);
    }

    /**
     * Retrieve order website currency for working with base prices
     *
     * @return Mage_Directory_Model_Currency
     */
    public function getStoreCurrency()
    {
        if (is_null($this->_storeCurrency)) {
            $this->_storeCurrency = Mage::getModel('directory/currency')->load($this->getStoreCurrencyCode());
        }
        return $this->_storeCurrency;
    }


    public function formatBasePrice($price)
    {
        return $this->getStoreCurrency()->format($price);
    }

    public function isCurrencyDifferent()
    {
        return $this->getOrderCurrencyCode() != $this->getStoreCurrencyCode();
    }

    /**
     * Retrieve order total due value
     *
     * @return float
     */
    public function getTotalDue()
    {
        $total = $this->getGrandTotal()-$this->getTotalPaid();
        $total = Mage::app()->getStore($this->getStoreId())->roundPrice($total);
        return max($total, 0);
    }

    /**
     * Retrieve order total due value
     *
     * @return float
     */
    public function getBaseTotalDue()
    {
        $total = $this->getBaseGrandTotal()-$this->getBaseTotalPaid();
        $total = Mage::app()->getStore($this->getStoreId())->roundPrice($total);
        return max($total, 0);
    }

    /**
     * Retrieve order invoices collection
     *
     * @return unknown
     */
    public function getInvoiceCollection()
    {
        if (is_null($this->_invoices)) {
            $this->_invoices = Mage::getResourceModel('sales/order_invoice_collection')
                ->addAttributeToSelect('*')
                ->setOrderFilter($this->getId());

            if ($this->getId()) {
                foreach ($this->_invoices as $invoice) {
                    $invoice->setOrder($this);
                }
            }
        }
        return $this->_invoices;
    }

     /**
     * Retrieve order shipments collection
     *
     * @return unknown
     */
    public function getShipmentsCollection()
    {
        if (empty($this->_shipments)) {
            if ($this->getId()) {
                $this->_shipments = Mage::getResourceModel('sales/order_shipment_collection')
                    ->addAttributeToSelect('increment_id')
                    ->addAttributeToSelect('created_at')
                    ->addAttributeToSelect('total_qty')
                    ->joinAttribute('shipping_firstname', 'order_address/firstname', 'shipping_address_id', null, 'left')
                    ->joinAttribute('shipping_lastname', 'order_address/lastname', 'shipping_address_id', null, 'left')
                    ->setOrderFilter($this->getId())
                    ->load()
                    ;
            } else {
                return false;
            }
        }
        return $this->_shipments;
    }

    /**
     * Retrieve order creditmemos collection
     *
     * @return unknown
     */
    public function getCreditmemosCollection()
    {
        if (empty($this->_creditmemos)) {
            if ($this->getId()) {
                $this->_creditmemos = Mage::getResourceModel('sales/order_Creditmemo_collection')
                    ->addAttributeToSelect('*')
                    ->setOrderFilter($this->getId())
                    ->load();
            } else {
                return false;
            }
        }
        return $this->_creditmemos;
    }

    /**
     * Retrieve order tracking numbers collection
     *
     * @return unknown
     */
    public function getTracksCollection()
    {
        if (empty($this->_tracks)) {
            $this->_tracks = Mage::getResourceModel('sales/order_shipment_track_collection')
                ->addAttributeToSelect('*')
                ->setOrderFilter($this->getId());

            if ($this->getId()) {
                $this->_tracks->load();
            }
        }
        return $this->_tracks;
    }

    /**
     * Check order invoices availability
     *
     * @return bool
     */
    public function hasInvoices()
    {
        return $this->getInvoiceCollection()->count();
    }

    /**
     * Check order shipments availability
     *
     * @return bool
     */
    public function hasShipments()
    {
        return $this->getShipmentsCollection()->count();
    }

    /**
     * Check order creditmemos availability
     *
     * @return bool
     */
    public function hasCreditmemos()
    {
        return $this->getCreditmemosCollection()->count();
    }


    /**
     * Retrieve array of related objects
     *
     * Used for order saving
     *
     * @return array
     */
    public function getRelatedObjects()
    {
        return $this->_relatedObjects;
    }

    public function getCustomerName()
    {
        if ($this->getCustomerFirstname()) {
            $customerName = $this->getCustomerFirstname() . ' ' . $this->getCustomerLastname();
        }
        else {
            $customerName = Mage::helper('sales')->__('Guest');
        }
        return $customerName;
    }

    /**
     * Add New object to related array
     *
     * @param   Mage_Core_Model_Abstract $object
     * @return  Mage_Sales_Model_Order
     */
    public function addRelatedObject(Mage_Core_Model_Abstract $object)
    {
        $this->_relatedObjects[] = $object;
        return $this;
    }

    public function getCreatedAtFormated($format)
    {
        return Mage::getBlockSingleton('core/text')->formatDate($this->getCreatedAt(), $format);
    }

    public function getEmailCustomerNote()
    {
        if ($this->getCustomerNoteNotify()) {
            return $this->getCustomerNote();
        }
        return '';
    }

    /**
     * Processing object before save data
     *
     * @return Mage_Core_Model_Abstract
     */
    protected function _beforeSave()
    {
        parent::_beforeSave();
        $this->_checkState();
        if (!$this->getId()) {
            $store = $this->getStore();
            $name = array($store->getWebsite()->getName(),$store->getGroup()->getName(),$store->getName());
            $this->setStoreName(implode("\n", $name));
        }
        return $this;
    }

    protected function _checkState()
    {
        if (!$this->getId()) {
            return $this;
        }

        if ($this->getState() == self::STATE_NEW) {
            $this->setState(self::STATE_PROCESSING, true);
        }

        if ($this->getState() !== self::STATE_CANCELED
            && !$this->canUnhold()
            && !$this->canInvoice()
            && !$this->canShip()) {
            if ($this->canCreditmemo()) {
                if ($this->getState() !== self::STATE_COMPLETE) {
                    $this->setState(self::STATE_COMPLETE, true);
                }
            }
            else {
                if ($this->getState() !== self::STATE_CLOSED) {
                    $this->setState(self::STATE_CLOSED, true);
                }
            }
        }

        return $this;
    }
}