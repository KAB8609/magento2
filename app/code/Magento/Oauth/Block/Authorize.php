<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Oauth
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * OAuth authorization block
 *
 * @category   Magento
 * @package    Magento_Oauth
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Oauth_Block_Authorize extends Magento_Oauth_Block_AuthorizeBaseAbstract
{
    /**
     * Retrieve customer form posting url
     *
     * @return string
     */
    public function getPostActionUrl()
    {
        /** @var $helper Magento_Customer_Helper_Data */
        $helper = $this->helper('Magento_Customer_Helper_Data');
        $url = $helper->getLoginPostUrl();
        if ($this->getIsSimple()) {
            if (strstr($url, '?')) {
                $url .= '&simple=1';
            } else {
                $url = rtrim($url, '/') . '/simple/1';
            }
        }
        return $url;
    }

    /**
     * Get form identity label
     *
     * @return string
     */
    public function getIdentityLabel()
    {
        return __('Email Address');
    }

    /**
     * Get form identity label
     *
     * @return string
     */
    public function getFormTitle()
    {
        return __('Log in as customer');
    }

    /**
     * Retrieve reject URL path
     *
     * @return string
     */
    public function getRejectUrlPath()
    {
        return 'oauth/authorize/reject';
    }
}