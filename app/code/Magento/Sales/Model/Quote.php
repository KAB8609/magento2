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
 * Quote model
 *
 * Supported events:
 *  sales_quote_load_after
 *  sales_quote_save_before
 *  sales_quote_save_after
 *  sales_quote_delete_before
 *  sales_quote_delete_after
 *
 * @method Magento_Sales_Model_Resource_Quote _getResource()
 * @method Magento_Sales_Model_Resource_Quote getResource()
 * @method Magento_Sales_Model_Quote setStoreId(int $value)
 * @method string getCreatedAt()
 * @method Magento_Sales_Model_Quote setCreatedAt(string $value)
 * @method string getUpdatedAt()
 * @method Magento_Sales_Model_Quote setUpdatedAt(string $value)
 * @method string getConvertedAt()
 * @method Magento_Sales_Model_Quote setConvertedAt(string $value)
 * @method int getIsActive()
 * @method Magento_Sales_Model_Quote setIsActive(int $value)
 * @method Magento_Sales_Model_Quote setIsVirtual(int $value)
 * @method int getIsMultiShipping()
 * @method Magento_Sales_Model_Quote setIsMultiShipping(int $value)
 * @method int getItemsCount()
 * @method Magento_Sales_Model_Quote setItemsCount(int $value)
 * @method float getItemsQty()
 * @method Magento_Sales_Model_Quote setItemsQty(float $value)
 * @method int getOrigOrderId()
 * @method Magento_Sales_Model_Quote setOrigOrderId(int $value)
 * @method float getStoreToBaseRate()
 * @method Magento_Sales_Model_Quote setStoreToBaseRate(float $value)
 * @method float getStoreToQuoteRate()
 * @method Magento_Sales_Model_Quote setStoreToQuoteRate(float $value)
 * @method string getBaseCurrencyCode()
 * @method Magento_Sales_Model_Quote setBaseCurrencyCode(string $value)
 * @method string getStoreCurrencyCode()
 * @method Magento_Sales_Model_Quote setStoreCurrencyCode(string $value)
 * @method string getQuoteCurrencyCode()
 * @method Magento_Sales_Model_Quote setQuoteCurrencyCode(string $value)
 * @method float getGrandTotal()
 * @method Magento_Sales_Model_Quote setGrandTotal(float $value)
 * @method float getBaseGrandTotal()
 * @method Magento_Sales_Model_Quote setBaseGrandTotal(float $value)
 * @method Magento_Sales_Model_Quote setCheckoutMethod(string $value)
 * @method int getCustomerId()
 * @method Magento_Sales_Model_Quote setCustomerId(int $value)
 * @method Magento_Sales_Model_Quote setCustomerTaxClassId(int $value)
 * @method Magento_Sales_Model_Quote setCustomerGroupId(int $value)
 * @method string getCustomerEmail()
 * @method Magento_Sales_Model_Quote setCustomerEmail(string $value)
 * @method string getCustomerPrefix()
 * @method Magento_Sales_Model_Quote setCustomerPrefix(string $value)
 * @method string getCustomerFirstname()
 * @method Magento_Sales_Model_Quote setCustomerFirstname(string $value)
 * @method string getCustomerMiddlename()
 * @method Magento_Sales_Model_Quote setCustomerMiddlename(string $value)
 * @method string getCustomerLastname()
 * @method Magento_Sales_Model_Quote setCustomerLastname(string $value)
 * @method string getCustomerSuffix()
 * @method Magento_Sales_Model_Quote setCustomerSuffix(string $value)
 * @method string getCustomerDob()
 * @method Magento_Sales_Model_Quote setCustomerDob(string $value)
 * @method string getCustomerNote()
 * @method Magento_Sales_Model_Quote setCustomerNote(string $value)
 * @method int getCustomerNoteNotify()
 * @method Magento_Sales_Model_Quote setCustomerNoteNotify(int $value)
 * @method int getCustomerIsGuest()
 * @method Magento_Sales_Model_Quote setCustomerIsGuest(int $value)
 * @method string getRemoteIp()
 * @method Magento_Sales_Model_Quote setRemoteIp(string $value)
 * @method string getAppliedRuleIds()
 * @method Magento_Sales_Model_Quote setAppliedRuleIds(string $value)
 * @method string getReservedOrderId()
 * @method Magento_Sales_Model_Quote setReservedOrderId(string $value)
 * @method string getPasswordHash()
 * @method Magento_Sales_Model_Quote setPasswordHash(string $value)
 * @method string getCouponCode()
 * @method Magento_Sales_Model_Quote setCouponCode(string $value)
 * @method string getGlobalCurrencyCode()
 * @method Magento_Sales_Model_Quote setGlobalCurrencyCode(string $value)
 * @method float getBaseToGlobalRate()
 * @method Magento_Sales_Model_Quote setBaseToGlobalRate(float $value)
 * @method float getBaseToQuoteRate()
 * @method Magento_Sales_Model_Quote setBaseToQuoteRate(float $value)
 * @method string getCustomerTaxvat()
 * @method Magento_Sales_Model_Quote setCustomerTaxvat(string $value)
 * @method string getCustomerGender()
 * @method Magento_Sales_Model_Quote setCustomerGender(string $value)
 * @method float getSubtotal()
 * @method Magento_Sales_Model_Quote setSubtotal(float $value)
 * @method float getBaseSubtotal()
 * @method Magento_Sales_Model_Quote setBaseSubtotal(float $value)
 * @method float getSubtotalWithDiscount()
 * @method Magento_Sales_Model_Quote setSubtotalWithDiscount(float $value)
 * @method float getBaseSubtotalWithDiscount()
 * @method Magento_Sales_Model_Quote setBaseSubtotalWithDiscount(float $value)
 * @method int getIsChanged()
 * @method Magento_Sales_Model_Quote setIsChanged(int $value)
 * @method int getTriggerRecollect()
 * @method Magento_Sales_Model_Quote setTriggerRecollect(int $value)
 * @method string getExtShippingInfo()
 * @method Magento_Sales_Model_Quote setExtShippingInfo(string $value)
 * @method int getGiftMessageId()
 * @method Magento_Sales_Model_Quote setGiftMessageId(int $value)
 * @method bool|null getIsPersistent()
 * @method Magento_Sales_Model_Quote setIsPersistent(bool $value)
 */
class Magento_Sales_Model_Quote extends Magento_Core_Model_Abstract
{
    /**
     * Checkout login method key
     */
    const CHECKOUT_METHOD_LOGIN_IN  = 'login_in';

    /**
     * @var string
     */
    protected $_eventPrefix = 'sales_quote';

    /**
     * @var string
     */
    protected $_eventObject = 'quote';

    /**
     * Quote customer model object
     *
     * @var Magento_Customer_Model_Customer
     */
    protected $_customer;

    /**
     * Quote addresses collection
     *
     * @var Magento_Eav_Model_Entity_Collection_Abstract
     */
    protected $_addresses = null;

    /**
     * Quote items collection
     *
     * @var Magento_Eav_Model_Entity_Collection_Abstract
     */
    protected $_items = null;

    /**
     * Quote payments
     *
     * @var Magento_Eav_Model_Entity_Collection_Abstract
     */
    protected $_payments = null;

    /**
     * Different groups of error infos
     *
     * @var array
     */
    protected $_errorInfoGroups = array();

    /**
     * Whether quote should not be saved
     *
     * @var bool
     */
    protected $_preventSaving = false;

    /**
     * Core data
     *
     * @var Magento_Core_Helper_Data
     */
    protected $_coreData = null;

