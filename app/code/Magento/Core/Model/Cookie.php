<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Core cookie model
 *
 * @category   Magento
 * @package    Magento_Core
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Core\Model;

class Cookie
{
    const XML_PATH_COOKIE_DOMAIN    = 'web/cookie/cookie_domain';
    const XML_PATH_COOKIE_PATH      = 'web/cookie/cookie_path';
    const XML_PATH_COOKIE_LIFETIME  = 'web/cookie/cookie_lifetime';
    const XML_PATH_COOKIE_HTTPONLY  = 'web/cookie/cookie_httponly';

    protected $_lifetime;

    /**
     * Store object
     *
     * @var \Magento\Core\Model\Store
     */
    protected $_store;

    /**
     * Set Store object
     *
     * @param mixed $store
     * @return \Magento\Core\Model\Cookie
     */
    public function setStore($store)
    {
        $this->_store = \Mage::app()->getStore($store);
        return $this;
    }

    /**
     * Retrieve Store object
     *
     * @return \Magento\Core\Model\Store
     */
    public function getStore()
    {
        if (is_null($this->_store)) {
            $this->_store = \Mage::app()->getStore();
        }
        return $this->_store;
    }

    /**
     * Retrieve Request object
     *
     * @return \Magento\Core\Controller\Request\Http
     */
    protected function _getRequest()
    {
        return \Mage::getObjectManager()->get('Magento\Core\Controller\Request\Http');
    }

    /**
     * Retrieve Response object
     *
     * @return \Magento\Core\Controller\Response\Http
     */
    protected function _getResponse()
    {
        return \Mage::getObjectManager()->get('Magento\Core\Controller\Response\Http');
    }

    /**
     * Retrieve Domain for cookie
     *
     * @return string
     */
    public function getDomain()
    {
        $domain = $this->getConfigDomain();
        if (empty($domain)) {
            $domain = $this->_getRequest()->getHttpHost();
        }
        return $domain;
    }

    /**
     * Retrieve Config Domain for cookie
     *
     * @return string
     */
    public function getConfigDomain()
    {
        return (string)\Mage::getStoreConfig(self::XML_PATH_COOKIE_DOMAIN, $this->getStore());
    }

    /**
     * Retrieve Path for cookie
     *
     * @return string
     */
    public function getPath()
    {
        $path = \Mage::getStoreConfig(self::XML_PATH_COOKIE_PATH, $this->getStore());
        if (empty($path)) {
            $path = $this->_getRequest()->getBasePath();
        }
        return $path;
    }

    /**
     * Retrieve cookie lifetime
     *
     * @return int
     */
    public function getLifetime()
    {
        if (!is_null($this->_lifetime)) {
            $lifetime = $this->_lifetime;
        } else {
            $lifetime = \Mage::getStoreConfig(self::XML_PATH_COOKIE_LIFETIME, $this->getStore());
        }
        if (!is_numeric($lifetime)) {
            $lifetime = 3600;
        }
        return $lifetime;
    }

    /**
     * Set cookie lifetime
     *
     * @param int $lifetime
     * @return \Magento\Core\Model\Cookie
     */
    public function setLifetime($lifetime)
    {
        $this->_lifetime = (int)$lifetime;
        return $this;
    }

    /**
     * Retrieve use HTTP only flag
     *
     * @return bool
     */
    public function getHttponly()
    {
        $httponly = \Mage::getStoreConfig(self::XML_PATH_COOKIE_HTTPONLY, $this->getStore());
        if (is_null($httponly)) {
            return null;
        }
        return (bool)$httponly;
    }

    /**
     * Is https secure request
     * Use secure on adminhtml only
     *
     * @return bool
     */
    public function isSecure()
    {
        if ($this->getStore()->isAdmin()) {
            return $this->_getRequest()->isSecure();
        }
        return false;
    }

    /**
     * Set cookie
     *
     * @param string $name The cookie name
     * @param string $value The cookie value
     * @param int $period Lifetime period
     * @param string $path
     * @param string $domain
     * @param int|bool $secure
     * @param bool $httponly
     * @return \Magento\Core\Model\Cookie
     */
    public function set($name, $value, $period = null, $path = null, $domain = null, $secure = null, $httponly = null)
    {
        /**
         * Check headers sent
         */
        if (!$this->_getResponse()->canSendHeaders(false)) {
            return $this;
        }

        if ($period === true) {
            $period = 3600 * 24 * 365;
        } elseif (is_null($period)) {
            $period = $this->getLifetime();
        }

        if ($period == 0) {
            $expire = 0;
        }
        else {
            $expire = time() + $period;
        }
        if (is_null($path)) {
            $path = $this->getPath();
        }
        if (is_null($domain)) {
            $domain = $this->getDomain();
        }
        if (is_null($secure)) {
            $secure = $this->isSecure();
        }
        if (is_null($httponly)) {
            $httponly = $this->getHttponly();
        }

        setcookie($name, $value, $expire, $path, $domain, $secure, $httponly);

        return $this;
    }

    /**
     * Postpone cookie expiration time if cookie value defined
     *
     * @param string $name The cookie name
     * @param int $period Lifetime period
     * @param string $path
     * @param string $domain
     * @param int|bool $secure
     * @return \Magento\Core\Model\Cookie
     */
    public function renew($name, $period = null, $path = null, $domain = null, $secure = null, $httponly = null)
    {
        if (($period === null) && !$this->getLifetime()) {
            return $this;
        }
        $value = $this->_getRequest()->getCookie($name, false);
        if ($value !== false) {
            $this->set($name, $value, $period, $path, $domain, $secure, $httponly);
        }
        return $this;
    }

    /**
     * Retrieve cookie or false if not exists
     *
     * @param string $neme The cookie name
     * @return mixed
     */
    public function get($name = null)
    {
        return $this->_getRequest()->getCookie($name, false);
    }

    /**
     * Delete cookie
     *
     * @param string $name
     * @param string $path
     * @param string $domain
     * @param int|bool $secure
     * @param int|bool $httponly
     * @return \Magento\Core\Model\Cookie
     */
    public function delete($name, $path = null, $domain = null, $secure = null, $httponly = null)
    {
        /**
         * Check headers sent
         */
        if (!$this->_getResponse()->canSendHeaders(false)) {
            return $this;
        }

        if (is_null($path)) {
            $path = $this->getPath();
        }
        if (is_null($domain)) {
            $domain = $this->getDomain();
        }
        if (is_null($secure)) {
            $secure = $this->isSecure();
        }
        if (is_null($httponly)) {
            $httponly = $this->getHttponly();
        }

        setcookie($name, null, null, $path, $domain, $secure, $httponly);
        return $this;
    }
}
