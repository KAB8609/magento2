<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Shipping
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Shipping data helper
 */
class Mage_Shipping_Helper_Data extends Magento_Core_Helper_Abstract
{
    /**
     * Allowed hash keys
     *
     * @var array
     */
    protected $_allowedHashKeys = array('ship_id', 'order_id', 'track_id');

    /**
     * Decode url hash
     *
     * @param  string $hash
     * @return array
     */
    public function decodeTrackingHash($hash)
    {
        $hash = explode(':', Mage::helper('Magento_Core_Helper_Data')->urlDecode($hash));
        if (count($hash) === 3 && in_array($hash[0], $this->_allowedHashKeys)) {
            return array('key' => $hash[0], 'id' => (int)$hash[1], 'hash' => $hash[2]);
        }
        return array();
    }

    /**
     * Retrieve tracking url with params
     *
     * @param  string $key
     * @param  Magento_Sales_Model_Order|Magento_Sales_Model_Order_Shipment|Magento_Sales_Model_Order_Shipment_Track $model
     * @param  string $method Optional - method of a model to get id
     * @return string
     */
    protected function _getTrackingUrl($key, $model, $method = 'getId')
    {
        $helper = Mage::helper('Magento_Core_Helper_Data');
        $urlPart = "{$key}:{$model->$method()}:{$model->getProtectCode()}";
        $param = array('hash' => $helper->urlEncode($urlPart));

        $storeModel = Mage::app()->getStore($model->getStoreId());
        return $storeModel->getUrl('shipping/tracking/popup', $param);
    }

    /**
     * Shipping tracking popup URL getter
     *
     * @param Magento_Sales_Model_Abstract $model
     * @return string
     */
    public function getTrackingPopupUrlBySalesModel($model)
    {
        if ($model instanceof Magento_Sales_Model_Order) {
            return $this->_getTrackingUrl('order_id', $model);
        } elseif ($model instanceof Magento_Sales_Model_Order_Shipment) {
            return $this->_getTrackingUrl('ship_id', $model);
        } elseif ($model instanceof Magento_Sales_Model_Order_Shipment_Track) {
            return $this->_getTrackingUrl('track_id', $model, 'getEntityId');
        }
        return '';
    }

    /**
     * Retrieve tracking ajax url
     *
     * @return string
     */
    public function getTrackingAjaxUrl()
    {
        return $this->_getUrl('shipping/tracking/ajax');
    }

    public function isFreeMethod($method, $storeId = null)
    {
        $arr = explode('_', $method, 2);
        if (!isset($arr[1])) {
            return false;
        }
        $freeMethod = Mage::getStoreConfig('carriers/' . $arr[0] . '/free_method', $storeId);
        return $freeMethod == $arr[1];
    }
}
