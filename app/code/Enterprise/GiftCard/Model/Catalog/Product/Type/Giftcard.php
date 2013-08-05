<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_GiftCard
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Enterprise_GiftCard_Model_Catalog_Product_Type_Giftcard extends Mage_Catalog_Model_Product_Type_Abstract
{
    const TYPE_GIFTCARD = 'giftcard';

    /**
     * Whether product quantity is fractional number or not
     *
     * @var bool
     */
    protected $_canUseQtyDecimals = false;

    /**
     * Product is configurable
     *
     * @var bool
     */
    protected $_canConfigure = true;

    /**
     * Mock for store instance
     *
     * @var Mage_Core_Model_Store
     */
    protected $_store;

    /**
     * Locale instance
     *
     * @var Mage_Core_Model_LocaleInterface
     */
    protected $_locale;

    /**
     * Array of allowed giftcard amounts
     *
     * @var array
     */
    protected $_giftcardAmounts = null;

    /**
     * Initialize data
     *
     * @param Magento_Filesystem $filesystem
     * @param array $data
     */
    public function __construct(Magento_Filesystem $filesystem, array $data = array())
    {
        $this->_store = isset($data['store']) ? $data['store'] : Mage::app()->getStore();
        $this->_locale = isset($data['locale']) ? $data['locale'] : Mage::app()->getLocale();
        parent::__construct($filesystem, $data);
    }

    /**
     * Check if gift card type is combined
     *
     * @param Mage_Catalog_Model_Product $product
     * @return bool
     */
    public function isTypeCombined($product)
    {
        if ($product->getGiftcardType() == Enterprise_GiftCard_Model_Giftcard::TYPE_COMBINED) {
            return true;
        }
        return false;
    }

    /**
     * Check if gift card type is physical
     *
     * @param Mage_Catalog_Model_Product $product
     * @return bool
     */
    public function isTypePhysical($product)
    {
        if ($product->getGiftcardType() == Enterprise_GiftCard_Model_Giftcard::TYPE_PHYSICAL) {
            return true;
        }
        return false;
    }

    /**
     * Check if gift card type is virtual
     *
     * @param Mage_Catalog_Model_Product $product
     * @return bool
     */
    public function isTypeVirtual($product)
    {
        if ($product->getGiftcardType() == Enterprise_GiftCard_Model_Giftcard::TYPE_VIRTUAL) {
            return true;
        }
        return false;
    }

    /**
     * Check if gift card is virtual product
     *
     * @param Mage_Catalog_Model_Product $product
     * @return bool
     */
    public function isVirtual($product)
    {
        return $product->getGiftcardType() == Enterprise_GiftCard_Model_Giftcard::TYPE_VIRTUAL;
    }

    /**
     * Check if product is available for sale
     *
     * @param Mage_Catalog_Model_Product $product
     * @return bool
     */
    public function isSalable($product)
    {
        $amounts = $product->getPriceModel()->getAmounts($product);
        $open = $product->getAllowOpenAmount();

        if (!$open && !$amounts) {
            return false;
        }

        return parent::isSalable($product);
    }

    /**
     * Prepare product and its configuration to be added to some products list.
     * Use standard preparation process and also add specific giftcard options.
     *
     * @param Varien_Object $buyRequest
     * @param Mage_Catalog_Model_Product $product
     * @param string $processMode
     * @return array|string
     */
    protected function _prepareProduct(Varien_Object $buyRequest, $product, $processMode)
    {
        $result = parent::_prepareProduct($buyRequest, $product, $processMode);

        if (is_string($result)) {
            return $result;
        }

        try {
            $amount = $this->_validate($buyRequest, $product, $processMode);
        } catch (Mage_Core_Exception $e) {
            return $e->getMessage();
        } catch (Exception $e) {
            Mage::logException($e);
            return __('Something went wrong  preparing the gift card.');
        }

        $product->addCustomOption('giftcard_amount', $amount, $product);
        $product->addCustomOption('giftcard_sender_name', $buyRequest->getGiftcardSenderName(), $product);
        $product->addCustomOption('giftcard_recipient_name', $buyRequest->getGiftcardRecipientName(), $product);
        if (!$this->isTypePhysical($product)) {
            $product->addCustomOption('giftcard_sender_email', $buyRequest->getGiftcardSenderEmail(), $product);
            $product->addCustomOption('giftcard_recipient_email', $buyRequest->getGiftcardRecipientEmail(), $product);
        }

        $messageAllowed = false;
        if ($product->getUseConfigAllowMessage()) {
            $messageAllowed = Mage::getStoreConfigFlag(Enterprise_GiftCard_Model_Giftcard::XML_PATH_ALLOW_MESSAGE);
        } else {
            $messageAllowed = (int) $product->getAllowMessage();
        }

        if ($messageAllowed) {
            $product->addCustomOption('giftcard_message', $buyRequest->getGiftcardMessage(), $product);
        }

        return $result;
    }

    /**
     * Validate Gift Card product, determine and return its amount
     *
     * @param Varien_Object $buyRequest
     * @param Mage_Catalog_Model_Product $product
     * @param bool $processMode
     * @return double|float|mixed
     */
    private function _validate(Varien_Object $buyRequest, $product, $processMode)
    {
        $isStrictProcessMode = $this->_isStrictProcessMode($processMode);
        $allowedAmounts = $this->_getAllowedAmounts($product);
        $allowOpen = $product->getAllowOpenAmount();
        $selectedAmount = $buyRequest->getGiftcardAmount();
        $customAmount = $this->_getCustomGiftcardAmount($buyRequest);
        $this->_checkFields($buyRequest, $product, $isStrictProcessMode);

        $amount = null;
        if (($selectedAmount == 'custom' || !$selectedAmount) && $allowOpen) {
            if ($customAmount <= 0 && $isStrictProcessMode) {
                Mage::throwException(
                    __('Please specify a gift card amount.')
                );
            }
            $amount = $this->_getAmountWithinConstraints($product, $customAmount, $isStrictProcessMode);
        } elseif (is_numeric($selectedAmount)) {
            if (in_array($selectedAmount, $allowedAmounts)) {
                $amount = $selectedAmount;
            }
        }

        $amount = $this->_getAmountFromAllowed($amount, $allowedAmounts);

        if ($isStrictProcessMode) {
            $this->_checkGiftcardFields($buyRequest, $this->isTypePhysical($product), $amount);
        }
        return $amount;
    }

    /**
     * Get allowed giftcard amounts
     *
     * @param Mage_Catalog_Model_Product $product
     * @return array
     */
    protected function _getAllowedAmounts($product)
    {
        if (is_null($this->_giftcardAmounts)) {
            $allowedAmounts = array();
            foreach ($product->getGiftcardAmounts() as $value) {
                $allowedAmounts[] = $this->_store->roundPrice($value['website_value']);
            }
            $this->_giftcardAmounts = $allowedAmounts;
        }
        return $this->_giftcardAmounts;
    }

    /**
     * Get custom amount if null
     *
     * @param $amount
     * @param array $allowedAmounts
     * @return mixed|null
     */
    protected function _getAmountFromAllowed($amount, $allowedAmounts)
    {
        if (is_null($amount)) {
            if (count($allowedAmounts) == 1) {
                $amount = array_shift($allowedAmounts);
            }
        }
        return $amount;
    }

    /**
     * Check and count empty fields
     *
     * @param Varien_Object $buyRequest
     * @param Mage_Catalog_Model_Product $product
     * @param bool $isStrictProcessMode
     */
    protected function _checkFields($buyRequest, $product, $isStrictProcessMode)
    {
        $emptyFields = $this->_countEmptyFields($buyRequest, $product);
        $selectedAmount = $buyRequest->getGiftcardAmount();
        $allowOpen = $product->getAllowOpenAmount();
        $allowedAmounts = $this->_getAllowedAmounts($product);
        $customAmount = $this->_getCustomGiftcardAmount($buyRequest);

        if (($selectedAmount == 'custom' || !$selectedAmount) && $allowOpen && $customAmount <= 0) {
            $emptyFields++;
        } elseif (is_numeric($selectedAmount)) {
            if (!in_array($selectedAmount, $allowedAmounts)) {
                $emptyFields++;
            }
        } elseif (count($allowedAmounts) != 1) {
            $emptyFields++;
        }

        if ($emptyFields > 1 && $isStrictProcessMode) {
            Mage::throwException(
                __('Please specify all the required information.')
            );
        }
    }

    /**
     * Count empty fields
     *
     * @param Varien_Object $buyRequest
     * @param Mage_Catalog_Model_Product $product
     * @return int
     */
    protected function _countEmptyFields($buyRequest, $product)
    {
        $count = 0;
        if (!$buyRequest->getGiftcardRecipientName()) {
            $count++;
        }
        if (!$buyRequest->getGiftcardSenderName()) {
            $count++;
        }

        if (!$this->isTypePhysical($product)) {
            if (!$buyRequest->getGiftcardRecipientEmail()) {
                $count++;
            }
            if (!$buyRequest->getGiftcardSenderEmail()) {
                $count++;
            }
        }
        return $count;
    }

    /**
     * Check whether amount is appropriate
     *
     * @param Mage_Catalog_Model_Product $product
     * @param int $customAmount
     * @param bool $isStrict
     * @return int|void
     */
    protected function _getAmountWithinConstraints($product, $customAmount, $isStrict)
    {
        $minAmount = $product->getOpenAmountMin();
        $maxAmount = $product->getOpenAmountMax();
        if (!$minAmount || $minAmount && $customAmount >= $minAmount) {
            if (!$maxAmount || $maxAmount && $customAmount <= $maxAmount) {
                return $customAmount;
            } elseif ($customAmount > $maxAmount && $isStrict) {
                $messageAmount = $this->_helper('Mage_Core_Helper_Data')->currency($maxAmount, true, false);
                Mage::throwException(
                    __('Gift Card max amount is %s', $messageAmount)
                );
            }
        } elseif ($customAmount < $minAmount && $isStrict) {
            $messageAmount = $this->_helper('Mage_Core_Helper_Data')->currency($minAmount, true, false);
            Mage::throwException(
                __('Gift Card min amount is %s', $messageAmount)
            );
        }
    }

    /**
     * Fields check
     *
     * @param Varien_Object $buyRequest
     * @param bool $isPhysical
     * @param int $amount
     */
    protected function _checkGiftcardFields($buyRequest, $isPhysical, $amount)
    {
        if (is_null($amount)) {
            Mage::throwException(
                __('Please specify a gift card amount.')
            );
        }
        if (!$buyRequest->getGiftcardRecipientName()) {
            Mage::throwException(
                __('Please specify a recipient name.')
            );
        }
        if (!$buyRequest->getGiftcardSenderName()) {
            Mage::throwException(
                __('Please specify a sender name.')
            );
        }

        if (!$isPhysical) {
            if (!$buyRequest->getGiftcardRecipientEmail()) {
                Mage::throwException(
                    __('Please specify a recipient email.')
                );
            }
            if (!$buyRequest->getGiftcardSenderEmail()) {
                Mage::throwException(
                    __('Please specify a sender email.')
                );
            }
        }
    }

    /**
     * Get giftcard custom amount
     *
     * @param Varien_Object $buyRequest
     * @return int|null
     */
    protected function _getCustomGiftcardAmount($buyRequest)
    {
        $customAmount = $buyRequest->getCustomGiftcardAmount();
        $rate = $this->_store->getCurrentCurrencyRate();
        if ($rate != 1 && $customAmount) {
            $customAmount = $this->_locale->getNumber($customAmount);
            if (is_numeric($customAmount) && $customAmount) {
                $customAmount = $this->_store->roundPrice($customAmount / $rate);
            }
        }
        return $customAmount;
    }

    /**
     * Check if product can be bought
     *
     * @param  Mage_Catalog_Model_Product $product
     * @return Mage_Catalog_Model_Product_Type_Abstract
     * @throws Mage_Core_Exception
     */
    public function checkProductBuyState($product)
    {
        parent::checkProductBuyState($product);
        $option = $product->getCustomOption('info_buyRequest');
        if ($option instanceof Mage_Sales_Model_Quote_Item_Option) {
            $buyRequest = new Varien_Object(unserialize($option->getValue()));
            $this->_validate($buyRequest, $product, self::PROCESS_MODE_FULL);
        }
        return $this;
    }


    /**
     * Sets flag that product has required options, because gift card always
     * has some required options, at least - recipient name
     *
     * @param Mage_Catalog_Model_Product $product
     * @return Enterprise_GiftCard_Model_Catalog_Product_Type_Giftcard
     */
    public function beforeSave($product)
    {
        parent::beforeSave($product);
        $product->setTypeHasOptions(true);
        $product->setTypeHasRequiredOptions(true);
        return $this;
    }

    /**
     * Prepare selected options for giftcard
     *
     * @param  Mage_Catalog_Model_Product $product
     * @param  Varien_Object $buyRequest
     * @return array
     */
    public function processBuyRequest($product, $buyRequest)
    {
        $options = array(
            'giftcard_amount'         => $buyRequest->getGiftcardAmount(),
            'custom_giftcard_amount'  => $buyRequest->getCustomGiftcardAmount(),
            'giftcard_sender_name'    => $buyRequest->getGiftcardSenderName(),
            'giftcard_sender_email'    => $buyRequest->getGiftcardSenderEmail(),
            'giftcard_recipient_name' => $buyRequest->getGiftcardRecipientName(),
            'giftcard_recipient_email' => $buyRequest->getGiftcardRecipientEmail(),
            'giftcard_message'        => $buyRequest->getGiftcardMessage()
        );

        return $options;
    }

    /**
     * Delete data specific for Gift Card product type
     *
     * @param Mage_Catalog_Model_Product $product
     */
    public function deleteTypeSpecificData(Mage_Catalog_Model_Product $product)
    {
    }
}
