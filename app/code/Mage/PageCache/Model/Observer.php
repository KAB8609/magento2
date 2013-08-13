<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Magento_PageCache
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Page cache observer model
 *
 * @category    Mage
 * @package     Magento_PageCache
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_PageCache_Model_Observer
{
    const XML_NODE_ALLOWED_CACHE = 'frontend/cache/allowed_requests';

    /**
     * Retrieve the helper instance
     *
     * @return Magento_PageCache_Helper_Data
     */
    protected function _getHelper()
    {
        return Mage::helper('Magento_PageCache_Helper_Data');
    }

    /**
     * Check if full page cache is enabled
     *
     * @return bool
     */
    public function isCacheEnabled()
    {
        return $this->_getHelper()->isEnabled();
    }

    /**
     * Check when cache should be disabled
     *
     * @param Magento_Event_Observer $observer
     * @return Magento_PageCache_Model_Observer
     */
    public function processPreDispatch(Magento_Event_Observer $observer)
    {
        if (!$this->isCacheEnabled()) {
            return $this;
        }
        $action = $observer->getEvent()->getControllerAction();
        $request = $action->getRequest();
        $needCaching = true;

        if ($request->isPost()) {
            $needCaching = false;
        }

        $configuration = Mage::getConfig()->getNode(self::XML_NODE_ALLOWED_CACHE);

        if (!$configuration) {
            $needCaching = false;
        }

        $configuration = $configuration->asArray();
        $module = $request->getModuleName();
        $controller = $request->getControllerName();
        $action = $request->getActionName();

        if (!isset($configuration[$module])) {
            $needCaching = false;
        }

        if (isset($configuration[$module]['controller']) && $configuration[$module]['controller'] != $controller) {
            $needCaching = false;
        }

        if (isset($configuration[$module]['action']) && $configuration[$module]['action'] != $action) {
            $needCaching = false;
        }

        if (!$needCaching) {
            $this->_getHelper()->setNoCacheCookie();
        }

        return $this;
    }

    /**
     * Temporary disabling full page caching by setting bo-cache cookie
     *
     * @param Magento_Event_Observer $observer
     * @return Magento_PageCache_Model_Observer
     */
    public function setNoCacheCookie(Magento_Event_Observer $observer)
    {
        if (!$this->isCacheEnabled()) {
            return $this;
        }
        $this->_getHelper()->setNoCacheCookie(0)->lockNoCacheCookie();
        return $this;
    }

    /**
     * Activating full page cache aby deleting no-cache cookie
     *
     * @param Magento_Event_Observer $observer
     * @return Magento_PageCache_Model_Observer
     */
    public function deleteNoCacheCookie(Magento_Event_Observer $observer)
    {
        if (!$this->isCacheEnabled()) {
            return $this;
        }
        $this->_getHelper()->unlockNoCacheCookie()->removeNoCacheCookie();
        return $this;
    }
}
