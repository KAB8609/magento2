<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CustomerCustomAttributes
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Customer observer
 *
 */
namespace Magento\CustomerCustomAttributes\Model;

class Observer
{
    const CONVERT_ALGORITM_SOURCE_TARGET_WITH_PREFIX = 1;
    const CONVERT_ALGORITM_SOURCE_WITHOUT_PREFIX     = 2;
    const CONVERT_ALGORITM_TARGET_WITHOUT_PREFIX     = 3;

    const CONVERT_TYPE_CUSTOMER             = 'customer';
    const CONVERT_TYPE_CUSTOMER_ADDRESS     = 'customer_address';

    /**
     * After load observer for quote
     *
     * @param \Magento\Event\Observer $observer
     * @return \Magento\CustomerCustomAttributes\Model\Observer
     */
    public function salesQuoteAfterLoad(\Magento\Event\Observer $observer)
    {
        $quote = $observer->getEvent()->getQuote();
        if ($quote instanceof \Magento\Core\Model\AbstractModel) {
            \Mage::getModel('\Magento\CustomerCustomAttributes\Model\Sales\Quote')
                ->load($quote->getId())
                ->attachAttributeData($quote);
        }

        return $this;
    }

    /**
     * After load observer for collection of quote address
     *
     * @param \Magento\Event\Observer $observer
     * @return \Magento\CustomerCustomAttributes\Model\Observer
     */
    public function salesQuoteAddressCollectionAfterLoad(\Magento\Event\Observer $observer)
    {
        $collection = $observer->getEvent()->getQuoteAddressCollection();
        if ($collection instanceof \Magento\Data\Collection\Db) {
            \Mage::getModel('\Magento\CustomerCustomAttributes\Model\Sales\Quote\Address')
                ->attachDataToEntities($collection->getItems());
        }

        return $this;
    }

    /**
     * After save observer for quote
     *
     * @param \Magento\Event\Observer $observer
     * @return \Magento\CustomerCustomAttributes\Model\Observer
     */
    public function salesQuoteAfterSave(\Magento\Event\Observer $observer)
    {
        $quote = $observer->getEvent()->getQuote();
        if ($quote instanceof \Magento\Core\Model\AbstractModel) {
            \Mage::getModel('\Magento\CustomerCustomAttributes\Model\Sales\Quote')
                ->saveAttributeData($quote);
        }

        return $this;
    }

    /**
     * After save observer for quote address
     *
     * @param \Magento\Event\Observer $observer
     * @return \Magento\CustomerCustomAttributes\Model\Observer
     */
    public function salesQuoteAddressAfterSave(\Magento\Event\Observer $observer)
    {
        $quoteAddress = $observer->getEvent()->getQuoteAddress();
        if ($quoteAddress instanceof \Magento\Core\Model\AbstractModel) {
            \Mage::getModel('\Magento\CustomerCustomAttributes\Model\Sales\Quote\Address')
                ->saveAttributeData($quoteAddress);
        }

        return $this;
    }

    /**
     * After load observer for order
     *
     * @param \Magento\Event\Observer $observer
     * @return \Magento\CustomerCustomAttributes\Model\Observer
     */
    public function salesOrderAfterLoad(\Magento\Event\Observer $observer)
    {
        $order = $observer->getEvent()->getOrder();
        if ($order instanceof \Magento\Core\Model\AbstractModel) {
            \Mage::getModel('\Magento\CustomerCustomAttributes\Model\Sales\Order')
                ->load($order->getId())
                ->attachAttributeData($order);
        }

        return $this;
    }

    /**
     * After load observer for collection of order address
     *
     * @param \Magento\Event\Observer $observer
     * @return \Magento\CustomerCustomAttributes\Model\Observer
     */
    public function salesOrderAddressCollectionAfterLoad(\Magento\Event\Observer $observer)
    {
        $collection = $observer->getEvent()->getOrderAddressCollection();
        if ($collection instanceof \Magento\Data\Collection\Db) {
            \Mage::getModel('\Magento\CustomerCustomAttributes\Model\Sales\Order\Address')
                ->attachDataToEntities($collection->getItems());
        }

        return $this;
    }