    /**
     * Catalog product
     *
     * @var Magento_Catalog_Helper_Product
     */
    protected $_catalogProduct = null;

    /**
     * Sales data
     *
     * @var Magento_Sales_Helper_Data
     */
    protected $_salesData = null;

    /**
     * Core event manager proxy
     *
     * @var Magento_Core_Model_Event_Manager
     */
    protected $_eventManager = null;

    /**
     * Core store config
     *
     * @var Magento_Core_Model_Store_Config
     */
    protected $_coreStoreConfig;

    /**
     * @var Magento_Core_Model_StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var Magento_Core_Model_Config
     */
    protected $_config;

    /**
     * @var Magento_Sales_Model_Quote_AddressFactory
     */
    protected $_quoteAddressFactory;

    /**
     * @var Magento_Customer_Model_CustomerFactory
     */
    protected $_customerFactory;

    /**
     * @var Magento_Customer_Model_GroupFactory
     */
    protected $_customerGroupFactory;

    /**
     * @var Magento_Sales_Model_Resource_Quote_Item_CollectionFactory
     */
    protected $_quoteItemCollFactory;

    /**
     * @var Magento_Sales_Model_Quote_ItemFactory
     */
    protected $_quoteItemFactory;

    /**
     * @var Magento_Core_Model_Message
     */
    protected $_message;

    /**
     * @var Magento_Sales_Model_Status_ListFactory
     */
    protected $_statusListFactory;

    /**
     * @var Magento_Catalog_Model_ProductFactory
     */
    protected $_productFactory;

    /**
     * @var Magento_Sales_Model_Quote_PaymentFactory
     */
    protected $_quotePaymentFactory;

    /**
     * @var Magento_Sales_Model_Resource_Quote_Payment_CollectionFactory
     */
    protected $_quotePaymentCollFactory;

    /**
     * @var Magento_Sales_Model_Recurring_ProfileFactory
     */
    protected $_recurringProfileFactory;

    /**
     * @param Magento_Core_Model_Event_Manager $eventManager
     * @param Magento_Sales_Helper_Data $salesData
     * @param Magento_Catalog_Helper_Product $catalogProduct
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Model_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param Magento_Core_Model_Store_Config $coreStoreConfig
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @param Magento_Core_Model_Config $config
     * @param Magento_Sales_Model_Quote_AddressFactory $quoteAddressFactory
     * @param Magento_Customer_Model_CustomerFactory $customerFactory
     * @param Magento_Customer_Model_GroupFactory $customerGroupFactory
     * @param Magento_Sales_Model_Resource_Quote_Item_CollectionFactory $quoteItemCollFactory
     * @param Magento_Sales_Model_Quote_ItemFactory $quoteItemFactory
     * @param Magento_Core_Model_Message $message
     * @param Magento_Sales_Model_Status_ListFactory $statusListFactory
     * @param Magento_Catalog_Model_ProductFactory $productFactory
     * @param Magento_Sales_Model_Quote_PaymentFactory $quotePaymentFactory
     * @param Magento_Sales_Model_Resource_Quote_Payment_CollectionFactory $quotePaymentCollFactory
     * @param Magento_Sales_Model_Recurring_ProfileFactory $recurringProfileFactory
     * @param Magento_Core_Model_Resource_Abstract $resource
     * @param Magento_Data_Collection_Db $resourceCollection
     * @param array $data
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        Magento_Core_Model_Event_Manager $eventManager,
        Magento_Sales_Helper_Data $salesData,
        Magento_Catalog_Helper_Product $catalogProduct,
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Model_Context $context,
        Magento_Core_Model_Registry $registry,
        Magento_Core_Model_Store_Config $coreStoreConfig,
        Magento_Core_Model_StoreManagerInterface $storeManager,
        Magento_Core_Model_Config $config,
        Magento_Sales_Model_Quote_AddressFactory $quoteAddressFactory,
        Magento_Customer_Model_CustomerFactory $customerFactory,
        Magento_Customer_Model_GroupFactory $customerGroupFactory,
        Magento_Sales_Model_Resource_Quote_Item_CollectionFactory $quoteItemCollFactory,
        Magento_Sales_Model_Quote_ItemFactory $quoteItemFactory,
        Magento_Core_Model_Message $message,
        Magento_Sales_Model_Status_ListFactory $statusListFactory,
        Magento_Catalog_Model_ProductFactory $productFactory,
        Magento_Sales_Model_Quote_PaymentFactory $quotePaymentFactory,
        Magento_Sales_Model_Resource_Quote_Payment_CollectionFactory $quotePaymentCollFactory,
        Magento_Sales_Model_Recurring_ProfileFactory $recurringProfileFactory,
        Magento_Core_Model_Resource_Abstract $resource = null,
        Magento_Data_Collection_Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_eventManager = $eventManager;
        $this->_salesData = $salesData;
        $this->_catalogProduct = $catalogProduct;
        $this->_coreData = $coreData;
        $this->_coreStoreConfig = $coreStoreConfig;
        $this->_storeManager = $storeManager;
        $this->_config = $config;
        $this->_quoteAddressFactory = $quoteAddressFactory;
        $this->_customerFactory = $customerFactory;
        $this->_customerGroupFactory = $customerGroupFactory;
        $this->_quoteItemCollFactory = $quoteItemCollFactory;
        $this->_quoteItemFactory = $quoteItemFactory;
        $this->_message = $message;
        $this->_statusListFactory = $statusListFactory;
        $this->_productFactory = $productFactory;
        $this->_quotePaymentFactory = $quotePaymentFactory;
        $this->_quotePaymentCollFactory = $quotePaymentCollFactory;
        $this->_recurringProfileFactory = $recurringProfileFactory;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Init resource model
     */
    protected function _construct()
    {
        $this->_init('Magento_Sales_Model_Resource_Quote');
    }

    /**
     * Get quote store identifier
     *
     * @return int
     */
    public function getStoreId()
    {
        if (!$this->hasStoreId()) {
            return $this->_storeManager->getStore()->getId();
        }
        return $this->_getData('store_id');
    }

    /**
     * Get quote store model object
     *
     * @return  Magento_Core_Model_Store
     */
    public function getStore()
    {
        return $this->_storeManager->getStore($this->getStoreId());
    }

    /**
     * Declare quote store model
     *
     * @param   Magento_Core_Model_Store $store
     * @return  Magento_Sales_Model_Quote
     */
    public function setStore(Magento_Core_Model_Store $store)
    {
        $this->setStoreId($store->getId());
        return $this;
    }

    /**
     * Get all available store ids for quote
     *
     * @return array
     */
    public function getSharedStoreIds()
    {
        $ids = $this->_getData('shared_store_ids');
        if (is_null($ids) || !is_array($ids)) {
            $website = $this->getWebsite();
            if ($website) {
                return $website->getStoreIds();
            }
            return $this->getStore()->getWebsite()->getStoreIds();
        }
        return $ids;
    }

