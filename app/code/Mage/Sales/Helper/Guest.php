<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Sales module base helper
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Sales_Helper_Guest extends Magento_Core_Helper_Data
{
    /**
     * Cookie params
     */
    protected $_cookieName  = 'guest-view';
    protected $_lifeTime    = 600;

    /**
     * Try to load valid order by $_POST or $_COOKIE
     *
     * @return bool|null
     */
    public function loadValidOrder()
    {
        if (Mage::getSingleton('Magento_Customer_Model_Session')->isLoggedIn()) {
            Mage::app()->getResponse()->setRedirect(Mage::getUrl('sales/order/history'));
            return false;
        }

        $post = Mage::app()->getRequest()->getPost();

        $type           = '';
        $incrementId    = '';
        $lastName       = '';
        $email          = '';
        $zip            = '';
        $protectCode    = '';
        $errors         = false;

        /** @var $order Mage_Sales_Model_Order */
        $order = Mage::getModel('Mage_Sales_Model_Order');

        if (empty($post) && !Mage::getSingleton('Magento_Core_Model_Cookie')->get($this->_cookieName)) {
            Mage::app()->getResponse()->setRedirect(Mage::getUrl('sales/guest/form'));
            return false;
        } elseif (!empty($post) && isset($post['oar_order_id']) && isset($post['oar_type']))  {
            $type           = $post['oar_type'];
            $incrementId    = $post['oar_order_id'];
            $lastName       = $post['oar_billing_lastname'];
            $email          = $post['oar_email'];
            $zip            = $post['oar_zip'];

            if (empty($incrementId) || empty($lastName) || empty($type) || (!in_array($type, array('email', 'zip')))
                || ($type == 'email' && empty($email)) || ($type == 'zip' && empty($zip))) {
                $errors = true;
            }

            if (!$errors) {
                $order->loadByIncrementId($incrementId);
            }

            if ($order->getId()) {
                $billingAddress = $order->getBillingAddress();
                if ((strtolower($lastName) != strtolower($billingAddress->getLastname()))
                    || ($type == 'email'
                        && strtolower($email) != strtolower($billingAddress->getEmail()))
                    || ($type == 'zip'
                        && (strtolower($zip) != strtolower($billingAddress->getPostcode())))
                ) {
                    $errors = true;
                }
            } else {
                $errors = true;
            }

            if (!$errors) {
                $toCookie = base64_encode($order->getProtectCode());
                Mage::getSingleton('Magento_Core_Model_Cookie')->set($this->_cookieName, $toCookie, $this->_lifeTime, '/');
            }
        } elseif (Mage::getSingleton('Magento_Core_Model_Cookie')->get($this->_cookieName)) {
            $fromCookie     = Mage::getSingleton('Magento_Core_Model_Cookie')->get($this->_cookieName);
            $protectCode    = base64_decode($fromCookie);

            if (!empty($protectCode)) {
                $order->loadByAttribute('protect_code', $protectCode);

                Mage::getSingleton('Magento_Core_Model_Cookie')->renew($this->_cookieName, $this->_lifeTime, '/');
            } else {
                $errors = true;
            }
        }

        if (!$errors && $order->getId()) {
            Mage::register('current_order', $order);
            return true;
        }

        Mage::getSingleton('Magento_Core_Model_Session')->addError(
            $this->__('You entered incorrect data. Please try again.')
        );
        Mage::app()->getResponse()->setRedirect(Mage::getUrl('sales/guest/form'));
        return false;
    }

    /**
     * Get Breadcrumbs for current controller action
     *
     * @param  Magento_Core_Controller_Front_Action $controller
     */
    public function getBreadcrumbs($controller)
    {
        $breadcrumbs = $controller->getLayout()->getBlock('breadcrumbs');
        $breadcrumbs->addCrumb(
            'home',
            array(
                'label' => Mage::helper('Mage_Sales_Helper_Guest')->__('Home'),
                'title' => Mage::helper('Mage_Sales_Helper_Guest')->__('Go to Home Page'),
                'link'  => Mage::getBaseUrl()
            )
        );
        $breadcrumbs->addCrumb(
            'cms_page',
            array(
                'label' => Mage::helper('Mage_Sales_Helper_Guest')->__('Order Information'),
                'title' => Mage::helper('Mage_Sales_Helper_Guest')->__('Order Information')
            )
        );
    }

}
