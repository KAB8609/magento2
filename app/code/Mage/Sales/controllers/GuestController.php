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
 * Sales orders controller
 *
 * @category   Mage
 * @package    MAbout This Orderage_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Sales_GuestController extends Mage_Sales_Controller_Abstract
{
    /**
     * Try to load valid order and register it
     *
     * @param int $orderId
     * @return bool
     */
    protected function _loadValidOrder($orderId = null)
    {
        return Mage::helper('Mage_Sales_Helper_Guest')->loadValidOrder();
    }

    /**
     * Check order view availability
     *
     * @param   Mage_Sales_Model_Order $order
     * @return  bool
     */
    protected function _canViewOrder($order)
    {
        $currentOrder = Mage::registry('current_order');
        if ($order->getId() && ($order->getId() === $currentOrder->getId())) {
            return true;
        }
        return false;
    }

    protected function _viewAction()
    {
        if (!$this->_loadValidOrder()) {
            return;
        }

        $this->loadLayout();
        Mage::helper('Mage_Sales_Helper_Guest')->getBreadcrumbs($this);
        $this->renderLayout();
    }

    /**
     * Order view form page
     */
    public function formAction()
    {
        if (Mage::getSingleton('Mage_Customer_Model_Session')->isLoggedIn()) {
            $this->_redirect('customer/account/');
            return;
        }
        $this->loadLayout();
        $this->getLayout()->getBlock('head')->setTitle(__('Orders and Returns'));
        Mage::helper('Mage_Sales_Helper_Guest')->getBreadcrumbs($this);
        $this->renderLayout();
    }

    public function printInvoiceAction()
    {
        if (!$this->_loadValidOrder()) {
            return;
        }

        $invoiceId = (int) $this->getRequest()->getParam('invoice_id');
        if ($invoiceId) {
            $invoice = Mage::getModel('Mage_Sales_Model_Order_Invoice')->load($invoiceId);
            $order = $invoice->getOrder();
        } else {
            $order = Mage::registry('current_order');
        }

        if ($this->_canViewOrder($order)) {
            if (isset($invoice)) {
                Mage::register('current_invoice', $invoice);
            }
            $this->loadLayout('print');
            $this->renderLayout();
        } else {
            $this->_redirect('sales/guest/form');
        }
    }

    public function printShipmentAction()
    {
        if (!$this->_loadValidOrder()) {
            return;
        }

        $shipmentId = (int) $this->getRequest()->getParam('shipment_id');
        if ($shipmentId) {
            $shipment = Mage::getModel('Mage_Sales_Model_Order_Shipment')->load($shipmentId);
            $order = $shipment->getOrder();
        } else {
            $order = Mage::registry('current_order');
        }
        if ($this->_canViewOrder($order)) {
            if (isset($shipment)) {
                Mage::register('current_shipment', $shipment);
            }
            $this->loadLayout('print');
            $this->renderLayout();
        } else {
            $this->_redirect('sales/guest/form');
        }
    }

    public function printCreditmemoAction()
    {
        if (!$this->_loadValidOrder()) {
            return;
        }

        $creditmemoId = (int) $this->getRequest()->getParam('creditmemo_id');
        if ($creditmemoId) {
            $creditmemo = Mage::getModel('Mage_Sales_Model_Order_Creditmemo')->load($creditmemoId);
            $order = $creditmemo->getOrder();
        } else {
            $order = Mage::registry('current_order');
        }

        if ($this->_canViewOrder($order)) {
            if (isset($creditmemo)) {
                Mage::register('current_creditmemo', $creditmemo);
            }
            $this->loadLayout('print');
            $this->renderLayout();
        } else {
            $this->_redirect('sales/guest/form');
        }
    }
}