    /**
     * Prepare data before save
     *
     * @return Magento_Sales_Model_Quote
     */
    protected function _beforeSave()
    {
        /**
         * Currency logic
         *
         * global - currency which is set for default in backend
         * base - currency which is set for current website. all attributes that
         *      have 'base_' prefix saved in this currency
         * quote/order - currency which was selected by customer or configured by
         *      admin for current store. currency in which customer sees
         *      price thought all checkout.
         *
         * Rates:
         *      base_to_global & base_to_quote/base_to_order
         */
        $globalCurrencyCode  = $this->_config->getValue(
            Magento_Directory_Model_Currency::XML_PATH_CURRENCY_BASE,
            'default'
        );
        $baseCurrency = $this->getStore()->getBaseCurrency();

        if ($this->hasForcedCurrency()) {
            $quoteCurrency = $this->getForcedCurrency();
        } else {
            $quoteCurrency = $this->getStore()->getCurrentCurrency();
        }

        $this->setGlobalCurrencyCode($globalCurrencyCode);
        $this->setBaseCurrencyCode($baseCurrency->getCode());
        $this->setStoreCurrencyCode($baseCurrency->getCode());
        $this->setQuoteCurrencyCode($quoteCurrency->getCode());

        $this->setBaseToGlobalRate($baseCurrency->getRate($globalCurrencyCode));
        $this->setBaseToQuoteRate($baseCurrency->getRate($quoteCurrency));

        if (!$this->hasChangedFlag() || $this->getChangedFlag() == true) {
            $this->setIsChanged(1);
        } else {
            $this->setIsChanged(0);
        }

        if ($this->_customer) {
            $this->setCustomerId($this->_customer->getId());
        }

        parent::_beforeSave();
    }

    /**
     * Save related items
     *
     * @return Magento_Sales_Model_Quote
     */
    protected function _afterSave()
    {
        parent::_afterSave();

        if (null !== $this->_addresses) {
            $this->getAddressesCollection()->save();
        }

        if (null !== $this->_items) {
            $this->getItemsCollection()->save();
        }

        if (null !== $this->_payments) {
            $this->getPaymentsCollection()->save();
        }
        return $this;
    }

    /**
     * Loading quote data by customer
     *
     * @param Magento_Customer_Model_Customer|int $customer
     * @return Magento_Sales_Model_Quote
     */
    public function loadByCustomer($customer)
    {
        if ($customer instanceof Magento_Customer_Model_Customer) {
            $customerId = $customer->getId();
        } else {
            $customerId = (int)$customer;
        }
        $this->_getResource()->loadByCustomerId($this, $customerId);
        $this->_afterLoad();
        return $this;
    }

    /**
     * Loading only active quote
     *
     * @param int $quoteId
     * @return Magento_Sales_Model_Quote
     */
    public function loadActive($quoteId)
    {
        $this->_getResource()->loadActive($this, $quoteId);
        $this->_afterLoad();
        return $this;
    }

    /**
     * Loading quote by identifier
     *
     * @param int $quoteId
     * @return Magento_Sales_Model_Quote
     */
    public function loadByIdWithoutStore($quoteId)
    {
        $this->_getResource()->loadByIdWithoutStore($this, $quoteId);
        $this->_afterLoad();
        return $this;
    }

    /**
     * Assign customer model object data to quote
     *
     * @param   Magento_Customer_Model_Customer $customer
     * @return  Magento_Sales_Model_Quote
     */
    public function assignCustomer(Magento_Customer_Model_Customer $customer)
    {
        return $this->assignCustomerWithAddressChange($customer);
    }

    /**
     * Assign customer model to quote with billing and shipping address change
     *
     * @param  Magento_Customer_Model_Customer    $customer
     * @param  Magento_Sales_Model_Quote_Address  $billingAddress
     * @param  Magento_Sales_Model_Quote_Address  $shippingAddress
     * @return Magento_Sales_Model_Quote
     */
    public function assignCustomerWithAddressChange(
        Magento_Customer_Model_Customer    $customer,
        Magento_Sales_Model_Quote_Address  $billingAddress  = null,
        Magento_Sales_Model_Quote_Address  $shippingAddress = null
    ) {
        if ($customer->getId()) {
            $this->setCustomer($customer);

            if (null !== $billingAddress) {
                $this->setBillingAddress($billingAddress);
            } else {
                $defaultBillingAddress = $customer->getDefaultBillingAddress();
                if ($defaultBillingAddress && $defaultBillingAddress->getId()) {
                    $billingAddress = $this->_quoteAddressFactory->create()
                        ->importCustomerAddress($defaultBillingAddress);
                    $this->setBillingAddress($billingAddress);
                }
            }

            if (null === $shippingAddress) {
                $defaultShippingAddress = $customer->getDefaultShippingAddress();
                if ($defaultShippingAddress && $defaultShippingAddress->getId()) {
                    $shippingAddress = $this->_quoteAddressFactory->create()
                        ->importCustomerAddress($defaultShippingAddress);
                } else {
                    $shippingAddress = $this->_quoteAddressFactory->create();
                }
            }
            $this->setShippingAddress($shippingAddress);
        }

        return $this;
    }

    /**
     * Define customer object
     *
     * @param   Magento_Customer_Model_Customer $customer
     * @return  Magento_Sales_Model_Quote
     */
    public function setCustomer(Magento_Customer_Model_Customer $customer)
    {
        $this->_customer = $customer;
        $this->setCustomerId($customer->getId());
        $this->_coreData->copyFieldsetToTarget('customer_account', 'to_quote', $customer, $this);
        return $this;
    }

    /**
     * Retrieve customer model object
     *
     * @return Magento_Customer_Model_Customer
     */
    public function getCustomer()
    {
        if (null === $this->_customer) {
            $this->_customer = $this->_customerFactory->create();
            $customerId = $this->getCustomerId();
            if ($customerId) {
                $this->_customer->load($customerId);
                if (!$this->_customer->getId()) {
                    $this->_customer->setCustomerId(null);
                }
            }
        }
        return $this->_customer;
    }

    /**
     * Retrieve customer group id
     *
     * @return int
     */
    public function getCustomerGroupId()
    {
        if ($this->hasData('customer_group_id')) {
            return $this->getData('customer_group_id');
        } elseif ($this->getCustomerId()) {
            return $this->getCustomer()->getGroupId();
        } else {
            return Magento_Customer_Model_Group::NOT_LOGGED_IN_ID;
        }
    }

    /**
     * @return string
     */
    public function getCustomerTaxClassId()
    {
        /**
         * tax class can vary at any time. so instead of using the value from session,
         * we need to retrieve from db every time to get the correct tax class
         */
        //if (!$this->getData('customer_group_id') && !$this->getData('customer_tax_class_id')) {
        $classId = $this->_customerGroupFactory->create()->getTaxClassId($this->getCustomerGroupId());
        $this->setCustomerTaxClassId($classId);
        //}

        return $this->getData('customer_tax_class_id');
    }

    /**
     * Retrieve quote address collection
     *
     * @return Magento_Eav_Model_Entity_Collection_Abstract
     */
    public function getAddressesCollection()
    {
        if (null === $this->_addresses) {
            $this->_addresses = $this->_quoteAddressFactory->create()->getCollection()
                ->setQuoteFilter($this->getId());

            if ($this->getId()) {
                foreach ($this->_addresses as $address) {
                    $address->setQuote($this);
                }
            }
        }
        return $this->_addresses;
    }

    /**
     * Retrieve quote address by type
     *
     * @param   string $type
     * @return  Magento_Sales_Model_Quote_Address
     */
    protected function _getAddressByType($type)
    {
        foreach ($this->getAddressesCollection() as $address) {
            if ($address->getAddressType() == $type && !$address->isDeleted()) {
                return $address;
            }
        }

        $address = $this->_quoteAddressFactory->create()->setAddressType($type);
        $this->addAddress($address);
        return $address;
    }

    /**
     * Retrieve quote billing address
     *
     * @return Magento_Sales_Model_Quote_Address
     */
    public function getBillingAddress()
    {
        return $this->_getAddressByType(Magento_Sales_Model_Quote_Address::TYPE_BILLING);
    }

