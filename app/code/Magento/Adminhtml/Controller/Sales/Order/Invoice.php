<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml sales order edit controller
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Adminhtml_Controller_Sales_Order_Invoice extends Magento_Adminhtml_Controller_Sales_Invoice_InvoiceAbstract
{
    /**
     * Get requested items qty's from request
     */
    protected function _getItemQtys()
    {
        $data = $this->getRequest()->getParam('invoice');
        if (isset($data['items'])) {
            $qtys = $data['items'];
        } else {
            $qtys = array();
        }
        return $qtys;
    }

    /**
     * Initialize invoice model instance
     *
     * @return Magento_Sales_Model_Order_Invoice
     */
    protected function _initInvoice($update = false)
    {
        $this->_title(__('Invoices'));

        $invoice = false;
        $itemsToInvoice = 0;
        $invoiceId = $this->getRequest()->getParam('invoice_id');
        $orderId = $this->getRequest()->getParam('order_id');
        if ($invoiceId) {
            $invoice = Mage::getModel('Magento_Sales_Model_Order_Invoice')->load($invoiceId);
            if (!$invoice->getId()) {
                $this->_getSession()->addError(__('The invoice no longer exists.'));
                return false;
            }
        } elseif ($orderId) {
            $order = Mage::getModel('Magento_Sales_Model_Order')->load($orderId);
            /**
             * Check order existing
             */
            if (!$order->getId()) {
                $this->_getSession()->addError(__('The order no longer exists.'));
                return false;
            }
            /**
             * Check invoice create availability
             */
            if (!$order->canInvoice()) {
                $this->_getSession()->addError(__('The order does not allow an invoice to be created.'));
                return false;
            }
            $savedQtys = $this->_getItemQtys();
            $invoice = Mage::getModel('Magento_Sales_Model_Service_Order', array('order' => $order))
                ->prepareInvoice($savedQtys);
            if (!$invoice->getTotalQty()) {
                Mage::throwException(__('Cannot create an invoice without products.'));
            }
        }

        Mage::register('current_invoice', $invoice);
        return $invoice;
    }

    /**
     * Save data for invoice and related order
     *
     * @param   Magento_Sales_Model_Order_Invoice $invoice
     * @return  Magento_Adminhtml_Controller_Sales_Order_Invoice
     */
    protected function _saveInvoice($invoice)
    {
        $invoice->getOrder()->setIsInProcess(true);
        $transactionSave = Mage::getModel('Magento_Core_Model_Resource_Transaction')
            ->addObject($invoice)
            ->addObject($invoice->getOrder())
            ->save();

        return $this;
    }

    /**
     * Prepare shipment
     *
     * @param Magento_Sales_Model_Order_Invoice $invoice
     * @return Magento_Sales_Model_Order_Shipment
     */
    protected function _prepareShipment($invoice)
    {
        $savedQtys = $this->_getItemQtys();
        $shipment = Mage::getModel('Magento_Sales_Model_Service_Order', array('order' => $invoice->getOrder()))
            ->prepareShipment($savedQtys);
        if (!$shipment->getTotalQty()) {
            return false;
        }


        $shipment->register();
        $tracks = $this->getRequest()->getPost('tracking');
        if ($tracks) {
            foreach ($tracks as $data) {
                $track = Mage::getModel('Magento_Sales_Model_Order_Shipment_Track')
                    ->addData($data);
                $shipment->addTrack($track);
            }
        }
        return $shipment;
    }

    /**
     * Invoice information page
     */
    public function viewAction()
    {
        $invoice = $this->_initInvoice();
        if ($invoice) {
            $this->_title(sprintf("#%s", $invoice->getIncrementId()));

            $this->loadLayout()
                ->_setActiveMenu('Magento_Sales::sales_order');
            $this->getLayout()->getBlock('sales_invoice_view')
                ->updateBackButtonUrl($this->getRequest()->getParam('come_from'));
            $this->renderLayout();
        }
        else {
            $this->_forward('noRoute');
        }
    }

    /**
     * Start create invoice action
     */
    public function startAction()
    {
        /**
         * Clear old values for invoice qty's
         */
        $this->_getSession()->getInvoiceItemQtys(true);
        $this->_redirect('*/*/new', array('order_id'=>$this->getRequest()->getParam('order_id')));
    }

    /**
     * Invoice create page
     */
    public function newAction()
    {
        $invoice = $this->_initInvoice();
        if ($invoice) {
            $this->_title(__('New Invoice'));

            if ($comment = Mage::getSingleton('Magento_Adminhtml_Model_Session')->getCommentText(true)) {
                $invoice->setCommentText($comment);
            }

            $this->loadLayout()
                ->_setActiveMenu('Magento_Sales::sales_order')
                ->renderLayout();
        } else {
            $this->_redirect('*/sales_order/view', array('order_id'=>$this->getRequest()->getParam('order_id')));
        }
    }

    /**
     * Update items qty action
     */
    public function updateQtyAction()
    {
        try {
            $invoice = $this->_initInvoice(true);
            // Save invoice comment text in current invoice object in order to display it in corresponding view
            $invoiceRawData = $this->getRequest()->getParam('invoice');
            $invoiceRawCommentText = $invoiceRawData['comment_text'];
            $invoice->setCommentText($invoiceRawCommentText);

            $this->loadLayout();
            $response = $this->getLayout()->getBlock('order_items')->toHtml();
        } catch (Magento_Core_Exception $e) {
            $response = array(
                'error'     => true,
                'message'   => $e->getMessage()
            );
            $response = Mage::helper('Magento_Core_Helper_Data')->jsonEncode($response);
        } catch (Exception $e) {
            $response = array(
                'error'     => true,
                'message'   => __('Cannot update item quantity.')
            );
            $response = Mage::helper('Magento_Core_Helper_Data')->jsonEncode($response);
        }
        $this->getResponse()->setBody($response);
    }

    /**
     * Save invoice
     * We can save only new invoice. Existing invoices are not editable
     */
    public function saveAction()
    {
        $data = $this->getRequest()->getPost('invoice');
        $orderId = $this->getRequest()->getParam('order_id');

        if (!empty($data['comment_text'])) {
            Mage::getSingleton('Magento_Adminhtml_Model_Session')->setCommentText($data['comment_text']);
        }

        try {
            $invoice = $this->_initInvoice();
            if ($invoice) {

                if (!empty($data['capture_case'])) {
                    $invoice->setRequestedCaptureCase($data['capture_case']);
                }

                if (!empty($data['comment_text'])) {
                    $invoice->addComment(
                        $data['comment_text'],
                        isset($data['comment_customer_notify']),
                        isset($data['is_visible_on_front'])
                    );
                }

                $invoice->register();

                if (!empty($data['send_email'])) {
                    $invoice->setEmailSent(true);
                }

                $invoice->getOrder()->setCustomerNoteNotify(!empty($data['send_email']));
                $invoice->getOrder()->setIsInProcess(true);

                $transactionSave = Mage::getModel('Magento_Core_Model_Resource_Transaction')
                    ->addObject($invoice)
                    ->addObject($invoice->getOrder());
                $shipment = false;
                if (!empty($data['do_shipment']) || (int) $invoice->getOrder()->getForcedShipmentWithInvoice()) {
                    $shipment = $this->_prepareShipment($invoice);
                    if ($shipment) {
                        $shipment->setEmailSent($invoice->getEmailSent());
                        $transactionSave->addObject($shipment);
                    }
                }
                $transactionSave->save();

                if (isset($shippingResponse) && $shippingResponse->hasErrors()) {
                    $this->_getSession()->addError(__('The invoice and the shipment  have been created. The shipping label cannot be created now.'));
                } elseif (!empty($data['do_shipment'])) {
                    $this->_getSession()->addSuccess(__('You created the invoice and shipment.'));
                } else {
                    $this->_getSession()->addSuccess(__('The invoice has been created.'));
                }

                // send invoice/shipment emails
                $comment = '';
                if (isset($data['comment_customer_notify'])) {
                    $comment = $data['comment_text'];
                }
                try {
                    $invoice->sendEmail(!empty($data['send_email']), $comment);
                } catch (Exception $e) {
                    Mage::logException($e);
                    $this->_getSession()->addError(__('We can\'t send the invoice email.'));
                }
                if ($shipment) {
                    try {
                        $shipment->sendEmail(!empty($data['send_email']));
                    } catch (Exception $e) {
                        Mage::logException($e);
                        $this->_getSession()->addError(__('We can\'t send the shipment.'));
                    }
                }
                Mage::getSingleton('Magento_Adminhtml_Model_Session')->getCommentText(true);
                $this->_redirect('*/sales_order/view', array('order_id' => $orderId));
            } else {
                $this->_redirect('*/*/new', array('order_id' => $orderId));
            }
            return;
        } catch (Magento_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        } catch (Exception $e) {
            $this->_getSession()->addError(__('We can\'t save the invoice.'));
            Mage::logException($e);
        }
        $this->_redirect('*/*/new', array('order_id' => $orderId));
    }


    /**
     * Capture invoice action
     */
    public function captureAction()
    {
        if ($invoice = $this->_initInvoice()) {
            try {
                $invoice->capture();
                $this->_saveInvoice($invoice);
                $this->_getSession()->addSuccess(__('The invoice has been captured.'));
            } catch (Magento_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            } catch (Exception $e) {
                $this->_getSession()->addError(__('Invoice capturing error'));
            }
            $this->_redirect('*/*/view', array('invoice_id'=>$invoice->getId()));
        } else {
            $this->_forward('noRoute');
        }
    }

    /**
     * Cancel invoice action
     */
    public function cancelAction()
    {
        if ($invoice = $this->_initInvoice()) {
            try {
                $invoice->cancel();
                $this->_saveInvoice($invoice);
                $this->_getSession()->addSuccess(__('You canceled the invoice.'));
            } catch (Magento_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            } catch (Exception $e) {
                $this->_getSession()->addError(__('Invoice canceling error'));
            }
            $this->_redirect('*/*/view', array('invoice_id'=>$invoice->getId()));
        } else {
            $this->_forward('noRoute');
        }
    }

    /**
     * Void invoice action
     */
    public function voidAction()
    {
        if ($invoice = $this->_initInvoice()) {
            try {
                $invoice->void();
                $this->_saveInvoice($invoice);
                $this->_getSession()->addSuccess(__('The invoice has been voided.'));
            } catch (Magento_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            } catch (Exception $e) {
                $this->_getSession()->addError(__('Invoice voiding error'));
            }
            $this->_redirect('*/*/view', array('invoice_id'=>$invoice->getId()));
        } else {
            $this->_forward('noRoute');
        }
    }

    public function addCommentAction()
    {
        try {
            $this->getRequest()->setParam('invoice_id', $this->getRequest()->getParam('id'));
            $data = $this->getRequest()->getPost('comment');
            if (empty($data['comment'])) {
                Mage::throwException(__('The Comment Text field cannot be empty.'));
            }
            $invoice = $this->_initInvoice();
            $invoice->addComment(
                $data['comment'],
                isset($data['is_customer_notified']),
                isset($data['is_visible_on_front'])
            );
            $invoice->sendUpdateEmail(!empty($data['is_customer_notified']), $data['comment']);
            $invoice->save();

            $this->loadLayout();
            $response = $this->getLayout()->getBlock('invoice_comments')->toHtml();
        } catch (Magento_Core_Exception $e) {
            $response = array(
                'error'     => true,
                'message'   => $e->getMessage()
            );
            $response = Mage::helper('Magento_Core_Helper_Data')->jsonEncode($response);
        } catch (Exception $e) {
            $response = array(
                'error'     => true,
                'message'   => __('Cannot add new comment.')
            );
            $response = Mage::helper('Magento_Core_Helper_Data')->jsonEncode($response);
        }
        $this->getResponse()->setBody($response);
    }

    /**
     * Create pdf for current invoice
     */
    public function printAction()
    {
        $this->_initInvoice();
        parent::printAction();
    }

}