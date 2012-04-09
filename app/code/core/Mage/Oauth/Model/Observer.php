<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Oauth
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * OAuth observer
 *
 * @category    Mage
 * @package     Mage_Oauth
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Oauth_Model_Observer
{
    /**
     * Is current authorize page is simple
     *
     * @return boolean
     */
    protected function _getIsSimple()
    {
        $simple = false;
        if (stristr(Mage::app()->getRequest()->getActionName(), 'simple')
            || !is_null(Mage::app()->getRequest()->getParam('simple', null))
        ) {
            $simple = true;
        }

        return $simple;
    }

    /**
     * Get authorize endpoint url
     *
     * @param string $userType
     * @return string
     */
    protected function _getAuthorizeUrl($userType)
    {
        $simple = $this->_getIsSimple();

        if (Mage_Oauth_Model_Token::USER_TYPE_CUSTOMER == $userType) {
            if ($simple) {
                $route = Mage_Oauth_Helper_Data::ENDPOINT_AUTHORIZE_CUSTOMER_SIMPLE;
            } else {
                $route = Mage_Oauth_Helper_Data::ENDPOINT_AUTHORIZE_CUSTOMER;
            }
        } elseif (Mage_Oauth_Model_Token::USER_TYPE_ADMIN == $userType) {
            if ($simple) {
                $route = Mage_Oauth_Helper_Data::ENDPOINT_AUTHORIZE_ADMIN_SIMPLE;
            } else {
                $route = Mage_Oauth_Helper_Data::ENDPOINT_AUTHORIZE_ADMIN;
            }
        } else {
            throw new Exception('Invalid user type.');
        }

        return Mage::getUrl($route, array('_query' => array('oauth_token' => $this->_getOauthToken())));
    }

    /**
     * Retrieve oauth_token param from request
     *
     * @return string|null
     */
    protected function _getOauthToken()
    {
        return Mage::app()->getRequest()->getParam('oauth_token', null);
    }

    /**
     * Redirect customer to callback page after login
     *
     * @param Varien_Event_Observer $observer
     */
    public function afterCustomerLogin(Varien_Event_Observer $observer)
    {
        if (null !== $this->_getOauthToken()) {
            $userType = Mage_Oauth_Model_Token::USER_TYPE_CUSTOMER;
            $url = $this->_getAuthorizeUrl($userType);
            Mage::app()->getResponse()
                ->setRedirect($url)
                ->sendHeaders()
                ->sendResponse();
            exit();
        }
    }

    /**
     * Redirect admin to authorize controller after login success
     *
     * @param Varien_Event_Observer $observer
     */
    public function afterAdminLogin(Varien_Event_Observer $observer)
    {
        if (null !== $this->_getOauthToken()) {
            $userType = Mage_Oauth_Model_Token::USER_TYPE_ADMIN;
            $url = $this->_getAuthorizeUrl($userType);
            Mage::app()->getResponse()
                ->setRedirect($url)
                ->sendHeaders()
                ->sendResponse();
            exit();
        }
    }

    /**
     * Redirect admin to authorize controller after login fail
     *
     * @param Varien_Event_Observer $observer
     */
    public function afterAdminLoginFailed(Varien_Event_Observer $observer)
    {
        if (null !== $this->_getOauthToken()) {
            /** @var $session Mage_Admin_Model_Session */
            $session = Mage::getSingleton('Mage_Admin_Model_Session');
            $session->addError($observer->getException()->getMessage());

            $userType = Mage_Oauth_Model_Token::USER_TYPE_ADMIN;
            $url = $this->_getAuthorizeUrl($userType);
            Mage::app()->getResponse()
                ->setRedirect($url)
                ->sendHeaders()
                ->sendResponse();
            exit();
        }
    }
}