    /**
     * Retrieve quote shipping address
     *
     * @return Magento_Sales_Model_Quote_Address
     */
    public function getShippingAddress()
    {
        return $this->_getAddressByType(Magento_Sales_Model_Quote_Address::TYPE_SHIPPING);
    }

    /**
     * @return array
     */
    public function getAllShippingAddresses()
    {
        $addresses = array();
        foreach ($this->getAddressesCollection() as $address) {
            if ($address->getAddressType() == Magento_Sales_Model_Quote_Address::TYPE_SHIPPING
                && !$address->isDeleted()
            ) {
                $addresses[] = $address;
            }
        }
        return $addresses;
    }

    /**
     * @return array
     */
    public function getAllAddresses()
    {
        $addresses = array();
        foreach ($this->getAddressesCollection() as $address) {
            if (!$address->isDeleted()) {
                $addresses[] = $address;
            }
        }
        return $addresses;
    }

    /**
     *
     * @param int $addressId
     * @return Magento_Sales_Model_Quote_Address
     */
    public function getAddressById($addressId)
    {
        foreach ($this->getAddressesCollection() as $address) {
            if ($address->getId() == $addressId) {
                return $address;
            }
        }
        return false;
    }

    /**
     * @param int|string $addressId
     * @return bool
     */
    public function getAddressByCustomerAddressId($addressId)
    {
        foreach ($this->getAddressesCollection() as $address) {
            if (!$address->isDeleted() && $address->getCustomerAddressId() == $addressId) {
                return $address;
            }
        }
        return false;
    }

    /**
     * @param int|string $addressId
     * @return bool
     */
    public function getShippingAddressByCustomerAddressId($addressId)
    {
        foreach ($this->getAddressesCollection() as $address) {
            if (!$address->isDeleted() && $address->getAddressType() == Magento_Sales_Model_Quote_Address::TYPE_SHIPPING
                && $address->getCustomerAddressId() == $addressId
            ) {
                return $address;
            }
        }
        return false;
    }

    /**
     * @param int|string $addressId
     * @return $this
     */
    public function removeAddress($addressId)
    {
        foreach ($this->getAddressesCollection() as $address) {
            if ($address->getId() == $addressId) {
                $address->isDeleted(true);
                break;
            }
        }
        return $this;
    }

    /**
     * Leave no more than one billing and one shipping address, fill them with default data
     *
     * @return Magento_Sales_Model_Quote
     */
    public function removeAllAddresses()
    {
        $addressByType = array();
        $addressesCollection = $this->getAddressesCollection();

        // mark all addresses as deleted
        foreach ($addressesCollection as $address) {
            $type = $address->getAddressType();
            if (!isset($addressByType[$type]) || $addressByType[$type]->getId() > $address->getId()) {
                $addressByType[$type] = $address;
            }
            $address->isDeleted(true);
        }

        // create new billing and shipping addresses filled with default values, set this data to existing records
        foreach ($addressByType as $type => $address) {
            $id = $address->getId();
            $emptyAddress = $this->_getAddressByType($type);
            $address->setData($emptyAddress->getData())->setId($id)->isDeleted(false);
            $emptyAddress->setDeleteImmediately(true);
        }

        // remove newly created billing and shipping addresses from collection to avoid senseless delete queries
        foreach ($addressesCollection as $key => $item) {
            if ($item->getDeleteImmediately()) {
                $addressesCollection->removeItemByKey($key);
            }
        }

        return $this;
    }

    /**
     * @param Magento_Sales_Model_Quote_Address $address
     * @return $this
     */
    public function addAddress(Magento_Sales_Model_Quote_Address $address)
    {
        $address->setQuote($this);
        if (!$address->getId()) {
            $this->getAddressesCollection()->addItem($address);
        }
        return $this;
    }

    /**
     * @param Magento_Sales_Model_Quote_Address $address
     * @return Magento_Sales_Model_Quote
     */
    public function setBillingAddress(Magento_Sales_Model_Quote_Address $address)
    {
        $old = $this->getBillingAddress();

        if (!empty($old)) {
            $old->addData($address->getData());
        } else {
            $this->addAddress($address->setAddressType(Magento_Sales_Model_Quote_Address::TYPE_BILLING));
        }
        return $this;
    }

    /**
     * Set shipping address
     *
     * @param Magento_Sales_Model_Quote_Address $address
     * @return Magento_Sales_Model_Quote
     */
    public function setShippingAddress(Magento_Sales_Model_Quote_Address $address)
    {
        if ($this->getIsMultiShipping()) {
            $this->addAddress($address->setAddressType(Magento_Sales_Model_Quote_Address::TYPE_SHIPPING));
        } else {
            $old = $this->getShippingAddress();
            if (!empty($old)) {
                $old->addData($address->getData());
            } else {
                $this->addAddress($address->setAddressType(Magento_Sales_Model_Quote_Address::TYPE_SHIPPING));
            }
        }
        return $this;
    }

    /**
     * @param Magento_Sales_Model_Quote_Address $address
     * @return $this
     */
    public function addShippingAddress(Magento_Sales_Model_Quote_Address $address)
    {
        $this->setShippingAddress($address);
        return $this;
    }

    /**
     * Retrieve quote items collection
     *
     * @param bool $useCache
     * @return  Magento_Eav_Model_Entity_Collection_Abstract
     */
    public function getItemsCollection($useCache = true)
    {
        if ($this->hasItemsCollection()) {
            return $this->getData('items_collection');
        }
        if (null === $this->_items) {
            $this->_items = $this->_quoteItemCollFactory->create();
            $this->_items->setQuote($this);
        }
        return $this->_items;
    }

    /**
     * Retrieve quote items array
     *
     * @return array
     */
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

    /**
     * Get array of all items what can be display directly
     *
     * @return array
     */
    public function getAllVisibleItems()
    {
        $items = array();
        foreach ($this->getItemsCollection() as $item) {
            if (!$item->isDeleted() && !$item->getParentItemId()) {
                $items[] =  $item;
            }
        }
        return $items;
    }

    /**
     * Checking items availability
     *
     * @return bool
     */
    public function hasItems()
    {
        return sizeof($this->getAllItems()) > 0;
    }

    /**
     * Checking availability of items with decimal qty
     *
     * @return bool
     */
    public function hasItemsWithDecimalQty()
    {
        foreach ($this->getAllItems() as $item) {
            if ($item->getProduct()->getStockItem()
                && $item->getProduct()->getStockItem()->getIsQtyDecimal()
            ) {
                return true;
            }
        }
        return false;
    }

    /**
     * Checking product exist in Quote
     *
     * @param int $productId
     * @return bool
     */
    public function hasProductId($productId)
    {
        foreach ($this->getAllItems() as $item) {
            if ($item->getProductId() == $productId) {
                return true;
            }
        }

        return false;
    }

    /**
     * Retrieve item model object by item identifier
     *
     * @param   int $itemId
     * @return  Magento_Sales_Model_Quote_Item
     */
    public function getItemById($itemId)
    {
        return $this->getItemsCollection()->getItemById($itemId);
    }

    /**
     * Delete quote item. If it does not have identifier then it will be only removed from collection
     *
     * @param   Magento_Sales_Model_Quote_Item $item
     * @return  Magento_Sales_Model_Quote
     */
    public function deleteItem(Magento_Sales_Model_Quote_Item $item)
    {
        if ($item->getId()) {
            $this->removeItem($item->getId());
        } else {
            $quoteItems = $this->getItemsCollection();
            $items = array($item);
            if ($item->getHasChildren()) {
                foreach ($item->getChildren() as $child) {
                    $items[] = $child;
                }
            }
            foreach ($quoteItems as $key => $quoteItem) {
                foreach ($items as $item) {
                    if ($quoteItem->compare($item)) {
                        $quoteItems->removeItemByKey($key);
                    }
                }
            }
        }

        return $this;
    }

