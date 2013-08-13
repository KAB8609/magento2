<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Backend event observer
 */
class Magento_Backend_Model_Observer
{
    /**
     * Bind locale
     *
     * @param Magento_Event_Observer $observer
     * @return Magento_Backend_Model_Observer
     */
    public function bindLocale($observer)
    {
        $locale = $observer->getEvent()->getLocale();
        if ($locale) {
            $selectedLocale = Mage::getSingleton('Magento_Backend_Model_Session')->getLocale();
            if ($selectedLocale) {
                $locale->setLocaleCode($selectedLocale);
            }
        }
        return $this;
    }

    /**
     * Prepare mass action separated data
     *
     * @return Magento_Backend_Model_Observer
     */
    public function massactionPrepareKey()
    {
        $request = Mage::app()->getFrontController()->getRequest();
        $key = $request->getPost('massaction_prepare_key');
        if ($key) {
            $postData = $request->getPost($key);
            $value = is_array($postData) ? $postData : explode(',', $postData);
            $request->setPost($key, $value ? $value : null);
        }
        return $this;
    }

    /**
     * Clear result of configuration files access level verification in system cache
     *
     * @return Magento_Backend_Model_Observer
     */
    public function clearCacheConfigurationFilesAccessLevelVerification()
    {
        return $this;
    }

    /**
     * Backend will always use base class for translation.
     *
     * @return Magento_Backend_Model_Observer
     */
    public function initializeTranslation()
    {
        return $this;
    }

    /**
     * Set url class name for store 'admin'
     *
     * @param Magento_Event_Observer $observer
     * @return Magento_Backend_Model_Observer
     */
    public function setUrlClassName(Magento_Event_Observer $observer)
    {
        /** @var $storeCollection Magento_Core_Model_Resource_Store_Collection */
        $storeCollection = $observer->getEvent()->getStoreCollection();
        /** @var $store Magento_Core_Model_Store */
        foreach ($storeCollection as $store) {
            if ($store->getId() == 0) {
                $store->setUrlClassName('Magento_Backend_Model_Url');
                break;
            }
        }

        Mage::app()->removeCache(Magento_AdminNotification_Model_System_Message_Security::VERIFICATION_RESULT_CACHE_KEY);
        return $this;
    }
}
