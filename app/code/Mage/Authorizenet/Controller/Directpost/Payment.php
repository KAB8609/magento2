<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Authorizenet
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * DirtectPost Payment Controller
 *
 * @category   Mage
 * @package    Mage_Authorizenet
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Authorizenet_Controller_Directpost_Payment extends Mage_Core_Controller_Front_Action
{
    /**
     * @return Mage_Checkout_Model_Session
     */
    protected function _getCheckout()
    {
        return Mage::getSingleton('Mage_Checkout_Model_Session');
    }

    /**
     * Get session model

     * @return Mage_Authorizenet_Model_Directpost_Session
     */
    protected function _getDirectPostSession()
    {
        return Mage::getSingleton('Mage_Authorizenet_Model_Directpost_Session');
    }

    /**
     * Response action.
     * Action for Authorize.net SIM Relay Request.
     */
    public function responseAction()
    {
        $params = array();
        $data = $this->getRequest()->getPost();
        /* @var $paymentMethod Mage_Authorizenet_Model_DirectPost */
        $paymentMethod = Mage::getModel('Mage_Authorizenet_Model_Directpost');

        $result = array();
        if (!empty($data['x_invoice_num'])) {
            $result['x_invoice_num'] = $data['x_invoice_num'];
        }

        try {
            if (!empty($data['store_id'])) {
                $paymentMethod->setStore($data['store_id']);
            }
            $paymentMethod->process($data);
            $result['success'] = 1;
        }
        catch (Mage_Core_Exception $e) {
            Mage::logException($e);
            $result['success'] = 0;
            $result['error_msg'] = $e->getMessage();
        }
        catch (Exception $e) {
            Mage::logException($e);
            $result['success'] = 0;
            $result['error_msg'] = $this->__('We couldn\'t process your order right now. Please try again later.');
        }

        if (!empty($data['controller_action_name'])
            && strpos($data['controller_action_name'], 'sales_order_') === false
        ) {
            if (!empty($data['key'])) {
                $result['key'] = $data['key'];
            }
            $result['controller_action_name'] = $data['controller_action_name'];
            $result['is_secure'] = isset($data['is_secure']) ? $data['is_secure'] : false;
            $params['redirect'] = Mage::helper('Mage_Authorizenet_Helper_Data')->getRedirectIframeUrl($result);
        }

        Mage::register('authorizenet_directpost_form_params', $params);
        $this->addPageLayoutHandles();
        $this->loadLayout(false)->renderLayout();
    }

    /**
     * Retrieve params and put javascript into iframe
     *
     */
    public function redirectAction()
    {
        $redirectParams = $this->getRequest()->getParams();
        $params = array();
        if (!empty($redirectParams['success'])
            && isset($redirectParams['x_invoice_num'])
            && isset($redirectParams['controller_action_name'])
        ) {
            $this->_getDirectPostSession()->unsetData('quote_id');
            $params['redirect_parent'] = Mage::helper('Mage_Authorizenet_Helper_Data')->getSuccessOrderUrl($redirectParams);
        }
        if (!empty($redirectParams['error_msg'])) {
            $cancelOrder = empty($redirectParams['x_invoice_num']);
            $this->_returnCustomerQuote($cancelOrder, $redirectParams['error_msg']);
        }

        if (isset($redirectParams['controller_action_name'])
            && strpos($redirectParams['controller_action_name'], 'sales_order_') !== false
        ) {
            unset($redirectParams['controller_action_name']);
            unset($params['redirect_parent']);
        }

        Mage::register('authorizenet_directpost_form_params', array_merge($params, $redirectParams));
        $this->addPageLayoutHandles();
        $this->loadLayout(false)->renderLayout();
    }

    /**
     * Send request to authorize.net
     *
     */
    public function placeAction()
    {
        $paymentParam = $this->getRequest()->getParam('payment');
        $controller = $this->getRequest()->getParam('controller');
        if (isset($paymentParam['method'])) {
            $params = Mage::helper('Mage_Authorizenet_Helper_Data')->getSaveOrderUrlParams($controller);
            $this->_getDirectPostSession()->setQuoteId($this->_getCheckout()->getQuote()->getId());
            $this->_forward(
                $params['action'],
                $params['controller'],
                $params['module'],
                $this->getRequest()->getParams()
            );
        } else {
            $result = array(
                'error_messages' => $this->__('Please choose a payment method.'),
                'goto_section'   => 'payment'
            );
            $this->getResponse()->setBody(Mage::helper('Mage_Core_Helper_Data')->jsonEncode($result));
        }
    }

    /**
     * Return customer quote by ajax
     *
     */
    public function returnQuoteAction()
    {
        $this->_returnCustomerQuote();
        $this->getResponse()->setBody(Mage::helper('Mage_Core_Helper_Data')->jsonEncode(array('success' => 1)));
    }

    /**
     * Return customer quote
     *
     * @param bool $cancelOrder
     * @param string $errorMsg
     */
    protected function _returnCustomerQuote($cancelOrder = false, $errorMsg = '')
    {
        $incrementId = $this->_getDirectPostSession()->getLastOrderIncrementId();
        if ($incrementId &&
            $this->_getDirectPostSession()
                ->isCheckoutOrderIncrementIdExist($incrementId)
        ) {
            /* @var $order Mage_Sales_Model_Order */
            $order = Mage::getModel('Mage_Sales_Model_Order')->loadByIncrementId($incrementId);
            if ($order->getId()) {
                $quote = Mage::getModel('Mage_Sales_Model_Quote')
                    ->load($order->getQuoteId());
                if ($quote->getId()) {
                    $quote->setIsActive(1)
                        ->setReservedOrderId(NULL)
                        ->save();
                    $this->_getCheckout()->replaceQuote($quote);
                }
                $this->_getDirectPostSession()->removeCheckoutOrderIncrementId($incrementId);
                $this->_getDirectPostSession()->unsetData('quote_id');
                if ($cancelOrder) {
                    $order->registerCancellation($errorMsg)->save();
                }
            }
        }
    }
}