    /**
     * Remove quote item by item identifier
     *
     * @param   int $itemId
     * @return  Magento_Sales_Model_Quote
     */
    public function removeItem($itemId)
    {
        $item = $this->getItemById($itemId);

        if ($item) {
            $item->setQuote($this);
            /**
             * If we remove item from quote - we can't use multishipping mode
             */
            $this->setIsMultiShipping(false);
            $item->isDeleted(true);
            if ($item->getHasChildren()) {
                foreach ($item->getChildren() as $child) {
                    $child->isDeleted(true);
                }
            }

            $parent = $item->getParentItem();
            if ($parent) {
                $parent->isDeleted(true);
            }

            $this->_eventManager->dispatch('sales_quote_remove_item', array('quote_item' => $item));
        }

        return $this;
    }

    /**
     * Mark all quote items as deleted (empty quote)
     *
     * @return Magento_Sales_Model_Quote
     */
    public function removeAllItems()
    {
        foreach ($this->getItemsCollection() as $itemId => $item) {
            if (is_null($item->getId())) {
                $this->getItemsCollection()->removeItemByKey($itemId);
            } else {
                $item->isDeleted(true);
            }
        }
        return $this;
    }

    /**
     * Adding new item to quote
     *
     * @param   Magento_Sales_Model_Quote_Item $item
     * @return  Magento_Sales_Model_Quote
     * @throws Magento_Core_Exception
     */
    public function addItem(Magento_Sales_Model_Quote_Item $item)
    {
        /**
         * Temporary workaround for purchase process: it is too dangerous to purchase more than one nominal item
         * or a mixture of nominal and non-nominal items, although technically possible.
         *
         * The problem is that currently it is implemented as sequential submission of nominal items and order,
         * by one click. It makes logically impossible to make the process of the purchase failsafe.
         * Proper solution is to submit items one by one with customer confirmation each time.
         */
        if ($item->isNominal() && $this->hasItems() || $this->hasNominalItems()) {
            throw new Magento_Core_Exception(
                __('Sorry, but items with payment agreements must be ordered one at a time To continue, please remove or buy the other items in your cart, then order this item by itself.')
            );
        }

        $item->setQuote($this);
        if (!$item->getId()) {
            $this->getItemsCollection()->addItem($item);
            $this->_eventManager->dispatch('sales_quote_add_item', array('quote_item' => $item));
        }
        return $this;
    }

    /**
     * Advanced func to add product to quote - processing mode can be specified there.
     * Returns error message if product type instance can't prepare product.
     *
     * @param mixed $product
     * @param null|float|Magento_Object $request
     * @param null|string $processMode
     * @return Magento_Sales_Model_Quote_Item|string
     * @throws Magento_Core_Exception
     */
    public function addProductAdvanced(Magento_Catalog_Model_Product $product, $request = null, $processMode = null)
    {
        if ($request === null) {
            $request = 1;
        }
        if (is_numeric($request)) {
            $request = new Magento_Object(array('qty'=>$request));
        }
        if (!($request instanceof Magento_Object)) {
            throw new Magento_Core_Exception(__('We found an invalid request for adding product to quote.'));
        }

        $cartCandidates = $product->getTypeInstance()->prepareForCartAdvanced($request, $product, $processMode);

        /**
         * Error message
         */
        if (is_string($cartCandidates)) {
            return $cartCandidates;
        }

        /**
         * If prepare process return one object
         */
        if (!is_array($cartCandidates)) {
            $cartCandidates = array($cartCandidates);
        }

        $parentItem = null;
        $errors = array();
        $items = array();
        foreach ($cartCandidates as $candidate) {
            // Child items can be sticked together only within their parent
            $stickWithinParent = $candidate->getParentProductId() ? $parentItem : null;
            $candidate->setStickWithinParent($stickWithinParent);
            $item = $this->_addCatalogProduct($candidate, $candidate->getCartQty());
            if ($request->getResetCount() && !$stickWithinParent && $item->getId() === $request->getId()) {
                $item->setData('qty', 0);
            }
            $items[] = $item;

            /**
             * As parent item we should always use the item of first added product
             */
            if (!$parentItem) {
                $parentItem = $item;
            }
            if ($parentItem && $candidate->getParentProductId()) {
                $item->setParentItem($parentItem);
            }

            /**
             * We specify qty after we know about parent (for stock)
             */
            $item->addQty($candidate->getCartQty());

            // collect errors instead of throwing first one
            if ($item->getHasError()) {
                $message = $item->getMessage();
                if (!in_array($message, $errors)) { // filter duplicate messages
                    $errors[] = $message;
                }
            }
        }
        if (!empty($errors)) {
            throw new Magento_Core_Exception(implode("\n", $errors));
        }

        $this->_eventManager->dispatch('sales_quote_product_add_after', array('items' => $items));

        return $item;
    }


    /**
     * Add product to quote
     *
     * return error message if product type instance can't prepare product
     *
     * @param mixed $product
     * @param null|float|Magento_Object $request
     * @return Magento_Sales_Model_Quote_Item|string
     */
    public function addProduct(Magento_Catalog_Model_Product $product, $request = null)
    {
        return $this->addProductAdvanced(
            $product,
            $request,
            Magento_Catalog_Model_Product_Type_Abstract::PROCESS_MODE_FULL
        );
    }

    /**
     * Adding catalog product object data to quote
     *
     * @param Magento_Catalog_Model_Product $product
     * @param int $qty
     * @return Magento_Sales_Model_Quote_Item
     */
    protected function _addCatalogProduct(Magento_Catalog_Model_Product $product, $qty = 1)
    {
        $newItem = false;
        $item = $this->getItemByProduct($product);
        if (!$item) {
            $item = $this->_quoteItemFactory->create();
            $item->setQuote($this);
            if ($this->_storeManager->getStore()->isAdmin()) {
                $item->setStoreId($this->getStore()->getId());
            } else {
                $item->setStoreId($this->_storeManager->getStore()->getId());
            }
            $newItem = true;
        }

        /**
         * We can't modify existing child items
         */
        if ($item->getId() && $product->getParentProductId()) {
            return $item;
        }

        $item->setOptions($product->getCustomOptions())->setProduct($product);

        // Add only item that is not in quote already (there can be other new or already saved item
        if ($newItem) {
            $this->addItem($item);
        }

        return $item;
    }

