<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Sales module base helper
 */
class Magento_Sales_Helper_Reorder extends Magento_Core_Helper_Data
{
    const XML_PATH_SALES_REORDER_ALLOW = 'sales/reorder/allow';

    /**
     * @var Magento_Customer_Model_Session
     */
    protected $_customerSession;

    /**
     * @param Magento_Core_Model_Event_Manager $eventManager
     * @param Magento_Core_Helper_Http $coreHttp
     * @param Magento_Core_Helper_Context $context
     * @param Magento_Core_Model_Config $config
     * @param Magento_Core_Model_Store_Config $coreStoreConfig
     * @param Magento_Core_Model_StoreManager $storeManager
     * @param Magento_Core_Model_Locale_Proxy $locale
     * @param Magento_Core_Model_Date_Proxy $dateModel
     * @param Magento_Core_Model_App_State $appState
     * @param Magento_Core_Model_Config_Resource $configResource
     * @param Magento_Customer_Model_Session $customerSession
     */
    public function __construct(
        Magento_Core_Model_Event_Manager $eventManager,
        Magento_Core_Helper_Http $coreHttp,
        Magento_Core_Helper_Context $context,
        Magento_Core_Model_Config $config,
        Magento_Core_Model_Store_Config $coreStoreConfig,
        Magento_Core_Model_StoreManager $storeManager,
        Magento_Core_Model_Locale_Proxy $locale,
        Magento_Core_Model_Date_Proxy $dateModel,
        Magento_Core_Model_App_State $appState,
        Magento_Core_Model_Config_Resource $configResource,
        Magento_Customer_Model_Session $customerSession
    ) {
        $this->_customerSession = $customerSession;
        parent::__construct(
            $eventManager,
            $coreHttp,
            $context,
            $config,
            $coreStoreConfig,
            $storeManager,
            $locale,
            $dateModel,
            $appState,
            $configResource
        );
    }

    /**
     * @return bool
     */
    public function isAllow()
    {
        return $this->isAllowed();
    }

    /**
     * Check if reorder is allowed for given store
     *
     * @param Magento_Core_Model_Store|int|null $store
     * @return bool
     */
    public function isAllowed($store = null)
    {
        if ($this->_coreStoreConfig->getConfig(self::XML_PATH_SALES_REORDER_ALLOW, $store)) {
            return true;
        }
        return false;
    }

    /**
     * @param Magento_Sales_Model_Order $order
     * @return bool
     */
    public function canReorder(Magento_Sales_Model_Order $order)
    {
        if (!$this->isAllowed($order->getStore())) {
            return false;
        }
        if ($this->_customerSession->isLoggedIn()) {
            return $order->canReorder();
        } else {
            return true;
        }
    }
}
