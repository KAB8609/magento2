<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftCard
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftCard\Block\Catalog\Product\View\Type;

class Giftcard extends \Magento\Catalog\Block\Product\View\AbstractView
{
    public function getAmountSettingsJson($product)
    {
        $result = array('min'=>0, 'max'=>0);
        if ($product->getAllowOpenAmount()) {
            if ($v = $product->getOpenAmountMin()) {
                $result['min'] = $v;
            }
            if ($v = $product->getOpenAmountMax()) {
                $result['max'] = $v;
            }
        }
        return $result;
    }

    public function isConfigured($product)
    {
        if (!$product->getAllowOpenAmount() && !$product->getGiftcardAmounts()) {
            return false;
        }
        return true;
    }

    public function isOpenAmountAvailable($product)
    {
        if (!$product->getAllowOpenAmount()) {
            return false;
        }
        return true;
    }

    public function isAmountAvailable($product)
    {
        if (!$product->getGiftcardAmounts()) {
            return false;
        }
        return true;
    }

    public function getAmounts($product)
    {
        $result = array();
        foreach ($product->getGiftcardAmounts() as $amount) {
            $result[] = \Mage::app()->getStore()->roundPrice($amount['website_value']);
        }
        sort($result);
        return $result;
    }

    public function getCurrentCurrency()
    {
        return \Mage::app()->getStore()->getCurrentCurrencyCode();
    }

    public function isMessageAvailable($product)
    {
        if ($product->getUseConfigAllowMessage()) {
            return \Mage::getStoreConfigFlag(\Magento\GiftCard\Model\Giftcard::XML_PATH_ALLOW_MESSAGE);
        } else {
            return (int) $product->getAllowMessage();
        }
    }

    public function isEmailAvailable($product)
    {
        if ($product->getTypeInstance()->isTypePhysical($product)) {
            return false;
        }
        return true;
    }

    public function getCustomerName()
    {
        $firstName = (string)\Mage::getSingleton('Magento\Customer\Model\Session')->getCustomer()->getFirstname();
        $lastName  = (string)\Mage::getSingleton('Magento\Customer\Model\Session')->getCustomer()->getLastname();

        if ($firstName && $lastName) {
            return $firstName . ' ' . $lastName;
        } else {
            return '';
        }
    }

    public function getCustomerEmail()
    {
        return (string) \Mage::getSingleton('Magento\Customer\Model\Session')->getCustomer()->getEmail();
    }

    public function getMessageMaxLength()
    {
        return (int) \Mage::getStoreConfig(\Magento\GiftCard\Model\Giftcard::XML_PATH_MESSAGE_MAX_LENGTH);
    }

    /**
     * Returns default value to show in input
     *
     * @param string $key
     * @return string
     */
    public function getDefaultValue($key)
    {
        return (string) $this->getProduct()->getPreconfiguredValues()->getData($key);
    }

    /**
     * Returns default sender name to show in input
     *
     * @return string
     */
    public function getDefaultSenderName()
    {
        $senderName = $this->getProduct()->getPreconfiguredValues()->getData('giftcard_sender_name');
        if (!strlen($senderName)) {
            $senderName = $this->getCustomerName();
        }
        return $senderName;
    }

    /**
     * Returns default sender email to show in input
     *
     * @return string
     */
    public function getDefaultSenderEmail()
    {
        $senderEmail = $this->getProduct()->getPreconfiguredValues()->getData('giftcard_sender_email');
        if (!strlen($senderEmail)) {
            $senderEmail = $this->getCustomerEmail();
        }
        return $senderEmail;
    }
}