    /**
     * After save observer for order
     *
     * @param \Magento\Event\Observer $observer
     * @return \Magento\CustomerCustomAttributes\Model\Observer
     */
    public function salesOrderAfterSave(\Magento\Event\Observer $observer)
    {
        $order = $observer->getEvent()->getOrder();
        if ($order instanceof \Magento\Core\Model\AbstractModel) {
            \Mage::getModel('\Magento\CustomerCustomAttributes\Model\Sales\Order')
                ->saveAttributeData($order);
        }

        return $this;
    }

    /**
     * After load observer for order address
     *
     * @param \Magento\Event\Observer $observer
     * @return \Magento\CustomerCustomAttributes\Model\Observer
     */
    public function salesOrderAddressAfterLoad(\Magento\Event\Observer $observer)
    {
        $address = $observer->getEvent()->getAddress();
        if ($address instanceof \Magento\Core\Model\AbstractModel) {
            \Mage::getModel('\Magento\CustomerCustomAttributes\Model\Sales\Order\Address')
                ->attachDataToEntities(array($address));
        }

        return $this;
    }

    /**
     * After save observer for order address
     *
     * @param \Magento\Event\Observer $observer
     * @return \Magento\CustomerCustomAttributes\Model\Observer
     */
    public function salesOrderAddressAfterSave(\Magento\Event\Observer $observer)
    {
        $orderAddress = $observer->getEvent()->getAddress();
        if ($orderAddress instanceof \Magento\Core\Model\AbstractModel) {
            \Mage::getModel('\Magento\CustomerCustomAttributes\Model\Sales\Order\Address')
                ->saveAttributeData($orderAddress);
        }

        return $this;
    }

    /**
     * Before save observer for customer attribute
     *
     * @param \Magento\Event\Observer $observer
     * @return \Magento\CustomerCustomAttributes\Model\Observer
     */
    public function enterpriseCustomerAttributeBeforeSave(\Magento\Event\Observer $observer)
    {
        $attribute = $observer->getEvent()->getAttribute();
        if ($attribute instanceof \Magento\Customer\Model\Attribute && $attribute->isObjectNew()) {
            /**
             * Check for maximum attribute_code length
             */
            $attributeCodeMaxLength = \Magento\Eav\Model\Entity\Attribute::ATTRIBUTE_CODE_MAX_LENGTH - 9;
            $validate = \Zend_Validate::is($attribute->getAttributeCode(), 'StringLength', array(
                'max' => $attributeCodeMaxLength
            ));
            if (!$validate) {
                throw \Mage::exception('Magento_Eav',
                    __('Maximum length of attribute code must be less than %1 symbols', $attributeCodeMaxLength)
                );
            }
        }

        return $this;
    }

    /**
     * After save observer for customer attribute
     *
     * @param \Magento\Event\Observer $observer
     * @return \Magento\CustomerCustomAttributes\Model\Observer
     */
    public function enterpriseCustomerAttributeSave(\Magento\Event\Observer $observer)
    {
        $attribute = $observer->getEvent()->getAttribute();
        if ($attribute instanceof \Magento\Customer\Model\Attribute && $attribute->isObjectNew()) {
            \Mage::getModel('\Magento\CustomerCustomAttributes\Model\Sales\Quote')
                ->saveNewAttribute($attribute);
            \Mage::getModel('\Magento\CustomerCustomAttributes\Model\Sales\Order')
                ->saveNewAttribute($attribute);
        }

        return $this;
    }

    /**
     * After delete observer for customer attribute
     *
     * @param \Magento\Event\Observer $observer
     * @return \Magento\CustomerCustomAttributes\Model\Observer
     */
    public function enterpriseCustomerAttributeDelete(\Magento\Event\Observer $observer)
    {
        $attribute = $observer->getEvent()->getAttribute();
        if ($attribute instanceof \Magento\Customer\Model\Attribute && !$attribute->isObjectNew()) {
            \Mage::getModel('\Magento\CustomerCustomAttributes\Model\Sales\Quote')
                ->deleteAttribute($attribute);
            \Mage::getModel('\Magento\CustomerCustomAttributes\Model\Sales\Order')
                ->deleteAttribute($attribute);
        }

        return $this;
    }

