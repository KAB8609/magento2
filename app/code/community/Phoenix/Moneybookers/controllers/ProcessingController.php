<?php
/**
 * {license_notice}
 *
 * @category    Phoenix
 * @package     Phoenix_Moneybookers
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Phoenix_Moneybookers_ProcessingController extends Mage_Core_Controller_Front_Action
{
    /**
     * Get singleton of Checkout Session Model
     *
     * @return Mage_Checkout_Model_Session
     */
    protected function _getCheckout()
    {
        return Mage::getSingleton('Mage_Checkout_Model_Session');
    }

    /**
     * Iframe page which submits the payment data to Moneybookers.
     */
    public function placeformAction()
    {
       $this->loadLayout();
       $this->renderLayout();
    }

    /**
     * Show orderPlaceRedirect page which contains the Moneybookers iframe.
     */
    public function paymentAction()
    {
        try {
            $session = $this->_getCheckout();

            $order = Mage::getModel('Mage_Sales_Model_Order');
            $order->loadByIncrementId($session->getLastRealOrderId());
            if (!$order->getId()) {
                Mage::throwException('No order for processing found');
            }
            $order->setState(Mage_Sales_Model_Order::STATE_PENDING_PAYMENT, Mage_Sales_Model_Order::STATE_PENDING_PAYMENT,
                Mage::helper('Phoenix_Moneybookers_Helper_Data')->__('The customer was redirected to Moneybookers.')
            );
            $order->save();

            $session->setMoneybookersQuoteId($session->getQuoteId());
            $session->setMoneybookersRealOrderId($session->getLastRealOrderId());
            $session->getQuote()->setIsActive(false)->save();
            $session->clear();

            $this->loadLayout();
            $this->renderLayout();
        } catch (Exception $e){
            Mage::logException($e);
            parent::_redirect('checkout/cart');
        }
    }

    /**
     * Action to which the customer will be returned when the payment is made.
     */
    public function successAction()
    {
        $event = Mage::getModel('Phoenix_Moneybookers_Model_Event')
                 ->setEventData($this->getRequest()->getParams());
        try {
            $quoteId = $event->successEvent();
            $this->_getCheckout()->setLastSuccessQuoteId($quoteId);
            $this->_redirect('checkout/onepage/success');
            return;
        } catch (Mage_Core_Exception $e) {
            $this->_getCheckout()->addError($e->getMessage());
        } catch(Exception $e) {
            Mage::logException($e);
        }
        $this->_redirect('checkout/cart');
    }

    /**
     * Action to which the customer will be returned if the payment process is
     * cancelled.
     * Cancel order and redirect user to the shopping cart.
     */
    public function cancelAction()
    {
        $event = Mage::getModel('Phoenix_Moneybookers_Model_Event')
                 ->setEventData($this->getRequest()->getParams());
        $message = $event->cancelEvent();

        // set quote to active
        $session = $this->_getCheckout();
        if ($quoteId = $session->getMoneybookersQuoteId()) {
            $quote = Mage::getModel('Mage_Sales_Model_Quote')->load($quoteId);
            if ($quote->getId()) {
                $quote->setIsActive(true)->save();
                $session->setQuoteId($quoteId);
            }
        }

        $session->addError($message);
        $this->_redirect('checkout/cart');
    }

    /**
     * Action to which the transaction details will be posted after the payment
     * process is complete.
     */
    public function statusAction()
    {
        $event = Mage::getModel('Phoenix_Moneybookers_Model_Event')
            ->setEventData($this->getRequest()->getParams());
        $message = $event->processStatusEvent();
        $this->getResponse()->setBody($message);
    }

    /**
     * Set redirect into responce. This has to be encapsulated in an JavaScript
     * call to jump out of the iframe.
     *
     * @param string $path
     * @param array $arguments
     */
    protected function _redirect($path, $arguments=array())
    {
        $this->getResponse()->setBody(
            $this->getLayout()
                ->createBlock('Phoenix_Moneybookers_Block_Redirect')
                ->setRedirectUrl(Mage::getUrl($path, $arguments))
                ->toHtml()
        );
        return $this;
    }
}
