<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Shipping
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Shipping data helper
 */
namespace Magento\Shipping\Helper;

class Data extends \Magento\App\Helper\AbstractHelper
{
    /**
     * Allowed hash keys
     *
     * @var array
     */
    protected $_allowedHashKeys = array('ship_id', 'order_id', 'track_id');

    /**
     * Core data
     *
     * @var \Magento\Core\Helper\Data
     */
    protected $_coreData = null;

    /**
     * Core store config
     *
     * @var \Magento\Core\Model\Store\Config
     */
    protected $_coreStoreConfig;

    /**
     * @var \Magento\Core\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @param \Magento\App\Helper\Context $context
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Core\Model\Store\Config $coreStoreConfig
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\App\Helper\Context $context,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Core\Model\Store\Config $coreStoreConfig,
        \Magento\Core\Model\StoreManagerInterface $storeManager
    ) {
        $this->_coreData = $coreData;
        $this->_coreStoreConfig = $coreStoreConfig;
        $this->_storeManager = $storeManager;
        parent::__construct($context);
    }

    /**
     * Decode url hash
     *
     * @param  string $hash
     * @return array
     */
    public function decodeTrackingHash($hash)
    {
        $hash = explode(':', $this->_coreData->urlDecode($hash));
        if (count($hash) === 3 && in_array($hash[0], $this->_allowedHashKeys)) {
            return array('key' => $hash[0], 'id' => (int)$hash[1], 'hash' => $hash[2]);
        }
        return array();
    }

    /**
     * Retrieve tracking url with params
     *
     * @param  string $key
     * @param  \Magento\Sales\Model\Order|\Magento\Sales\Model\Order\Shipment|\Magento\Sales\Model\Order\Shipment\Track $model
     * @param  string $method Optional - method of a model to get id
     * @return string
     */
    protected function _getTrackingUrl($key, $model, $method = 'getId')
    {
        $urlPart = "{$key}:{$model->$method()}:{$model->getProtectCode()}";
        $param = array('hash' => $this->_coreData->urlEncode($urlPart));

        $storeModel = $this->_storeManager->getStore($model->getStoreId());
        return $storeModel->getUrl('shipping/tracking/popup', $param);
    }

    /**
     * Shipping tracking popup URL getter
     *
     * @param \Magento\Sales\Model\AbstractModel $model
     * @return string
     */
    public function getTrackingPopupUrlBySalesModel($model)
    {
        if ($model instanceof \Magento\Sales\Model\Order) {
            return $this->_getTrackingUrl('order_id', $model);
        } elseif ($model instanceof \Magento\Sales\Model\Order\Shipment) {
            return $this->_getTrackingUrl('ship_id', $model);
        } elseif ($model instanceof \Magento\Sales\Model\Order\Shipment\Track) {
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

    /**
     * @param string $method
     * @param mixed $storeId
     * @return bool
     */
    public function isFreeMethod($method, $storeId = null)
    {
        $arr = explode('_', $method, 2);
        if (!isset($arr[1])) {
            return false;
        }
        $freeMethod = $this->_coreStoreConfig->getConfig('carriers/' . $arr[0] . '/free_method', $storeId);
        return $freeMethod == $arr[1];
    }
}