    /**
     * Updates quote item with new configuration
     *
     * $params sets how current item configuration must be taken into account and additional options.
     * It's passed to Magento_Catalog_Helper_Product->addParamsToBuyRequest() to compose resulting buyRequest.
     *
     * Basically it can hold
     * - 'current_config', Magento_Object or array - current buyRequest that configures product in this item,
     *   used to restore currently attached files
     * - 'files_prefix': string[a-z0-9_] - prefix that was added at frontend to names of file options (file inputs),
     *   so they won't intersect with other submitted options
     *
     * For more options see Magento_Catalog_Helper_Product->addParamsToBuyRequest()
     *
     * @param int $itemId
     * @param Magento_Object $buyRequest
     * @param null|array|Magento_Object $params
     * @return Magento_Sales_Model_Quote_Item
     * @throws Magento_Core_Exception
     *
     * @see Magento_Catalog_Helper_Product::addParamsToBuyRequest()
     */
    public function updateItem($itemId, $buyRequest, $params = null)
    {
        $item = $this->getItemById($itemId);
        if (!$item) {
            throw new Magento_Core_Exception(__('This is the wrong quote item id to update configuration.'));
        }
        $productId = $item->getProduct()->getId();

        //We need to create new clear product instance with same $productId
        //to set new option values from $buyRequest
        $product = $this->_productFactory->create()
            ->setStoreId($this->getStore()->getId())
            ->load($productId);

        if (!$params) {
            $params = new Magento_Object();
        } elseif (is_array($params)) {
            $params = new Magento_Object($params);
        }
        $params->setCurrentConfig($item->getBuyRequest());
        $buyRequest = $this->_catalogProduct->addParamsToBuyRequest($buyRequest, $params);

        $buyRequest->setResetCount(true);
        $resultItem = $this->addProduct($product, $buyRequest);

        if (is_string($resultItem)) {
            throw new Magento_Core_Exception($resultItem);
        }

        if ($resultItem->getParentItem()) {
            $resultItem = $resultItem->getParentItem();
        }

        if ($resultItem->getId() != $itemId) {
            /**
             * Product configuration didn't stick to original quote item
             * It either has same configuration as some other quote item's product or completely new configuration
             */
            $this->removeItem($itemId);
            $items = $this->getAllItems();
            foreach ($items as $item) {
                if (($item->getProductId() == $productId) && ($item->getId() != $resultItem->getId())) {
                    if ($resultItem->compare($item)) {
                        // Product configuration is same as in other quote item
                        $resultItem->setQty($resultItem->getQty() + $item->getQty());
                        $this->removeItem($item->getId());
                        break;
                    }
                }
            }
        } else {
            $resultItem->setQty($buyRequest->getQty());
        }

        return $resultItem;
    }

    /**
     * Retrieve quote item by product id
     *
     * @param   Magento_Catalog_Model_Product $product
     * @return  Magento_Sales_Model_Quote_Item|bool
     */
    public function getItemByProduct($product)
    {
        foreach ($this->getAllItems() as $item) {
            if ($item->representProduct($product)) {
                return $item;
            }
        }
        return false;
    }

    /**
     * @return int
     */
    public function getItemsSummaryQty()
    {
        $qty = $this->getData('all_items_qty');
        if (null === $qty) {
            $qty = 0;
            foreach ($this->getAllItems() as $item) {
                if ($item->getParentItem()) {
                    continue;
                }

                $children = $item->getChildren();
                if ($children && $item->isShipSeparately()) {
                    foreach ($children as $child) {
                        $qty += $child->getQty()*$item->getQty();
                    }
                } else {
                    $qty += $item->getQty();
                }
            }
            $this->setData('all_items_qty', $qty);
        }
        return $qty;
    }

    /**
     * @return int
     */
    public function getItemVirtualQty()
    {
        $qty = $this->getData('virtual_items_qty');
        if (null === $qty) {
            $qty = 0;
            foreach ($this->getAllItems() as $item) {
                if ($item->getParentItem()) {
                    continue;
                }

                $children = $item->getChildren();
                if ($children && $item->isShipSeparately()) {
                    foreach ($children as $child) {
                        if ($child->getProduct()->getIsVirtual()) {
                            $qty += $child->getQty();
                        }
                    }
                } else {
                    if ($item->getProduct()->getIsVirtual()) {
                        $qty += $item->getQty();
                    }
                }
            }
            $this->setData('virtual_items_qty', $qty);
        }
        return $qty;
    }

    /*********************** PAYMENTS ***************************/
    /**
     * @return Magento_Eav_Model_Entity_Collection_Abstract
     */
    public function getPaymentsCollection()
    {
        if (null === $this->_payments) {
            $this->_payments = $this->_quotePaymentCollFactory->create()
                ->setQuoteFilter($this->getId());

            if ($this->getId()) {
                foreach ($this->_payments as $payment) {
                    $payment->setQuote($this);
                }
            }
        }
        return $this->_payments;
    }

    /**
     * @return Magento_Sales_Model_Quote_Payment
     */
    public function getPayment()
    {
        foreach ($this->getPaymentsCollection() as $payment) {
            if (!$payment->isDeleted()) {
                return $payment;
            }
        }
        $payment = $this->_quotePaymentFactory->create();
        $this->addPayment($payment);
        return $payment;
    }

    /**
     * @param string $paymentId
     * @return bool
     */
    public function getPaymentById($paymentId)
    {
        foreach ($this->getPaymentsCollection() as $payment) {
            if ($payment->getId() == $paymentId) {
                return $payment;
            }
        }
        return false;
    }

    /**
     * @param Magento_Sales_Model_Quote_Payment $payment
     * @return $this
     */
    public function addPayment(Magento_Sales_Model_Quote_Payment $payment)
    {
        $payment->setQuote($this);
        if (!$payment->getId()) {
            $this->getPaymentsCollection()->addItem($payment);
        }
        return $this;
    }

    /**
     * @param Magento_Sales_Model_Quote_Payment $payment
     * @return Magento_Sales_Model_Quote_Payment
     */
    public function setPayment(Magento_Sales_Model_Quote_Payment $payment)
    {
        if (!$this->getIsMultiPayment() && ($old = $this->getPayment())) {
            $payment->setId($old->getId());
        }
        $this->addPayment($payment);

        return $payment;
    }

    /**
     * @return $this
     */
    public function removePayment()
    {
        $this->getPayment()->isDeleted(true);
        return $this;
    }

    /**
     * Collect totals
     *
     * @return Magento_Sales_Model_Quote
     */
    public function collectTotals()
    {
        /**
         * Protect double totals collection
         */
        if ($this->getTotalsCollectedFlag()) {
            return $this;
        }
        $this->_eventManager->dispatch($this->_eventPrefix . '_collect_totals_before', array($this->_eventObject => $this));

        $this->_collectItemsQtys();

        $this->setSubtotal(0);
        $this->setBaseSubtotal(0);

        $this->setSubtotalWithDiscount(0);
        $this->setBaseSubtotalWithDiscount(0);

        $this->setGrandTotal(0);
        $this->setBaseGrandTotal(0);

        foreach ($this->getAllAddresses() as $address) {
            $address->setSubtotal(0);
            $address->setBaseSubtotal(0);

            $address->setGrandTotal(0);
            $address->setBaseGrandTotal(0);

            $address->collectTotals();

            $this->setSubtotal((float) $this->getSubtotal() + $address->getSubtotal());
            $this->setBaseSubtotal((float) $this->getBaseSubtotal() + $address->getBaseSubtotal());

            $this->setSubtotalWithDiscount(
                (float)$this->getSubtotalWithDiscount() + $address->getSubtotalWithDiscount()
            );
            $this->setBaseSubtotalWithDiscount(
                (float)$this->getBaseSubtotalWithDiscount() + $address->getBaseSubtotalWithDiscount()
            );

            $this->setGrandTotal((float)$this->getGrandTotal() + $address->getGrandTotal());
            $this->setBaseGrandTotal((float)$this->getBaseGrandTotal() + $address->getBaseGrandTotal());
        }

        $this->_salesData->checkQuoteAmount($this, $this->getGrandTotal());
        $this->_salesData->checkQuoteAmount($this, $this->getBaseGrandTotal());

        $this->setData('trigger_recollect', 0);
        $this->_validateCouponCode();

        $this->_eventManager->dispatch($this->_eventPrefix . '_collect_totals_after', array($this->_eventObject => $this));

        $this->setTotalsCollectedFlag(true);
        return $this;
    }

