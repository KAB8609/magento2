<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Backend_Helper_Data extends Mage_Core_Helper_Abstract
{
    const XML_PATH_ADMINHTML_ROUTER_FRONTNAME   = 'admin/routers/adminhtml/args/frontName';
    const XML_PATH_USE_CUSTOM_ADMIN_URL         = 'default/admin/url/use_custom';
    const XML_PATH_USE_CUSTOM_ADMIN_PATH        = 'default/admin/url/use_custom_path';
    const XML_PATH_CUSTOM_ADMIN_PATH            = 'default/admin/url/custom_path';

    protected $_pageHelpUrl;

    public function getPageHelpUrl()
    {
        if (!$this->_pageHelpUrl) {
            $this->setPageHelpUrl();
        }
        return $this->_pageHelpUrl;
    }

    public function setPageHelpUrl($url=null)
    {
        if (is_null($url)) {
            $request = Mage::app()->getRequest();
            $frontModule = $request->getControllerModule();
            if (!$frontModule) {
                $frontName = $request->getModuleName();
                $router = Mage::app()->getFrontController()->getRouterByFrontName($frontName);

                $frontModule = $router->getModuleByFrontName($frontName);
                if (is_array($frontModule)) {
                    $frontModule = $frontModule[0];
                }
            }
            $url = 'http://www.magentocommerce.com/gethelp/';
            $url.= Mage::app()->getLocale()->getLocaleCode().'/';
            $url.= $frontModule.'/';
            $url.= $request->getControllerName().'/';
            $url.= $request->getActionName().'/';

            $this->_pageHelpUrl = $url;
        }
        $this->_pageHelpUrl = $url;

        return $this;
    }

    public function addPageHelpUrl($suffix)
    {
        $this->_pageHelpUrl = $this->getPageHelpUrl().$suffix;
        return $this;
    }

    public static function getUrl($route='', $params=array())
    {
        return Mage::getModel('Mage_Backend_Model_Url')->getUrl($route, $params);
    }

    public function getCurrentUserId()
    {
        if (Mage::getSingleton('Mage_Backend_Model_Auth_Session')->getUser()) {
            return Mage::getSingleton('Mage_Backend_Model_Auth_Session')->getUser()->getId();
        }
        return false;
    }

    /**
     * Decode filter string
     *
     * @param string $filterString
     * @return data
     */
    public function prepareFilterString($filterString)
    {
        $data = array();
        $filterString = base64_decode($filterString);
        parse_str($filterString, $data);
        array_walk_recursive($data, array($this, 'decodeFilter'));
        return $data;
    }

    /**
     * Decode URL encoded filter value recursive callback method
     *
     * @param string $value
     */
    public function decodeFilter(&$value)
    {
        $value = rawurldecode($value);
    }

    /**
     * Generate unique token for reset password confirmation link
     *
     * @return string
     */
    public function generateResetPasswordLinkToken()
    {
        return Mage::helper('Mage_Core_Helper_Data')->uniqHash();
    }

    /**
     * Get backend start page URL
     *
     * @return string
     */
    public function getHomePageUrl()
    {
        return Mage::getModel('Mage_Backend_Model_Url')->getRouteUrl('adminhtml');
    }

    /**
     * Find admin start page url
     *
     * @return string
     */
    public function getStartupPageUrl()
    {
        return Mage::getSingleton('Mage_Backend_Model_Url')->getStartupPageUrl();
    }
}
