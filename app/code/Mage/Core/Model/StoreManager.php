<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
class Mage_Core_Model_StoreManager implements Mage_Core_Model_StoreManagerInterface
{
    /**
     * Store storage factory model
     *
     * @var Mage_Core_Model_Store_StorageFactory
     */
    protected $_factory;

    /**
     * Event manager
     *
     * @var Mage_Core_Model_Event_Manager
     */
    protected $_eventManager;

    /**
     * Request model
     *
     * @var Mage_Core_Controller_Request_Http
     */
    protected $_request;

    /**
     * Default store code
     *
     * @var string
     */
    protected $_currentStore = null;

    /**
     * Flag is single store mode allowed
     *
     * @var bool
     */
    protected $_isSingleStoreAllowed = true;

    /**
     * Requested scope code
     *
     * @var string
     */
    protected $_scopeCode;

    /**
     * Requested scope type
     *
     * @var string
     */
    protected $_scopeType;

    /**
     * Helper factory
     *
     * @var Mage_Core_Model_Factory_Helper
     */
    protected $_helperFactory;

    /**
     * @param Mage_Core_Model_Store_StorageFactory $factory
     * @param Mage_Core_Controller_Request_Http $request
     * @param Mage_Core_Model_Factory_Helper $helperFactory
     * @param string $scopeCode
     * @param string $scopeType
     */
    public function __construct(
        Mage_Core_Model_Store_StorageFactory $factory,
        Mage_Core_Controller_Request_Http $request,
        Mage_Core_Model_Factory_Helper $helperFactory,
        $scopeCode = '',
        $scopeType = 'store'
    ) {
        $this->_factory = $factory;
        $this->_request = $request;
        $this->_scopeCode = $scopeCode;
        $this->_scopeType = $scopeType ?: self::SCOPE_TYPE_STORE;
        $this->_helperFactory = $helperFactory;
    }

    /**
     * Get storage instance
     *
     * @return Mage_Core_Model_Store_StorageInterface
     */
    protected function _getStorage()
    {
        $arguments = array(
            'isSingleStoreAllowed' => $this->_isSingleStoreAllowed,
            'currentStore' => $this->_currentStore,
            'scopeCode' => $this->_scopeCode,
            'scopeType' => $this->_scopeType,
        );
        return $this->_factory->get($arguments);
    }

    /**
     * Retrieve application store object without Store_Exception
     *
     * @param string|int|Mage_Core_Model_Store $storeId
     * @return Mage_Core_Model_Store
     */
    public function getSafeStore($storeId = null)
    {
        try {
            return $this->getStore($storeId);
        } catch (Exception $e) {
            if ($this->_getStorage()->getCurrentStore()) {
                $this->_request->setActionName('noRoute');
                return new Magento_Object();
            }

            Mage::throwException(
                __('Requested invalid store "%s"', $storeId)
            );
        }
    }

    /**
     * Set current default store
     *
     * @param string $store
     */
    public function setCurrentStore($store)
    {
        $this->_currentStore = $store;
        $this->_getStorage()->setCurrentStore($store);
    }

    /**
     * @throws Mage_Core_Model_Store_Exception
     */
    public function throwStoreException()
    {
        $this->_getStorage()->throwStoreException();
    }

    /**
     * Allow or disallow single store mode
     *
     * @param bool $value
     */
    public function setIsSingleStoreModeAllowed($value)
    {
        $this->_isSingleStoreAllowed = $value;
        $this->_getStorage()->setIsSingleStoreModeAllowed($value);
    }

    /**
     * Check if store has only one store view
     *
     * @return bool
     */
    public function hasSingleStore()
    {
        return $this->_getStorage()->hasSingleStore();
    }

    /**
     * Check if system is run in the single store mode
     *
     * @return bool
     */
    public function isSingleStoreMode()
    {
        /** @var $helper Mage_Core_Helper_Data */
        $helper =  $this->_helperFactory->get('Mage_Core_Helper_Data');
        return $this->hasSingleStore() && $helper->isSingleStoreModeEnabled();
    }

    /**
     * Retrieve application store object
     *
     * @param null|string|bool|int|Mage_Core_Model_Store $storeId
     * @return Mage_Core_Model_Store
     * @throws Mage_Core_Model_Store_Exception
     */
    public function getStore($storeId = null)
    {
        return $this->_getStorage()->getStore($storeId);
    }

    /**
     * Retrieve stores array
     *
     * @param bool $withDefault
     * @param bool $codeKey
     * @return Mage_Core_Model_Store[]
     */
    public function getStores($withDefault = false, $codeKey = false)
    {
        return $this->_getStorage()->getStores($withDefault, $codeKey);
    }

    /**
     * Retrieve application website object
     *
     * @param null|bool|int|string|Mage_Core_Model_Website $websiteId
     * @return Mage_Core_Model_Website
     * @throws Mage_Core_Exception
     */
    public function getWebsite($websiteId = null)
    {
        return $this->_getStorage()->getWebsite($websiteId);
    }

    /**
     * Get loaded websites
     *
     * @param bool $withDefault
     * @param bool|string $codeKey
     * @return Mage_Core_Model_Website[]
     */
    public function getWebsites($withDefault = false, $codeKey = false)
    {
        return $this->_getStorage()->getWebsites($withDefault, $codeKey);
    }

    /**
     * Reinitialize store list
     */
    public function reinitStores()
    {
        $this->_getStorage()->reinitStores();
    }

    /**
     * Retrieve default store for default group and website
     *
     * @return Mage_Core_Model_Store
     */
    public function getDefaultStoreView()
    {
        return $this->_getStorage()->getDefaultStoreView();
    }

    /**
     * Retrieve application store group object
     *
     * @param null|Mage_Core_Model_Store_Group|string $groupId
     * @return Mage_Core_Model_Store_Group
     * @throws Mage_Core_Exception
     */
    public function getGroup($groupId = null)
    {
        return $this->_getStorage()->getGroup($groupId);
    }

    /**
     * Prepare array of store groups
     * can be filtered to contain default store group or not by $withDefault flag
     * depending on flag $codeKey array keys can be group id or group code
     *
     * @param bool $withDefault
     * @param bool $codeKey
     * @return Mage_Core_Model_Store_Group[]
     */
    public function getGroups($withDefault = false, $codeKey = false)
    {
        return $this->_getStorage()->getGroups($withDefault, $codeKey);
    }

    /**
     *  Unset website by id from app cache
     *
     * @param null|bool|int|string|Mage_Core_Model_Website $websiteId
     */
    public function clearWebsiteCache($websiteId = null)
    {
        $this->_getStorage()->clearWebsiteCache($websiteId);
    }

    /**
     * Get either default or any store view
     *
     * @return Mage_Core_Model_Store|null
     */
    public function getAnyStoreView()
    {
        return $this->_getStorage()->getAnyStoreView();
    }

    /**
     * Get current store code
     *
     * @return string
     */
    public function getCurrentStore()
    {
        return $this->_getStorage()->getCurrentStore();
    }
}