    /**
     * After save observer for customer address attribute
     *
     * @param \Magento\Event\Observer $observer
     * @return \Magento\CustomerCustomAttributes\Model\Observer
     */
    public function enterpriseCustomerAddressAttributeSave(\Magento\Event\Observer $observer)
    {
        $attribute = $observer->getEvent()->getAttribute();
        if ($attribute instanceof \Magento\Customer\Model\Attribute && $attribute->isObjectNew()) {
            \Mage::getModel('\Magento\CustomerCustomAttributes\Model\Sales\Quote\Address')
                ->saveNewAttribute($attribute);
            \Mage::getModel('\Magento\CustomerCustomAttributes\Model\Sales\Order\Address')
                ->saveNewAttribute($attribute);
        }

        return $this;
    }

    /**
     * After delete observer for customer address attribute
     *
     * @param \Magento\Event\Observer $observer
     * @return \Magento\CustomerCustomAttributes\Model\Observer
     */
    public function enterpriseCustomerAddressAttributeDelete(\Magento\Event\Observer $observer)
    {
        $attribute = $observer->getEvent()->getAttribute();
        if ($attribute instanceof \Magento\Customer\Model\Attribute && !$attribute->isObjectNew()) {
            \Mage::getModel('\Magento\CustomerCustomAttributes\Model\Sales\Quote\Address')
                ->deleteAttribute($attribute);
            \Mage::getModel('\Magento\CustomerCustomAttributes\Model\Sales\Order\Address')
                ->deleteAttribute($attribute);
        }

        return $this;
    }

    /**
     * Observer for converting quote to order
     *
     * @param \Magento\Event\Observer $observer
     * @return \Magento\CustomerCustomAttributes\Model\Observer
     */
    public function coreCopyFieldsetSalesConvertQuoteToOrder(\Magento\Event\Observer $observer)
    {
        $this->_copyFieldset(
            $observer,
            self::CONVERT_ALGORITM_SOURCE_TARGET_WITH_PREFIX,
            self::CONVERT_TYPE_CUSTOMER
        );

        return $this;
    }

    /**
     * Observer for converting quote address to order address
     *
     * @param \Magento\Event\Observer $observer
     * @return \Magento\CustomerCustomAttributes\Model\Observer
     */
    public function coreCopyFieldsetSalesConvertQuoteAddressToOrderAddress(\Magento\Event\Observer $observer)
    {
        $this->_copyFieldset(
            $observer,
            self::CONVERT_ALGORITM_SOURCE_TARGET_WITH_PREFIX,
            self::CONVERT_TYPE_CUSTOMER_ADDRESS
        );

        return $this;
    }

    /**
     * Observer for converting order to quote
     *
     * @param \Magento\Event\Observer $observer
     * @return \Magento\CustomerCustomAttributes\Model\Observer
     */
    public function coreCopyFieldsetSalesCopyOrderToEdit(\Magento\Event\Observer $observer)
    {
        $this->_copyFieldset(
            $observer,
            self::CONVERT_ALGORITM_SOURCE_TARGET_WITH_PREFIX,
            self::CONVERT_TYPE_CUSTOMER
        );

        return $this;
    }

    /**
     * Observer for converting order billing address to quote billing address
     *
     * @param \Magento\Event\Observer $observer
     * @return \Magento\CustomerCustomAttributes\Model\Observer
     */
    public function coreCopyFieldsetSalesCopyOrderBillingAddressToOrder(\Magento\Event\Observer $observer)
    {
        $this->_copyFieldset(
            $observer,
            self::CONVERT_ALGORITM_SOURCE_TARGET_WITH_PREFIX,
            self::CONVERT_TYPE_CUSTOMER_ADDRESS
        );

        return $this;
    }

    /**
     * Observer for converting order shipping address to quote shipping address
     *
     * @param \Magento\Event\Observer $observer
     * @return \Magento\CustomerCustomAttributes\Model\Observer
     */
    public function coreCopyFieldsetSalesCopyOrderShippingAddressToOrder(\Magento\Event\Observer $observer)
    {
        $this->_copyFieldset(
            $observer,
            self::CONVERT_ALGORITM_SOURCE_TARGET_WITH_PREFIX,
            self::CONVERT_TYPE_CUSTOMER_ADDRESS
        );

        return $this;
    }

