<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Backend event observer
 *
 * @category   Mage
 * @package    Mage_Backend
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Backend_Model_Observer
{

    public function bindLocale($observer)
    {
        if ($locale=$observer->getEvent()->getLocale()) {
            if ($choosedLocale = Mage::getSingleton('Mage_Backend_Model_Session')->getLocale()) {
                $locale->setLocaleCode($choosedLocale);
            }
        }
        return $this;
    }

    /**
     * Prepare massaction separated data
     *
     * @return Mage_Backend_Model_Observer
     */
    public function massactionPrepareKey()
    {
        $request = Mage::app()->getFrontController()->getRequest();
        if ($key = $request->getPost('massaction_prepare_key')) {
            $postData = $request->getPost($key);
            $value = is_array($postData) ? $postData : explode(',', $postData);
            $request->setPost($key, $value ? $value : null);
        }
        return $this;
    }

    /**
     * Clear result of configuration files access level verification in system cache
     *
     * @return Mage_Backend_Model_Observer
     */
    public function clearCacheConfigurationFilesAccessLevelVerification()
    {
        Mage::app()->removeCache(Mage_Backend_Model_System_Message_Security::VERIFICATION_RESULT_CACHE_KEY);
        return $this;
    }
}
