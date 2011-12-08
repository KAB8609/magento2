<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Ogone
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Mage_Ogone_Block_Placeform extends Mage_Core_Block_Template
{
    public function __construct()
    {
    }

    /**
     * Get checkout session namespace
     *
     * @return Mage_Checkout_Model_Session
     */
    public function getCheckout()
    {
        return Mage::getSingleton('Mage_Checkout_Model_Session');
    }

    /**
     * Ogone payment APi instance
     *
     * @return Mage_Ogone_Model_Api
     */
    protected function _getApi()
    {
        return Mage::getSingleton('Mage_Ogone_Model_Api');
    }

    /**
     * Return order instance with loaded onformation by increment id
     *
     * @return Mage_Sales_Model_Order
     */
    protected function _getOrder()
    {
        if ($this->getOrder()) {
            $order = $this->getOrder();
        } else if ($this->getCheckout()->getLastRealOrderId()) {
            $order = Mage::getModel('Mage_Sales_Model_Order')->loadByIncrementId($this->getCheckout()->getLastRealOrderId());
        } else {
            return null;
        }
        return $order;
    }

    /**
     * Get Form data by using ogone payment api
     *
     * @return array
     */
    public function getFormData()
    {
        return $this->_getApi()->getFormFields($this->_getOrder());
    }

    /**
     * Getting gateway url
     *
     * @return string
     */
    public function getFormAction()
    {
        return $this->_getApi()->getConfig()->getGatewayPath();
    }
}
