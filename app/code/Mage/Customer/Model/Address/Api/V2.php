<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Customer
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Customer address api V2
 *
 * @category   Mage
 * @package    Mage_Customer
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Customer_Model_Address_Api_V2 extends Mage_Customer_Model_Address_Api
{
    /**
     * Create new address for customer
     *
     * @param int $customerId
     * @param array $addressData
     * @return int
     */
    public function create($customerId, $addressData)
    {
        $customer = Mage::getModel('Mage_Customer_Model_Customer')
            ->load($customerId);
        /* @var $customer Mage_Customer_Model_Customer */

        if (!$customer->getId()) {
            $this->_fault('customer_not_exists');
        }

        $address = Mage::getModel('Mage_Customer_Model_Address');

        foreach ($this->getAllowedAttributes($address) as $attributeCode=>$attribute) {
            if (isset($addressData->$attributeCode)) {
                $address->setData($attributeCode, $addressData->$attributeCode);
            }
        }

        $address->setCustomerId($customer->getId());

        $valid = $address->validate();

        if (is_array($valid)) {
            $this->_fault('data_invalid', implode("\n", $valid));
        }

        try {
            $address->save();
            $this->_saveDefaultAddresses($addressData, $address);
        } catch (Mage_Core_Exception $e) {
            $this->_fault('data_invalid', $e->getMessage());
        }

        return $address->getId();
    }

    /**
     * Retrieve address data
     *
     * @param int $addressId
     * @return array
     */
    public function info($addressId)
    {
        $address = Mage::getModel('Mage_Customer_Model_Address')
            ->load($addressId);

        if (!$address->getId()) {
            $this->_fault('not_exists');
        }

        $result = array();

        foreach ($this->_mapAttributes as $attributeAlias => $attributeCode) {
            $result[$attributeAlias] = $address->getData($attributeCode);
        }

        foreach ($this->getAllowedAttributes($address) as $attributeCode => $attribute) {
            $result[$attributeCode] = $address->getData($attributeCode);
        }


        if ($customer = $address->getCustomer()) {
            $result['is_default_billing']  = $customer->getDefaultBilling() == $address->getId();
            $result['is_default_shipping'] = $customer->getDefaultShipping() == $address->getId();
        }

        return $result;
    }

    /**
     * Update address data
     *
     * @param int $addressId
     * @param array $addressData
     * @return boolean
     */
    public function update($addressId, $addressData)
    {
        /** @var $address Mage_Customer_Model_Address */
        $address = Mage::getModel('Mage_Customer_Model_Address')->load($addressId);

        if (!$address->getId()) {
            $this->_fault('not_exists');
        }

        foreach ($this->getAllowedAttributes($address) as $attributeCode=>$attribute) {
            if (isset($addressData->$attributeCode)) {
                $address->setData($attributeCode, $addressData->$attributeCode);
            }
        }

        $valid = $address->validate();
        if (is_array($valid)) {
            $this->_fault('data_invalid', implode("\n", $valid));
        }

        try {
            $address->save();
            $this->_saveDefaultAddresses($addressData, $address);
        } catch (Mage_Core_Exception $e) {
            $this->_fault('data_invalid', $e->getMessage());
        }

        return true;
    }

    /**
     * Process default billing and shipping addresses.
     *
     * @param object $addressData
     * @param Mage_Customer_Model_Address $address
     */
    protected function _saveDefaultAddresses($addressData, $address)
    {
        if (isset($addressData->is_default_billing) || isset($addressData->is_default_shipping)) {
            $customer = $address->getCustomer();
            if (isset($addressData->is_default_billing)) {
                if ($addressData->is_default_billing) {
                    $customer->setDefaultBilling($address->getId());
                } else {
                    $customer->setDefaultBilling(null);
                }
            }
            if (isset($addressData->is_default_shipping)) {
                if ($addressData->is_default_shipping) {
                    $customer->setDefaultShipping($address->getId());
                } else {
                    $customer->setDefaultShipping(null);
                }
            }
            $customer->save();
        }
    }
} // Class Mage_Customer_Model_Address_Api End