    /**
     * Collect items qty
     *
     * @return Magento_Sales_Model_Quote
     */
    protected function _collectItemsQtys()
    {
        $this->setItemsCount(0);
        $this->setItemsQty(0);
        $this->setVirtualItemsQty(0);

        foreach ($this->getAllVisibleItems() as $item) {
            if ($item->getParentItem()) {
                continue;
            }

            $children = $item->getChildren();
            if ($children && $item->isShipSeparately()) {
                foreach ($children as $child) {
                    if ($child->getProduct()->getIsVirtual()) {
                        $this->setVirtualItemsQty($this->getVirtualItemsQty() + $child->getQty() * $item->getQty());
                    }
                }
            }

            if ($item->getProduct()->getIsVirtual()) {
                $this->setVirtualItemsQty($this->getVirtualItemsQty() + $item->getQty());
            }
            $this->setItemsCount($this->getItemsCount() + 1);
            $this->setItemsQty((float)$this->getItemsQty() + $item->getQty());
        }

        return $this;
    }

    /**
     * Get all quote totals (sorted by priority)
     * Method process quote states isVirtual and isMultiShipping
     *
     * @return array
     */
    public function getTotals()
    {
        /**
         * If quote is virtual we are using totals of billing address because
         * all items assigned to it
         */
        if ($this->isVirtual()) {
            return $this->getBillingAddress()->getTotals();
        }

        $shippingAddress = $this->getShippingAddress();
        $totals = $shippingAddress->getTotals();
        // Going through all quote addresses and merge their totals
        foreach ($this->getAddressesCollection() as $address) {
            if ($address->isDeleted() || $address === $shippingAddress) {
                continue;
            }
            foreach ($address->getTotals() as $code => $total) {
                if (isset($totals[$code])) {
                    $totals[$code]->merge($total);
                } else {
                    $totals[$code] = $total;
                }
            }
        }

        $sortedTotals = array();
        foreach ($this->getBillingAddress()->getTotalCollector()->getRetrievers() as $total) {
            /* @var $total Magento_Sales_Model_Quote_Address_Total_Abstract */
            if (isset($totals[$total->getCode()])) {
                $sortedTotals[$total->getCode()] = $totals[$total->getCode()];
            }
        }
        return $sortedTotals;
    }

    /**
     * @param string $message
     * @param string $index
     * @return $this
     */
    public function addMessage($message, $index = 'error')
    {
        $messages = $this->getData('messages');
        if (null === $messages) {
            $messages = array();
        }

        if (isset($messages[$index])) {
            return $this;
        }

        if (is_string($message)) {
            $message = $this->_message->error($message);
        }

        $messages[$index] = $message;
        $this->setData('messages', $messages);
        return $this;
    }

    /**
     * Retrieve current quote messages
     *
     * @return array
     */
    public function getMessages()
    {
        $messages = $this->getData('messages');
        if (null === $messages) {
            $messages = array();
            $this->setData('messages', $messages);
        }
        return $messages;
    }

    /**
     * Retrieve current quote errors
     *
     * @return array
     */
    public function getErrors()
    {
        $errors = array();
        foreach ($this->getMessages() as $message) {
            /* @var $error Magento_Core_Model_Message_Abstract */
            if ($message->getType() == Magento_Core_Model_Message::ERROR) {
                array_push($errors, $message);
            }
        }
        return $errors;
    }

    /**
     * Sets flag, whether this quote has some error associated with it.
     *
     * @param bool $flag
     * @return Magento_Sales_Model_Quote
     */
    protected function _setHasError($flag)
    {
        return $this->setData('has_error', $flag);
    }

    /**
     * Sets flag, whether this quote has some error associated with it.
     * When TRUE - also adds 'unknown' error information to list of quote errors.
     * When FALSE - clears whole list of quote errors.
     * It's recommended to use addErrorInfo() instead - to be able to remove error statuses later.
     *
     * @param bool $flag
     * @return Magento_Sales_Model_Quote
     * @see addErrorInfo()
     */
    public function setHasError($flag)
    {
        if ($flag) {
            $this->addErrorInfo();
        } else {
            $this->_clearErrorInfo();
        }
        return $this;
    }

    /**
     * Clears list of errors, associated with this quote.
     * Also automatically removes error-flag from oneself.
     *
     * @return Magento_Sales_Model_Quote
     */
    protected function _clearErrorInfo()
    {
        $this->_errorInfoGroups = array();
        $this->_setHasError(false);
        return $this;
    }

    /**
     * Adds error information to the quote.
     * Automatically sets error flag.
     *
     * @param string $type An internal error type ('error', 'qty', etc.), passed then to adding messages routine
     * @param string|null $origin Usually a name of module, that embeds error
     * @param int|null $code Error code, unique for origin, that sets it
     * @param string|null $message Error message
     * @param Magento_Object|null $additionalData Any additional data, that caller would like to store
     * @return Magento_Sales_Model_Quote
     */
    public function addErrorInfo($type = 'error', $origin = null, $code = null, $message = null, $additionalData = null)
    {
        if (!isset($this->_errorInfoGroups[$type])) {
            $this->_errorInfoGroups[$type] = $this->_statusListFactory->create();
        }

        $this->_errorInfoGroups[$type]->addItem($origin, $code, $message, $additionalData);

        if ($message !== null) {
            $this->addMessage($message, $type);
        }
        $this->_setHasError(true);

        return $this;
    }

    /**
     * Removes error infos, that have parameters equal to passed in $params.
     * $params can have following keys (if not set - then any item is good for this key):
     *   'origin', 'code', 'message'
     *
     * @param string $type An internal error type ('error', 'qty', etc.), passed then to adding messages routine
     * @param array $params
     * @return Magento_Sales_Model_Quote
     */
    public function removeErrorInfosByParams($type = 'error', $params)
    {
        if ($type && !isset($this->_errorInfoGroups[$type])) {
            return $this;
        }

        $errorLists = array();
        if ($type) {
            $errorLists[] = $this->_errorInfoGroups[$type];
        } else {
            $errorLists = $this->_errorInfoGroups;
        }

        foreach ($errorLists as $type => $errorList) {
            $removedItems = $errorList->removeItemsByParams($params);
            foreach ($removedItems as $item) {
                if ($item['message'] !== null) {
                    $this->removeMessageByText($type, $item['message']);
                }
            }
        }

        $errorsExist = false;
        foreach ($this->_errorInfoGroups as $errorListCheck) {
            if ($errorListCheck->getItems()) {
                $errorsExist = true;
                break;
            }
        }
        if (!$errorsExist) {
            $this->_setHasError(false);
        }

        return $this;
    }

    /**
     * Removes message by text
     *
     * @param string $type
     * @param string $text
     * @return Magento_Sales_Model_Quote
     */
    public function removeMessageByText($type = 'error', $text)
    {
        $messages = $this->getData('messages');
        if (null === $messages) {
            $messages = array();
        }

        if (!isset($messages[$type])) {
            return $this;
        }

        $message = $messages[$type];
        if ($message instanceof Magento_Core_Model_Message_Abstract) {
            $message = $message->getText();
        } elseif (!is_string($message)) {
            return $this;
        }
        if ($message == $text) {
            unset($messages[$type]);
            $this->setData('messages', $messages);
        }
        return $this;
    }

    /**
     * Generate new increment order id and associate it with current quote
     *
     * @return Magento_Sales_Model_Quote
     */
    public function reserveOrderId()
    {
        if (!$this->getReservedOrderId()) {
            $this->setReservedOrderId($this->_getResource()->getReservedOrderId($this));
        } else {
            //checking if reserved order id was already used for some order
            //if yes reserving new one if not using old one
            if ($this->_getResource()->isOrderIncrementIdUsed($this->getReservedOrderId())) {
                $this->setReservedOrderId($this->_getResource()->getReservedOrderId($this));
            }
        }
        return $this;
    }

