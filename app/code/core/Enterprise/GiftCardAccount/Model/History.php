<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_GiftCardAccount
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Enter description here ...
 *
 * @method Enterprise_GiftCardAccount_Model_Resource_History _getResource()
 * @method Enterprise_GiftCardAccount_Model_Resource_History getResource()
 * @method int getGiftcardaccountId()
 * @method Enterprise_GiftCardAccount_Model_History setGiftcardaccountId(int $value)
 * @method string getUpdatedAt()
 * @method Enterprise_GiftCardAccount_Model_History setUpdatedAt(string $value)
 * @method int getAction()
 * @method Enterprise_GiftCardAccount_Model_History setAction(int $value)
 * @method float getBalanceAmount()
 * @method Enterprise_GiftCardAccount_Model_History setBalanceAmount(float $value)
 * @method float getBalanceDelta()
 * @method Enterprise_GiftCardAccount_Model_History setBalanceDelta(float $value)
 * @method string getAdditionalInfo()
 * @method Enterprise_GiftCardAccount_Model_History setAdditionalInfo(string $value)
 *
 * @category    Enterprise
 * @package     Enterprise_GiftCardAccount
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_GiftCardAccount_Model_History extends Mage_Core_Model_Abstract
{
    const ACTION_CREATED  = 0;
    const ACTION_USED     = 1;
    const ACTION_SENT     = 2;
    const ACTION_REDEEMED = 3;
    const ACTION_EXPIRED  = 4;
    const ACTION_UPDATED  = 5;

    protected function _construct()
    {
        $this->_init('Enterprise_GiftCardAccount_Model_Resource_History');
    }

    public function getActionNamesArray()
    {
        return array(
            self::ACTION_CREATED  => Mage::helper('Enterprise_GiftCardAccount_Helper_Data')->__('Created'),
            self::ACTION_UPDATED  => Mage::helper('Enterprise_GiftCardAccount_Helper_Data')->__('Updated'),
            self::ACTION_SENT     => Mage::helper('Enterprise_GiftCardAccount_Helper_Data')->__('Sent'),
            self::ACTION_USED     => Mage::helper('Enterprise_GiftCardAccount_Helper_Data')->__('Used'),
            self::ACTION_REDEEMED => Mage::helper('Enterprise_GiftCardAccount_Helper_Data')->__('Redeemed'),
            self::ACTION_EXPIRED  => Mage::helper('Enterprise_GiftCardAccount_Helper_Data')->__('Expired'),
        );
    }

    protected function _getCreatedAdditionalInfo()
    {
        if ($this->getGiftcardaccount()->getOrder()) {
            $orderId = $this->getGiftcardaccount()->getOrder()->getIncrementId();
            return Mage::helper('Enterprise_GiftCardAccount_Helper_Data')->__('Order #%s.', $orderId);
        } else if ($user = Mage::getSingleton('Mage_Backend_Model_Auth_Session')->getUser()) {
            $username = $user->getUsername();
            if ($username) {
                return Mage::helper('Enterprise_GiftCardAccount_Helper_Data')->__('By admin: %s.', $username);
            }
        }

        return '';
    }

    protected function _getUsedAdditionalInfo()
    {
        if ($this->getGiftcardaccount()->getOrder()) {
            $orderId = $this->getGiftcardaccount()->getOrder()->getIncrementId();
            return Mage::helper('Enterprise_GiftCardAccount_Helper_Data')->__('Order #%s.', $orderId);
        }

        return '';
    }

    protected function _getSentAdditionalInfo()
    {
        $recipient = $this->getGiftcardaccount()->getRecipientEmail();
        if ($name = $this->getGiftcardaccount()->getRecipientName()) {
            $recipient = "{$name} <{$recipient}>";
        }

        $sender = '';
        if ($user = Mage::getSingleton('Mage_Backend_Model_Auth_Session')->getUser()) {
            if ($user->getUsername()) {
                $sender = $user->getUsername();
            }
        }

        if ($sender) {
            return Mage::helper('Enterprise_GiftCardAccount_Helper_Data')->__('Recipient: %s. By admin: %s.', $recipient, $sender);
        } else {
            return Mage::helper('Enterprise_GiftCardAccount_Helper_Data')->__('Recipient: %s.', $recipient);
        }
    }

    protected function _getRedeemedAdditionalInfo()
    {
        if ($customerId = $this->getGiftcardaccount()->getCustomerId()) {
            return Mage::helper('Enterprise_GiftCardAccount_Helper_Data')->__('Customer #%s.', $customerId);
        }
        return '';
    }

    protected function _getUpdatedAdditionalInfo()
    {
        if ($user = Mage::getSingleton('Mage_Backend_Model_Auth_Session')->getUser()) {
            $username = $user->getUsername();
            if ($username) {
                return Mage::helper('Enterprise_GiftCardAccount_Helper_Data')->__('By admin: %s.', $username);
            }
        }
        return '';
    }

    protected function _getExpiredAdditionalInfo()
    {
        return '';
    }

    protected function _beforeSave()
    {
        if (!$this->hasGiftcardaccount()) {
            Mage::throwException(Mage::helper('Enterprise_GiftCardAccount_Helper_Data')->__('Please assign gift card account.'));
        }

        $this->setAction($this->getGiftcardaccount()->getHistoryAction());
        $this->setGiftcardaccountId($this->getGiftcardaccount()->getId());
        $this->setBalanceAmount($this->getGiftcardaccount()->getBalance());
        $this->setBalanceDelta($this->getGiftcardaccount()->getBalanceDelta());

        switch ($this->getGiftcardaccount()->getHistoryAction()) {
            case self::ACTION_CREATED:
                $this->setAdditionalInfo($this->_getCreatedAdditionalInfo());

                $this->setBalanceDelta($this->getBalanceAmount());
            break;
            case self::ACTION_USED:
                $this->setAdditionalInfo($this->_getUsedAdditionalInfo());
            break;
            case self::ACTION_SENT:
                $this->setAdditionalInfo($this->_getSentAdditionalInfo());
            break;
            case self::ACTION_REDEEMED:
                $this->setAdditionalInfo($this->_getRedeemedAdditionalInfo());
            break;
            case self::ACTION_UPDATED:
                $this->setAdditionalInfo($this->_getUpdatedAdditionalInfo());
            break;
            case self::ACTION_EXPIRED:
                $this->setAdditionalInfo($this->_getExpiredAdditionalInfo());
            break;            
            default:
                Mage::throwException(Mage::helper('Enterprise_GiftCardAccount_Helper_Data')->__('Unknown history action.'));
            break;
        }

        return parent::_beforeSave();
    }
}
