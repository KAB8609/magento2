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
 * Sales Quote address model
 *
 * @method Magento_Sales_Model_Resource_Quote_Address _getResource()
 * @method Magento_Sales_Model_Resource_Quote_Address getResource()
 * @method int getQuoteId()
 * @method Magento_Sales_Model_Quote_Address setQuoteId(int $value)
 * @method string getCreatedAt()
 * @method Magento_Sales_Model_Quote_Address setCreatedAt(string $value)
 * @method string getUpdatedAt()
 * @method Magento_Sales_Model_Quote_Address setUpdatedAt(string $value)
 * @method int getCustomerId()
 * @method Magento_Sales_Model_Quote_Address setCustomerId(int $value)
 * @method int getSaveInAddressBook()
 * @method Magento_Sales_Model_Quote_Address setSaveInAddressBook(int $value)
 * @method int getCustomerAddressId()
 * @method Magento_Sales_Model_Quote_Address setCustomerAddressId(int $value)
 * @method string getAddressType()
 * @method Magento_Sales_Model_Quote_Address setAddressType(string $value)
 * @method string getEmail()
 * @method Magento_Sales_Model_Quote_Address setEmail(string $value)
 * @method string getPrefix()
 * @method Magento_Sales_Model_Quote_Address setPrefix(string $value)
 * @method string getFirstname()
 * @method Magento_Sales_Model_Quote_Address setFirstname(string $value)
 * @method string getMiddlename()
 * @method Magento_Sales_Model_Quote_Address setMiddlename(string $value)
 * @method string getLastname()
 * @method Magento_Sales_Model_Quote_Address setLastname(string $value)
 * @method string getSuffix()
 * @method Magento_Sales_Model_Quote_Address setSuffix(string $value)
 * @method string getCompany()
 * @method Magento_Sales_Model_Quote_Address setCompany(string $value)
 * @method string getCity()
 * @method Magento_Sales_Model_Quote_Address setCity(string $value)
 * @method Magento_Sales_Model_Quote_Address setRegion(string $value)
 * @method Magento_Sales_Model_Quote_Address setRegionId(int $value)
 * @method string getPostcode()
 * @method Magento_Sales_Model_Quote_Address setPostcode(string $value)
 * @method string getCountryId()
 * @method Magento_Sales_Model_Quote_Address setCountryId(string $value)
 * @method string getTelephone()
 * @method Magento_Sales_Model_Quote_Address setTelephone(string $value)
 * @method string getFax()
 * @method Magento_Sales_Model_Quote_Address setFax(string $value)
 * @method int getSameAsBilling()
 * @method Magento_Sales_Model_Quote_Address setSameAsBilling(int $value)
 * @method int getFreeShipping()
 * @method Magento_Sales_Model_Quote_Address setFreeShipping(int $value)
 * @method int getCollectShippingRates()
 * @method Magento_Sales_Model_Quote_Address setCollectShippingRates(int $value)
 * @method string getShippingMethod()
 * @method Magento_Sales_Model_Quote_Address setShippingMethod(string $value)
 * @method string getShippingDescription()
 * @method Magento_Sales_Model_Quote_Address setShippingDescription(string $value)
 * @method float getWeight()
 * @method Magento_Sales_Model_Quote_Address setWeight(float $value)
 * @method float getSubtotal()
 * @method Magento_Sales_Model_Quote_Address setSubtotal(float $value)
 * @method float getBaseSubtotal()
 * @method Magento_Sales_Model_Quote_Address setBaseSubtotal(float $value)
 * @method Magento_Sales_Model_Quote_Address setSubtotalWithDiscount(float $value)
 * @method Magento_Sales_Model_Quote_Address setBaseSubtotalWithDiscount(float $value)
 * @method float getTaxAmount()
 * @method Magento_Sales_Model_Quote_Address setTaxAmount(float $value)
 * @method float getBaseTaxAmount()
 * @method Magento_Sales_Model_Quote_Address setBaseTaxAmount(float $value)
 * @method float getShippingAmount()
 * @method float getBaseShippingAmount()
 * @method float getShippingTaxAmount()
 * @method Magento_Sales_Model_Quote_Address setShippingTaxAmount(float $value)
 * @method float getBaseShippingTaxAmount()
 * @method Magento_Sales_Model_Quote_Address setBaseShippingTaxAmount(float $value)
 * @method float getDiscountAmount()
 * @method Magento_Sales_Model_Quote_Address setDiscountAmount(float $value)
 * @method float getBaseDiscountAmount()
 * @method Magento_Sales_Model_Quote_Address setBaseDiscountAmount(float $value)
 * @method float getGrandTotal()
 * @method Magento_Sales_Model_Quote_Address setGrandTotal(float $value)
 * @method float getBaseGrandTotal()
 * @method Magento_Sales_Model_Quote_Address setBaseGrandTotal(float $value)
 * @method string getCustomerNotes()
 * @method Magento_Sales_Model_Quote_Address setCustomerNotes(string $value)
 * @method string getDiscountDescription()
 * @method Magento_Sales_Model_Quote_Address setDiscountDescription(string $value)
 * @method null|array getDiscountDescriptionArray()
 * @method Magento_Sales_Model_Quote_Address setDiscountDescriptionArray(array $value)
 * @method float getShippingDiscountAmount()
 * @method Magento_Sales_Model_Quote_Address setShippingDiscountAmount(float $value)
 * @method float getBaseShippingDiscountAmount()
 * @method Magento_Sales_Model_Quote_Address setBaseShippingDiscountAmount(float $value)
 * @method float getSubtotalInclTax()
 * @method Magento_Sales_Model_Quote_Address setSubtotalInclTax(float $value)
 * @method float getBaseSubtotalTotalInclTax()
 * @method Magento_Sales_Model_Quote_Address setBaseSubtotalTotalInclTax(float $value)
 * @method int getGiftMessageId()
 * @method Magento_Sales_Model_Quote_Address setGiftMessageId(int $value)
 * @method float getHiddenTaxAmount()
 * @method Magento_Sales_Model_Quote_Address setHiddenTaxAmount(float $value)
 * @method float getBaseHiddenTaxAmount()
 * @method Magento_Sales_Model_Quote_Address setBaseHiddenTaxAmount(float $value)
 * @method float getShippingHiddenTaxAmount()
 * @method Magento_Sales_Model_Quote_Address setShippingHiddenTaxAmount(float $value)
 * @method float getBaseShippingHiddenTaxAmnt()
 * @method Magento_Sales_Model_Quote_Address setBaseShippingHiddenTaxAmnt(float $value)
 * @method float getShippingInclTax()
 * @method Magento_Sales_Model_Quote_Address setShippingInclTax(float $value)
 * @method float getBaseShippingInclTax()
 * @method Magento_Sales_Model_Quote_Address setBaseShippingInclTax(float $value)
 *
 * @category    Magento
 * @package     Magento_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Sales_Model_Quote_Address extends Magento_Customer_Model_Address_Abstract
{
    const RATES_FETCH = 1;
    const RATES_RECALCULATE = 2;

    /**
     * Prefix of model events
     *
     * @var string
     */
    protected $_eventPrefix = 'sales_quote_address';

    /**
     * Name of event object
     *
     * @var string
     */
    protected $_eventObject = 'quote_address';

    /**
     * Quote object
     *
     * @var Magento_Sales_Model_Quote
     */
    protected $_items = null;

    /**
     * Quote object
     *
     * @var Magento_Sales_Model_Quote
     */
    protected $_quote = null;

    /**
     * Sales Quote address rates
     *
     * @var Magento_Sales_Model_Quote_Address_Rate
     */
    protected $_rates = null;

    /**
     * Total models collector
     *
     * @var Magento_Sales_Model_Quote_Address_Totla_Collector
     */
    protected $_totalCollector = null;

    /**
     * Total data as array
     *
     * @var array
     */
    protected $_totals = array();

    /**
     * @var array
     */
    protected $_totalAmounts = array();

    /**
     * @var array
     */
    protected $_baseTotalAmounts = array();

    /**
     * Whether to segregate by nominal items only
     *
     * @var bool
     */
    protected $_nominalOnly = null;

    /**
     * Core store config
     *
     * @var Magento_Core_Model_Store_Config
     */
    protected $_coreStoreConfig = null;

    /**
     * Enforce format of the street field
     *
     * @param Magento_Core_Model_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param Magento_Core_Model_Store_Config $coreStoreConfig
     * @param Magento_Core_Model_Resource_Abstract $resource
     * @param Magento_Data_Collection_Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_Context $context,
        Magento_Core_Model_Registry $registry,
        Magento_Core_Model_Store_Config $coreStoreConfig,
        Magento_Core_Model_Resource_Abstract $resource = null,
        Magento_Data_Collection_Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_coreStoreConfig = $coreStoreConfig;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Initialize resource
     */
    protected function _construct()
    {
        $this->_init('Magento_Sales_Model_Resource_Quote_Address');
    }

    /**
     * Initialize quote identifier before save
     *
     * @return Magento_Sales_Model_Quote_Address
     */
    protected function _beforeSave()
    {
        parent::_beforeSave();
        if ($this->getQuote()) {
            $quoteId = $this->getQuote()->getId();
            if ($quoteId) {
                $this->setQuoteId($quoteId);
            } else {
                $this->_dataSaveAllowed = false;
            }
            $this->setCustomerId($this->getQuote()->getCustomerId());
            /**
             * Init customer address id if customer address is assigned
             */
            if ($this->getCustomerAddress()) {
                $this->setCustomerAddressId($this->getCustomerAddress()->getId());
            }
        }
        if ($this->getAddressType() == Magento_Sales_Model_Quote_Address::TYPE_SHIPPING
            && $this->getSameAsBilling() === null
        ) {
            $this->setSameAsBilling(1);
        }
        return $this;
    }

    /**
     * Save child collections
     *
     * @return Magento_Sales_Model_Quote_Address
     */
    protected function _afterSave()
    {
        parent::_afterSave();
        if (null !== $this->_items) {
            $this->getItemsCollection()->save();
        }
        if (null !== $this->_rates) {
            $this->getShippingRatesCollection()->save();
        }
        return $this;
    }

    /**
     * Declare address quote model object
     *
     * @param   Magento_Sales_Model_Quote $quote
     * @return  Magento_Sales_Model_Quote_Address
     */
    public function setQuote(Magento_Sales_Model_Quote $quote)
    {
        $this->_quote = $quote;
        $this->setQuoteId($quote->getId());
        return $this;
    }

    /**
     * Retrieve quote object
     *
     * @return Magento_Sales_Model_Quote
     */
    public function getQuote()
    {
        return $this->_quote;
    }

    /**
     * Import quote address data from customer address object
     *
     * @param   Magento_Customer_Model_Address $address
     * @return  Magento_Sales_Model_Quote_Address
     */
    public function importCustomerAddress(Magento_Customer_Model_Address $address)
    {
        Mage::helper('Magento_Core_Helper_Data')->copyFieldset('customer_address', 'to_quote_address', $address, $this);
        $email = null;
        if ($address->hasEmail()) {
            $email =  $address->getEmail();
        } elseif ($address->getCustomer()) {
            $email = $address->getCustomer()->getEmail();
        }
        if ($email) {
            $this->setEmail($email);
        }
        return $this;
    }

    /**
     * Export data to customer address object
     *
     * @return Magento_Customer_Model_Address
     */
    public function exportCustomerAddress()
    {
        $address = Mage::getModel('Magento_Customer_Model_Address');
        Mage::helper('Magento_Core_Helper_Data')
            ->copyFieldset('sales_convert_quote_address', 'to_customer_address', $this, $address);
        return $address;
    }

    /**
     * Import address data from order address
     *
     * @param   Magento_Sales_Model_Order_Address $address
     * @return  Magento_Sales_Model_Quote_Address
     */
    public function importOrderAddress(Magento_Sales_Model_Order_Address $address)
    {
        $this->setAddressType($address->getAddressType())
            ->setCustomerId($address->getCustomerId())
            ->setCustomerAddressId($address->getCustomerAddressId())
            ->setEmail($address->getEmail());

        Mage::helper('Magento_Core_Helper_Data')
            ->copyFieldset('sales_convert_order_address', 'to_quote_address', $address, $this);

        return $this;
    }

    /**
     * Convert object to array
     *
     * @param   array $arrAttributes
     * @return  array
     */
    public function toArray(array $arrAttributes = array())
    {
        $arr = parent::toArray($arrAttributes);
        $arr['rates'] = $this->getShippingRatesCollection()->toArray($arrAttributes);
        $arr['items'] = $this->getItemsCollection()->toArray($arrAttributes);
        foreach ($this->getTotals() as $k=>$total) {
            $arr['totals'][$k] = $total->toArray();
        }
        return $arr;
    }

    /**
     * Retrieve address items collection
     *
     * @return Magento_Eav_Model_Entity_Collection_Abstract
     */
    public function getItemsCollection()
    {
        if (null === $this->_items) {
            $this->_items = Mage::getModel('Magento_Sales_Model_Quote_Address_Item')->getCollection()
                ->setAddressFilter($this->getId());

            if ($this->getId()) {
                foreach ($this->_items as $item) {
                    $item->setAddress($this);
                }
            }
        }
        return $this->_items;
    }

    /**
     * Get all available address items
     *
     * @return array
     */
    public function getAllItems()
    {
        // We calculate item list once and cache it in three arrays - all items, nominal, non-nominal
        $cachedItems = $this->_nominalOnly ? 'nominal' : ($this->_nominalOnly === false ? 'nonnominal' : 'all');
        $key = 'cached_items_' . $cachedItems;
        if (!$this->hasData($key)) {
            // For compatibility  we will use $this->_filterNominal to divide nominal items from non-nominal
            // (because it can be overloaded)
            // So keep current flag $this->_nominalOnly and restore it after cycle
            $wasNominal = $this->_nominalOnly;
            $this->_nominalOnly = true; // Now $this->_filterNominal() will return positive values for nominal items

            $quoteItems = $this->getQuote()->getItemsCollection();
            $addressItems = $this->getItemsCollection();

            $items = array();
            $nominalItems = array();
            $nonNominalItems = array();
            if ($this->getQuote()->getIsMultiShipping() && $addressItems->count() > 0) {
                foreach ($addressItems as $aItem) {
                    if ($aItem->isDeleted()) {
                        continue;
                    }

                    if (!$aItem->getQuoteItemImported()) {
                        $qItem = $this->getQuote()->getItemById($aItem->getQuoteItemId());
                        if ($qItem) {
                            $aItem->importQuoteItem($qItem);
                        }
                    }
                    $items[] = $aItem;
                    if ($this->_filterNominal($aItem)) {
                        $nominalItems[] = $aItem;
                    } else {
                        $nonNominalItems[] = $aItem;
                    }
                }
            } else {
                /*
                * For virtual quote we assign items only to billing address, otherwise - only to shipping address
                */
                $addressType = $this->getAddressType();
                $canAddItems = $this->getQuote()->isVirtual()
                    ? ($addressType == self::TYPE_BILLING)
                    : ($addressType == self::TYPE_SHIPPING);

                if ($canAddItems) {
                    foreach ($quoteItems as $qItem) {
                        if ($qItem->isDeleted()) {
                            continue;
                        }
                        $items[] = $qItem;
                        if ($this->_filterNominal($qItem)) {
                            $nominalItems[] = $qItem;
                        } else {
                            $nonNominalItems[] = $qItem;
                        }
                    }
                }
            }

            // Cache calculated lists
            $this->setData('cached_items_all', $items);
            $this->setData('cached_items_nominal', $nominalItems);
            $this->setData('cached_items_nonnominal', $nonNominalItems);

            $this->_nominalOnly = $wasNominal; // Restore original value before we changed it
        }

        $items = $this->getData($key);
        return $items;
    }

    /**
     * Getter for all non-nominal items
     *
     * @return array
     */
    public function getAllNonNominalItems()
    {
        $this->_nominalOnly = false;
        $result = $this->getAllItems();
        $this->_nominalOnly = null;
        return $result;
    }

    /**
     * Getter for all nominal items
     *
     * @return array
     */
    public function getAllNominalItems()
    {
        $this->_nominalOnly = true;
        $result = $this->getAllItems();
        $this->_nominalOnly = null;
        return $result;
    }

    /**
     * Segregate by nominal criteria
     *
     * true: get nominals only
     * false: get non-nominals only
     * null: get all
     *
     * @param Magento_Sales_Model_Quote_Item_Abstract
     * @return Magento_Sales_Model_Quote_Item_Abstract|false
     */
    protected function _filterNominal($item)
    {
        return (null === $this->_nominalOnly)
            || ((false === $this->_nominalOnly) && !$item->isNominal())
            || ((true === $this->_nominalOnly) && $item->isNominal())
            ? $item : false;
    }

    /**
     * Retrieve all visible items
     *
     * @return array
     */
    public function getAllVisibleItems()
    {
        $items = array();
        foreach ($this->getAllItems() as $item) {
            if (!$item->getParentItemId()) {
                $items[] = $item;
            }
        }
        return $items;
    }

    /**
     * Retrieve item quantity by id
     *
     * @param int $itemId
     * @return float|int
     */
    public function getItemQty($itemId = 0)
    {
        if ($this->hasData('item_qty')) {
            return $this->getData('item_qty');
        }

        $qty = 0;
        if ($itemId == 0) {
            foreach ($this->getAllItems() as $item) {
                $qty += $item->getQty();
            }
        } else {
            $item = $this->getItemById($itemId);
            if ($item) {
                $qty = $item->getQty();
            }
        }
        return $qty;
    }

    /**
     * Check Quote address has Items
     *
     * @return bool
     */
    public function hasItems()
    {
        return sizeof($this->getAllItems()) > 0;
    }

    /**
     * Get address item object by id without
     *
     * @param int $itemId
     * @return Magento_Sales_Model_Quote_Address_Item
     */
    public function getItemById($itemId)
    {
        foreach ($this->getItemsCollection() as $item) {
            if ($item->getId() == $itemId) {
                return $item;
            }
        }
        return false;
    }

    /**
     * Get prepared not deleted item
     *
     * @param int $itemId
     * @return Magento_Sales_Model_Quote_Address_Item
     */
    public function getValidItemById($itemId)
    {
        foreach ($this->getAllItems() as $item) {
            if ($item->getId() == $itemId) {
                return $item;
            }
        }
        return false;
    }

    /**
     * Retrieve item object by quote item Id
     *
     * @param int $itemId
     * @return Magento_Sales_Model_Quote_Address_Item
     */
    public function getItemByQuoteItemId($itemId)
    {
        foreach ($this->getItemsCollection() as $item) {
            if ($item->getQuoteItemId() == $itemId) {
                return $item;
            }
        }
        return false;
    }

    /**
     * Remove item from collection
     *
     * @param int $itemId
     * @return Magento_Sales_Model_Quote_Address
     */
    public function removeItem($itemId)
    {
        $item = $this->getItemById($itemId);
        if ($item) {
            $item->isDeleted(true);
        }
        return $this;
    }

    /**
     * Add item to address
     *
     * @param   Magento_Sales_Model_Quote_Item_Abstract $item
     * @param   int $qty
     * @return  Magento_Sales_Model_Quote_Address
     */
    public function addItem(Magento_Sales_Model_Quote_Item_Abstract $item, $qty=null)
    {
        if ($item instanceof Magento_Sales_Model_Quote_Item) {
            if ($item->getParentItemId()) {
                return $this;
            }
            $addressItem = Mage::getModel('Magento_Sales_Model_Quote_Address_Item')
                ->setAddress($this)
                ->importQuoteItem($item);
            $this->getItemsCollection()->addItem($addressItem);

            if ($item->getHasChildren()) {
                foreach ($item->getChildren() as $child) {
                    $addressChildItem = Mage::getModel('Magento_Sales_Model_Quote_Address_Item')
                        ->setAddress($this)
                        ->importQuoteItem($child)
                        ->setParentItem($addressItem);
                    $this->getItemsCollection()->addItem($addressChildItem);
                }
            }
        } else {
            $addressItem = $item;
            $addressItem->setAddress($this);
            if (!$addressItem->getId()) {
                $this->getItemsCollection()->addItem($addressItem);
            }
        }

        if ($qty) {
            $addressItem->setQty($qty);
        }
        return $this;
    }

    /**
     * Retrieve collection of quote shipping rates
     *
     * @return Magento_Eav_Model_Entity_Collection_Abstract
     */
    public function getShippingRatesCollection()
    {
        if (null === $this->_rates) {
            $this->_rates = Mage::getModel('Magento_Sales_Model_Quote_Address_Rate')->getCollection()
                ->setAddressFilter($this->getId());
            if ($this->getQuote()->hasNominalItems(false)) {
                $this->_rates->setFixedOnlyFilter(true);
            }
            if ($this->getId()) {
                foreach ($this->_rates as $rate) {
                    $rate->setAddress($this);
                }
            }
        }
        return $this->_rates;
    }

    /**
     * Retrieve all address shipping rates
     *
     * @return array
     */
    public function getAllShippingRates()
    {
        $rates = array();
        foreach ($this->getShippingRatesCollection() as $rate) {
            if (!$rate->isDeleted()) {
                $rates[] = $rate;
            }
        }
        return $rates;
    }

    /**
     * Retrieve all grouped shipping rates
     *
     * @return array
     */
    public function getGroupedAllShippingRates()
    {
        $rates = array();
        foreach ($this->getShippingRatesCollection() as $rate) {
            if (!$rate->isDeleted() && $rate->getCarrierInstance()) {
                if (!isset($rates[$rate->getCarrier()])) {
                    $rates[$rate->getCarrier()] = array();
                }

                $rates[$rate->getCarrier()][] = $rate;
                $rates[$rate->getCarrier()][0]->carrier_sort_order = $rate->getCarrierInstance()->getSortOrder();
            }
        }
        uasort($rates, array($this, '_sortRates'));
        return $rates;
    }

    /**
     * Sort rates recursive callback
     *
     * @param array $firstItem
     * @param array $secondItem
     * @return int
     */
    protected function _sortRates($firstItem, $secondItem)
    {
        if ((int)$firstItem[0]->carrier_sort_order < (int)$secondItem[0]->carrier_sort_order) {
            return -1;
        } elseif ((int)$firstItem[0]->carrier_sort_order > (int)$secondItem[0]->carrier_sort_order) {
            return 1;
        } else {
            return 0;
        }
    }

    /**
     * Retrieve shipping rate by identifier
     *
     * @param   int $rateId
     * @return  Magento_Sales_Model_Quote_Address_Rate|bool
     */
    public function getShippingRateById($rateId)
    {
        foreach ($this->getShippingRatesCollection() as $rate) {
            if ($rate->getId() == $rateId) {
                return $rate;
            }
        }
        return false;
    }

    /**
     * Retrieve shipping rate by code
     *
     * @param   string $code
     * @return  Magento_Sales_Model_Quote_Address_Rate
     */
    public function getShippingRateByCode($code)
    {
        foreach ($this->getShippingRatesCollection() as $rate) {
            if ($rate->getCode() == $code) {
                return $rate;
            }
        }
        return false;
    }

    /**
     * Mark all shipping rates as deleted
     *
     * @return Magento_Sales_Model_Quote_Address
     */
    public function removeAllShippingRates()
    {
        foreach ($this->getShippingRatesCollection() as $rate) {
            $rate->isDeleted(true);
        }
        return $this;
    }

    /**
     * Add shipping rate
     *
     * @param Magento_Sales_Model_Quote_Address_Rate $rate
     * @return Magento_Sales_Model_Quote_Address
     */
    public function addShippingRate(Magento_Sales_Model_Quote_Address_Rate $rate)
    {
        $rate->setAddress($this);
        $this->getShippingRatesCollection()->addItem($rate);
        return $this;
    }

    /**
     * Collecting shipping rates by address
     *
     * @return Magento_Sales_Model_Quote_Address
     */
    public function collectShippingRates()
    {
        if (!$this->getCollectShippingRates()) {
            return $this;
        }

        $this->setCollectShippingRates(false);

        $this->removeAllShippingRates();

        if (!$this->getCountryId()) {
            return $this;
        }

        $found = $this->requestShippingRates();
        if (!$found) {
            $this->setShippingAmount(0)
                ->setBaseShippingAmount(0)
                ->setShippingMethod('')
                ->setShippingDescription('');
        }

        return $this;
    }

    /**
     * Request shipping rates for entire address or specified address item
     * Returns true if current selected shipping method code corresponds to one of the found rates
     *
     * @param Magento_Sales_Model_Quote_Item_Abstract $item
     * @return bool
     */
    public function requestShippingRates(Magento_Sales_Model_Quote_Item_Abstract $item = null)
    {
        /** @var $request Magento_Shipping_Model_Rate_Request */
        $request = Mage::getModel('Magento_Shipping_Model_Rate_Request');
        $request->setAllItems($item ? array($item) : $this->getAllItems());
        $request->setDestCountryId($this->getCountryId());
        $request->setDestRegionId($this->getRegionId());
        $request->setDestRegionCode($this->getRegionCode());
        $request->setDestStreet($this->getStreetFull());
        $request->setDestCity($this->getCity());
        $request->setDestPostcode($this->getPostcode());
        $request->setPackageValue($item ? $item->getBaseRowTotal() : $this->getBaseSubtotal());
        $packageWithDiscount = $item
            ? $item->getBaseRowTotal() - $item->getBaseDiscountAmount()
            : $this->getBaseSubtotalWithDiscount();
        $request->setPackageValueWithDiscount($packageWithDiscount);
        $request->setPackageWeight($item ? $item->getRowWeight() : $this->getWeight());
        $request->setPackageQty($item ? $item->getQty() : $this->getItemQty());

        /**
         * Need for shipping methods that use insurance based on price of physical products
         */
        $packagePhysicalValue = $item
            ? $item->getBaseRowTotal()
            : $this->getBaseSubtotal() - $this->getBaseVirtualAmount();
        $request->setPackagePhysicalValue($packagePhysicalValue);

        $request->setFreeMethodWeight($item ? 0 : $this->getFreeMethodWeight());

        /**
         * Store and website identifiers need specify from quote
         */
        /*$request->setStoreId(Mage::app()->getStore()->getId());
        $request->setWebsiteId(Mage::app()->getStore()->getWebsiteId());*/

        $request->setStoreId($this->getQuote()->getStore()->getId());
        $request->setWebsiteId($this->getQuote()->getStore()->getWebsiteId());
        $request->setFreeShipping($this->getFreeShipping());
        /**
         * Currencies need to convert in free shipping
         */
        $request->setBaseCurrency($this->getQuote()->getStore()->getBaseCurrency());
        $request->setPackageCurrency($this->getQuote()->getStore()->getCurrentCurrency());
        $request->setLimitCarrier($this->getLimitCarrier());

        $request->setBaseSubtotalInclTax($this->getBaseSubtotalInclTax());

        $result = Mage::getModel('Magento_Shipping_Model_Shipping')->collectRates($request)->getResult();

        $found = false;
        if ($result) {
            $shippingRates = $result->getAllRates();

            foreach ($shippingRates as $shippingRate) {
                $rate = Mage::getModel('Magento_Sales_Model_Quote_Address_Rate')
                    ->importShippingRate($shippingRate);
                if (!$item) {
                    $this->addShippingRate($rate);
                }

                if ($this->getShippingMethod() == $rate->getCode()) {
                    if ($item) {
                        $item->setBaseShippingAmount($rate->getPrice());
                    } else {
                        /**
                         * possible bug: this should be setBaseShippingAmount(),
                         * see Magento_Sales_Model_Quote_Address_Total_Shipping::collect()
                         * where this value is set again from the current specified rate price
                         * (looks like a workaround for this bug)
                         */
                        $this->setShippingAmount($rate->getPrice());
                    }

                    $found = true;
                }
            }
        }
        return $found;
    }

    /**
     * Get totals collector model
     *
     * @return Magento_Sales_Model_Quote_Address_Total_Collector
     */
    public function getTotalCollector()
    {
        if ($this->_totalCollector === null) {
            $this->_totalCollector = Mage::getModel(
                'Magento_Sales_Model_Quote_Address_Total_Collector',
                array('store' => $this->getQuote()->getStore())
            );
        }
        return $this->_totalCollector;
    }

    /**
     * Collect address totals
     *
     * @return Magento_Sales_Model_Quote_Address
     */
    public function collectTotals()
    {
        Mage::dispatchEvent($this->_eventPrefix . '_collect_totals_before', array($this->_eventObject => $this));
        foreach ($this->getTotalCollector()->getCollectors() as $model) {
            $model->collect($this);
        }
        Mage::dispatchEvent($this->_eventPrefix . '_collect_totals_after', array($this->_eventObject => $this));
        return $this;
    }

    /**
     * Get address totals as array
     *
     * @return array
     */
    public function getTotals()
    {
        foreach ($this->getTotalCollector()->getRetrievers() as $model) {
            $model->fetch($this);
        }
        return $this->_totals;
    }

    /**
     * Add total data or model
     *
     * @param Magento_Sales_Model_Quote_Total|array $total
     * @return Magento_Sales_Model_Quote_Address
     */
    public function addTotal($total)
    {
        if (is_array($total)) {
            $totalInstance = Mage::getModel('Magento_Sales_Model_Quote_Address_Total')->setData($total);
        } elseif ($total instanceof Magento_Sales_Model_Quote_Total) {
            $totalInstance = $total;
        }
        $totalInstance->setAddress($this);
        $this->_totals[$totalInstance->getCode()] = $totalInstance;
        return $this;
    }

    /**
     * Rewrite clone method
     *
     * @return Magento_Sales_Model_Quote_Address
     */
    public function __clone()
    {
        $this->setId(null);
    }

    /**
     * Validate minimum amount
     *
     * @return bool
     */
    public function validateMinimumAmount()
    {
        $storeId = $this->getQuote()->getStoreId();
        if (!$this->_coreStoreConfig->getConfigFlag('sales/minimum_order/active', $storeId)) {
            return true;
        }

        if ($this->getQuote()->getIsVirtual() && $this->getAddressType() == self::TYPE_SHIPPING) {
            return true;
        } elseif (!$this->getQuote()->getIsVirtual() && $this->getAddressType() != self::TYPE_SHIPPING) {
            return true;
        }

        $amount = $this->_coreStoreConfig->getConfig('sales/minimum_order/amount', $storeId);
        if ($this->getBaseSubtotalWithDiscount() < $amount) {
            return false;
        }
        return true;
    }

    /**
     * Retrieve applied taxes
     *
     * @return array
     */
    public function getAppliedTaxes()
    {
        return unserialize($this->getData('applied_taxes'));
    }

    /**
     * Set applied taxes
     *
     * @param array $data
     * @return Magento_Sales_Model_Quote_Address
     */
    public function setAppliedTaxes($data)
    {
        return $this->setData('applied_taxes', serialize($data));
    }

    /**
     * Set shipping amount
     *
     * @param float $value
     * @param bool $alreadyExclTax
     * @return Magento_Sales_Model_Quote_Address
     */
    public function setShippingAmount($value, $alreadyExclTax = false)
    {
        return $this->setData('shipping_amount', $value);
    }

    /**
     * Set base shipping amount
     *
     * @param float $value
     * @param bool $alreadyExclTax
     * @return Magento_Sales_Model_Quote_Address
     */
    public function setBaseShippingAmount($value, $alreadyExclTax = false)
    {
        return $this->setData('base_shipping_amount', $value);
    }

    /**
     * Set total amount value
     *
     * @param   string $code
     * @param   float $amount
     * @return  Magento_Sales_Model_Quote_Address
     */
    public function setTotalAmount($code, $amount)
    {
        $this->_totalAmounts[$code] = $amount;
        if ($code != 'subtotal') {
            $code = $code . '_amount';
        }
        $this->setData($code, $amount);
        return $this;
    }

    /**
     * Set total amount value in base store currency
     *
     * @param   string $code
     * @param   float $amount
     * @return  Magento_Sales_Model_Quote_Address
     */
    public function setBaseTotalAmount($code, $amount)
    {
        $this->_baseTotalAmounts[$code] = $amount;
        if ($code != 'subtotal') {
            $code = $code . '_amount';
        }
        $this->setData('base_' . $code, $amount);
        return $this;
    }

    /**
     * Add amount total amount value
     *
     * @param   string $code
     * @param   float $amount
     * @return  Magento_Sales_Model_Quote_Address
     */
    public function addTotalAmount($code, $amount)
    {
        $amount = $this->getTotalAmount($code)+$amount;
        $this->setTotalAmount($code, $amount);
        return $this;
    }

    /**
     * Add amount total amount value in base store currency
     *
     * @param   string $code
     * @param   float $amount
     * @return  Magento_Sales_Model_Quote_Address
     */
    public function addBaseTotalAmount($code, $amount)
    {
        $amount = $this->getBaseTotalAmount($code)+$amount;
        $this->setBaseTotalAmount($code, $amount);
        return $this;
    }

    /**
     * Get total amount value by code
     *
     * @param   string $code
     * @return  float
     */
    public function getTotalAmount($code)
    {
        if (isset($this->_totalAmounts[$code])) {
            return  $this->_totalAmounts[$code];
        }
        return 0;
    }

    /**
     * Get total amount value by code in base store curncy
     *
     * @param   string $code
     * @return  float
     */
    public function getBaseTotalAmount($code)
    {
        if (isset($this->_baseTotalAmounts[$code])) {
            return  $this->_baseTotalAmounts[$code];
        }
        return 0;
    }

    /**
     * Get all total amount values
     *
     * @return array
     */
    public function getAllTotalAmounts()
    {
        return $this->_totalAmounts;
    }

    /**
     * Get all total amount values in base currency
     *
     * @return array
     */
    public function getAllBaseTotalAmounts()
    {
        return $this->_baseTotalAmounts;
    }

    /**
     * Get subtotal amount with applied discount in base currency
     *
     * @return float
     */
    public function getBaseSubtotalWithDiscount()
    {
        return $this->getBaseSubtotal()+$this->getBaseDiscountAmount();
    }

    /**
     * Get subtotal amount with applied discount
     *
     * @return float
     */
    public function getSubtotalWithDiscount()
    {
        return $this->getSubtotal()+$this->getDiscountAmount();
    }
}
