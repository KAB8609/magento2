<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Customer
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * API2 class for customer address (customer)
 *
 * @category   Mage
 * @package    Mage_Customer
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Customer_Model_Api2_Customer_Address_Rest_Customer_V1 extends Mage_Customer_Model_Api2_Customer_Address_Rest
{
    /**
     * Load customer address by id
     *
     * @param int $id
     * @throws Mage_Api2_Exception
     * @return Mage_Customer_Model_Address
     */
    protected function _loadCustomerAddressById($id)
    {
        /* @var $customerAddress Mage_Customer_Model_Address */
        $customerAddress = parent::_loadCustomerAddressById($id);
        // check owner
        if ($this->getApiUser()->getUserId() != $customerAddress->getCustomerId()) {
            $this->_critical(self::RESOURCE_NOT_FOUND);
        }
        return $customerAddress;
    }

    /**
     * Load customer by id
     *
     * @param int $id
     * @throws Mage_Api2_Exception
     * @return Mage_Customer_Model_Customer
     */
    protected function _loadCustomerById($id)
    {
        /* @var $customer Mage_Customer_Model_Customer */
        $customer = parent::_loadCustomerById($id);
        // check customer accaunt owner
        if ($this->getApiUser()->getUserId() != $customer->getId()) {
            $this->_critical(self::RESOURCE_NOT_FOUND);
        }
        return $customer;
    }
}