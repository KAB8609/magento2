<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_PageCache
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Page cache observer model
 *
 * @category    Magento
 * @package     Magento_PageCache
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\PageCache\Model;

class Observer
{
    const XML_NODE_ALLOWED_CACHE = 'frontend/cache/allowed_requests';

    /**
     * Page cache data
     *
     * @var \Magento\PageCache\Helper\Data
     */
    protected $_pageCacheData = null;

    /**
     * @var array
     */
    protected $_allowedCache;

    /**
     * @param \Magento\PageCache\Helper\Data $pageCacheData
     * @param array $allowedCache
     */
    public function __construct(\Magento\PageCache\Helper\Data $pageCacheData, array $allowedCache = array())
    {
        $this->_pageCacheData = $pageCacheData;
        $this->_allowedCache = $allowedCache;
    }

    /**
     * Check if full page cache is enabled
     *
     * @return bool
     */
    public function isCacheEnabled()
    {
        return $this->_pageCacheData->isEnabled();
    }

    /**
     * Check when cache should be disabled
     *
     * @param \Magento\Event\Observer $observer
     * @return \Magento\PageCache\Model\Observer
     */
    public function processPreDispatch(\Magento\Event\Observer $observer)
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

        if (empty($this->_allowedCache)) {
            $needCaching = false;
        }

        $module = $request->getModuleName();
        $controller = $request->getControllerName();
        $action = $request->getActionName();


        if (!isset($this->_allowedCache[$module])) {
            $needCaching = false;
        }

        if (isset($this->_allowedCache[$module]['controller'])
            && $this->_allowedCache[$module]['controller'] != $controller
        ) {
            $needCaching = false;
        }

        if (isset($this->_allowedCache[$module]['action']) && $this->_allowedCache[$module]['action'] != $action) {
            $needCaching = false;
        }

        if (!$needCaching) {
            $this->_pageCacheData->setNoCacheCookie();
        }

        return $this;
    }

    /**
     * Temporary disabling full page caching by setting bo-cache cookie
     *
     * @param \Magento\Event\Observer $observer
     * @return \Magento\PageCache\Model\Observer
     */
    public function setNoCacheCookie(\Magento\Event\Observer $observer)
    {
        if (!$this->isCacheEnabled()) {
            return $this;
        }
        $this->_pageCacheData->setNoCacheCookie(0)->lockNoCacheCookie();
        return $this;
    }

    /**
     * Activating full page cache aby deleting no-cache cookie
     *
     * @param \Magento\Event\Observer $observer
     * @return \Magento\PageCache\Model\Observer
     */
    public function deleteNoCacheCookie(\Magento\Event\Observer $observer)
    {
        if (!$this->isCacheEnabled()) {
            return $this;
        }
        $this->_pageCacheData->unlockNoCacheCookie()->removeNoCacheCookie();
        return $this;
    }
}