    /**
     * @param bool $multishipping
     * @return bool
     */
    public function validateMinimumAmount($multishipping = false)
    {
        $storeId = $this->getStoreId();
        $minOrderActive = $this->_coreStoreConfig->getConfigFlag('sales/minimum_order/active', $storeId);
        $minOrderMulti  = $this->_coreStoreConfig->getConfigFlag('sales/minimum_order/multi_address', $storeId);
        $minAmount      = $this->_coreStoreConfig->getConfig('sales/minimum_order/amount', $storeId);

        if (!$minOrderActive) {
            return true;
        }

        $addresses = $this->getAllAddresses();

        if ($multishipping) {
            if ($minOrderMulti) {
                foreach ($addresses as $address) {
                    foreach ($address->getQuote()->getItemsCollection() as $item) {
                        $amount = $item->getBaseRowTotal() - $item->getBaseDiscountAmount();
                        if ($amount < $minAmount) {
                            return false;
                        }
                    }
                }
            } else {
                $baseTotal = 0;
                foreach ($addresses as $address) {
                    /* @var $address Magento_Sales_Model_Quote_Address */
                    $baseTotal += $address->getBaseSubtotalWithDiscount();
                }
                if ($baseTotal < $minAmount) {
                    return false;
                }
            }
        } else {
            foreach ($addresses as $address) {
                /* @var $address Magento_Sales_Model_Quote_Address */
                if (!$address->validateMinimumAmount()) {
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * Check quote for virtual product only
     *
     * @return bool
     */
    public function isVirtual()
    {
        $isVirtual = true;
        $countItems = 0;
        foreach ($this->getItemsCollection() as $_item) {
            /* @var $_item Magento_Sales_Model_Quote_Item */
            if ($_item->isDeleted() || $_item->getParentItemId()) {
                continue;
            }
            $countItems ++;
            if (!$_item->getProduct()->getIsVirtual()) {
                $isVirtual = false;
                break;
            }
        }
        return $countItems == 0 ? false : $isVirtual;
    }

    /**
     * Check quote for virtual product only
     *
     * @return bool
     */
    public function getIsVirtual()
    {
        return intval($this->isVirtual());
    }

    /**
     * Has a virtual products on quote
     *
     * @return bool
     */
    public function hasVirtualItems()
    {
        $hasVirtual = false;
        foreach ($this->getItemsCollection() as $item) {
            if ($item->getParentItemId()) {
                continue;
            }
            if ($item->getProduct()->isVirtual()) {
                $hasVirtual = true;
            }
        }
        return $hasVirtual;
    }

    /**
     * Merge quotes
     *
     * @param   Magento_Sales_Model_Quote $quote
     * @return  Magento_Sales_Model_Quote
     */
    public function merge(Magento_Sales_Model_Quote $quote)
    {
        $this->_eventManager->dispatch($this->_eventPrefix . '_merge_before', array(
            $this->_eventObject => $this,
            'source' => $quote
        ));

        foreach ($quote->getAllVisibleItems() as $item) {
            $found = false;
            foreach ($this->getAllItems() as $quoteItem) {
                if ($quoteItem->compare($item)) {
                    $quoteItem->setQty($quoteItem->getQty() + $item->getQty());
                    $found = true;
                    break;
                }
            }

            if (!$found) {
                $newItem = clone $item;
                $this->addItem($newItem);
                if ($item->getHasChildren()) {
                    foreach ($item->getChildren() as $child) {
                        $newChild = clone $child;
                        $newChild->setParentItem($newItem);
                        $this->addItem($newChild);
                    }
                }
            }
        }

        /**
         * Init shipping and billing address if quote is new
         */
        if (!$this->getId()) {
            $this->getShippingAddress();
            $this->getBillingAddress();
        }

        if ($quote->getCouponCode()) {
            $this->setCouponCode($quote->getCouponCode());
        }

        $this->_eventManager->dispatch($this->_eventPrefix . '_merge_after', array(
            $this->_eventObject => $this,
            'source' => $quote
        ));

        return $this;
    }

    /**
     * Whether there are recurring items
     *
     * @return bool
     */
    public function hasRecurringItems()
    {
        foreach ($this->getAllVisibleItems() as $item) {
            if ($item->getProduct() && $item->getProduct()->isRecurring()) {
                return true;
            }
        }
        return false;
    }

    /**
     * Getter whether quote has nominal items
     * Can bypass treating virtual items as nominal
     *
     * @param bool $countVirtual
     * @return bool
     */
    public function hasNominalItems($countVirtual = true)
    {
        foreach ($this->getAllVisibleItems() as $item) {
            if ($item->isNominal()) {
                if ((!$countVirtual) && $item->getProduct()->isVirtual()) {
                    continue;
                }
                return true;
            }
        }
        return false;
    }

    /**
     * Whether quote has nominal items only
     *
     * @return bool
     */
    public function isNominal()
    {
        foreach ($this->getAllVisibleItems() as $item) {
            if (!$item->isNominal()) {
                return false;
            }
        }
        return true;
    }

    /**
     * Create recurring payment profiles basing on the current items
     *
     * @return array
     * @throws Exception
     */
    public function prepareRecurringPaymentProfiles()
    {
        if (!$this->getTotalsCollectedFlag()) {
            // Whoops! Make sure nominal totals must be calculated here.
            throw new Exception('Quote totals must be collected before this operation.');
        }

        $result = array();
        foreach ($this->getAllVisibleItems() as $item) {
            $product = $item->getProduct();
            if (is_object($product) && ($product->isRecurring())
                && $profile = $this->_recurringProfileFactory->create()->importProduct($product)
            ) {
                $profile->importQuote($this);
                $profile->importQuoteItem($item);
                $result[] = $profile;
            }
        }
        return $result;
    }

    /**
     * @return $this
     */
    protected function _validateCouponCode()
    {
        $code = $this->_getData('coupon_code');
        if (strlen($code)) {
            $addressHasCoupon = false;
            $addresses = $this->getAllAddresses();
            if (count($addresses) > 0) {
                foreach ($addresses as $address) {
                    if ($address->hasCouponCode()) {
                        $addressHasCoupon = true;
                    }
                }
                if (!$addressHasCoupon) {
                    $this->setCouponCode('');
                }
            }
        }
        return $this;
    }

    /**
     * Trigger collect totals after loading, if required
     *
     * @return Magento_Sales_Model_Quote
     */
    protected function _afterLoad()
    {
        // collect totals and save me, if required
        if (1 == $this->getData('trigger_recollect')) {
            $this->collectTotals()->save();
        }
        return parent::_afterLoad();
    }

    /**
     * Return quote checkout method code
     *
     * @param boolean $originalMethod if true return defined method from beginning
     * @return string
     */
    public function getCheckoutMethod($originalMethod = false)
    {
        if ($this->getCustomerId() && !$originalMethod) {
            return self::CHECKOUT_METHOD_LOGIN_IN;
        }
        return $this->_getData('checkout_method');
    }

    /**
     * Prevent quote from saving
     *
     * @return Magento_Sales_Model_Quote
     */
    public function preventSaving()
    {
        $this->_preventSaving = true;
        return $this;
    }

    /**
     * Save quote with prevention checking
     *
     * @return Magento_Sales_Model_Quote
     */
    public function save()
    {
        if ($this->_preventSaving) {
            return $this;
        }
        return parent::save();
    }
}