    /**
     * Observer for converting customer to quote
     *
     * @param \Magento\Event\Observer $observer
     * @return \Magento\CustomerCustomAttributes\Model\Observer
     */
    public function coreCopyFieldsetCustomerAccountToQuote(\Magento\Event\Observer $observer)
    {
        $this->_copyFieldset(
            $observer,
            self::CONVERT_ALGORITM_SOURCE_WITHOUT_PREFIX,
            self::CONVERT_TYPE_CUSTOMER
        );

        return $this;
    }

    /**
     * Observer for converting customer address to quote address
     *
     * @param \Magento\Event\Observer $observer
     * @return \Magento\CustomerCustomAttributes\Model\Observer
     */
    public function coreCopyFieldsetCustomerAddressToQuoteAddress(\Magento\Event\Observer $observer)
    {
        $this->_copyFieldset(
            $observer,
            self::CONVERT_ALGORITM_SOURCE_WITHOUT_PREFIX,
            self::CONVERT_TYPE_CUSTOMER_ADDRESS
        );

        return $this;
    }

    /**
     * Observer for converting quote address to customer address
     *
     * @param \Magento\Event\Observer $observer
     * @return \Magento\CustomerCustomAttributes\Model\Observer
     */
    public function coreCopyFieldsetQuoteAddressToCustomerAddress(\Magento\Event\Observer $observer)
    {
        $this->_copyFieldset(
            $observer,
            self::CONVERT_ALGORITM_SOURCE_WITHOUT_PREFIX,
            self::CONVERT_TYPE_CUSTOMER_ADDRESS
        );

        return $this;
    }

    /**
     * Observer for converting quote to customer
     *
     * @param \Magento\Event\Observer $observer
     * @return \Magento\CustomerCustomAttributes\Model\Observer
     */
    public function coreCopyFieldsetCheckoutOnepageQuoteToCustomer(\Magento\Event\Observer $observer)
    {
        $this->_copyFieldset(
            $observer,
            self::CONVERT_ALGORITM_TARGET_WITHOUT_PREFIX,
            self::CONVERT_TYPE_CUSTOMER
        );

        return $this;
    }

    /**
     * CopyFieldset converts customer attributes from source object to target object
     *
     * @param \Magento\Event\Observer $observer
     * @param int $algoritm
     * @param int $convertType
     * @return \Magento\CustomerCustomAttributes\Model\Observer
     */
    protected function _copyFieldset(\Magento\Event\Observer $observer, $algoritm, $convertType)
    {
        $source = $observer->getEvent()->getSource();
        $target = $observer->getEvent()->getTarget();

        if ($source instanceof \Magento\Core\Model\AbstractModel && $target instanceof \Magento\Core\Model\AbstractModel) {
            if ($convertType == self::CONVERT_TYPE_CUSTOMER) {
                $attributes = \Mage::helper('Magento\CustomerCustomAttributes\Helper\Data')->getCustomerUserDefinedAttributeCodes();
                $prefix     = 'customer_';
            } else if ($convertType == self::CONVERT_TYPE_CUSTOMER_ADDRESS) {
                $attributes = \Mage::helper('Magento\CustomerCustomAttributes\Helper\Data')->getCustomerAddressUserDefinedAttributeCodes();
                $prefix     = '';
            } else {
                return $this;
            }

            foreach ($attributes as $attribute) {
                switch ($algoritm) {
                    case self::CONVERT_ALGORITM_SOURCE_TARGET_WITH_PREFIX:
                        $sourceAttribute = $prefix . $attribute;
                        $targetAttribute = $prefix . $attribute;
                        break;
                    case self::CONVERT_ALGORITM_SOURCE_WITHOUT_PREFIX:
                        $sourceAttribute = $attribute;
                        $targetAttribute = $prefix . $attribute;
                        break;
                    case self::CONVERT_ALGORITM_TARGET_WITHOUT_PREFIX:
                        $sourceAttribute = $prefix . $attribute;
                        $targetAttribute = $attribute;
                        break;
                    default:
                        return $this;
                }
                $target->setData($targetAttribute, $source->getData($sourceAttribute));
            }
        }

        return $this;
    }
}
