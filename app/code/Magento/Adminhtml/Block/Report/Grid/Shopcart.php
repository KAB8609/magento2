<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml shopping carts report grid block
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Adminhtml_Block_Report_Grid_Shopcart extends Magento_Backend_Block_Widget_Grid_Extended
{
    /**
     * stores current currency code
     */
    protected $_currentCurrencyCode = null;

    /**
     * ids of current stores
     */
    protected $_storeIds            = array();

    /**
     * storeIds setter
     *
     * @param  array $storeIds
     * @return Magento_Adminhtml_Block_Report_Grid_Shopcart_Abstract
     */
    public function setStoreIds($storeIds)
    {
        $this->_storeIds = $storeIds;
        return $this;
    }

    /**
     * Retrieve currency code based on selected store
     *
     * @return string
     */
    public function getCurrentCurrencyCode()
    {
        if (is_null($this->_currentCurrencyCode)) {
            reset($this->_storeIds);
            $this->_currentCurrencyCode = (count($this->_storeIds) > 0)
                ? $this->_storeManager->getStore(current($this->_storeIds))->getBaseCurrencyCode()
                : $this->_storeManager->getStore()->getBaseCurrencyCode();
        }
        return $this->_currentCurrencyCode;
    }

    /**
     * Get currency rate (base to given currency)
     *
     * @param string|Magento_Directory_Model_Currency $currencyCode
     * @return double
     */
    public function getRate($toCurrency)
    {
        return $this->_storeManager->getStore()->getBaseCurrency()->getRate($toCurrency);
    }
}
