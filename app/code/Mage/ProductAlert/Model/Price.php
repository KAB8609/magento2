<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_ProductAlert
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * ProductAlert for changed price model
 *
 * @method Mage_ProductAlert_Model_Resource_Price _getResource()
 * @method Mage_ProductAlert_Model_Resource_Price getResource()
 * @method int getCustomerId()
 * @method Mage_ProductAlert_Model_Price setCustomerId(int $value)
 * @method int getProductId()
 * @method Mage_ProductAlert_Model_Price setProductId(int $value)
 * @method float getPrice()
 * @method Mage_ProductAlert_Model_Price setPrice(float $value)
 * @method int getWebsiteId()
 * @method Mage_ProductAlert_Model_Price setWebsiteId(int $value)
 * @method string getAddDate()
 * @method Mage_ProductAlert_Model_Price setAddDate(string $value)
 * @method string getLastSendDate()
 * @method Mage_ProductAlert_Model_Price setLastSendDate(string $value)
 * @method int getSendCount()
 * @method Mage_ProductAlert_Model_Price setSendCount(int $value)
 * @method int getStatus()
 * @method Mage_ProductAlert_Model_Price setStatus(int $value)
 *
 * @category    Mage
 * @package     Mage_ProductAlert
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_ProductAlert_Model_Price extends Magento_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('Mage_ProductAlert_Model_Resource_Price');
    }

    public function getCustomerCollection()
    {
        return Mage::getResourceModel('Mage_ProductAlert_Model_Resource_Price_Customer_Collection');
    }

    public function loadByParam()
    {
        if (!is_null($this->getProductId()) && !is_null($this->getCustomerId()) && !is_null($this->getWebsiteId())) {
            $this->getResource()->loadByParam($this);
        }
        return $this;
    }

    public function deleteCustomer($customerId, $websiteId = 0)
    {
        $this->getResource()->deleteCustomer($this, $customerId, $websiteId);
        return $this;
    }
}
