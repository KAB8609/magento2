<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_SalesArchive
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Archive controller
 *
 */
class Enterprise_SalesArchive_Adminhtml_Sales_ArchiveController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Render archive grid
     *
     * @return Enterprise_SalesArchive_Adminhtml_Sales_ArchiveController
     */
    protected function _renderGrid()
    {
        $this->loadLayout(false);
        $this->renderLayout();
        return $this;
    }

    /**
     * Orders view page
     */
    public function ordersAction()
    {
        $this->loadLayout();
        $this->_setActiveMenu('sales/archive/orders');
        $this->renderLayout();
    }

    /**
     * Orders grid
     */
    public function ordersGridAction()
    {
        $this->_renderGrid();
    }

    /**
     * Invoices view page
     */
    public function invoicesAction()
    {
        $this->loadLayout();
        $this->_setActiveMenu('sales/archive/invoices');
        $this->renderLayout();
    }

    /**
     * Invoices grid
     */
    public function invoicesGridAction()
    {
        $this->_renderGrid();
    }


    /**
     * Creditmemos view page
     */
    public function creditmemosAction()
    {
        $this->loadLayout();
        $this->_setActiveMenu('sales/archive/creditmemos');
        $this->renderLayout();
    }

    /**
     * Creditmemos grid
     */
    public function creditmemosGridAction()
    {
        $this->_renderGrid();
    }

    /**
     * Shipments view page
     */
    public function shipmentsAction()
    {
        $this->loadLayout();
        $this->_setActiveMenu('sales/archive/shipments');
        $this->renderLayout();
    }

    /**
     * Shipments grid
     */
    public function shipmentsGridAction()
    {
        $this->_renderGrid();
    }


    /**
     * Cancel selected orders
     */
    public function massCancelAction()
    {
        $orderIds = $this->getRequest()->getPost('order_ids', array());
        $countCancelOrder = 0;
        foreach ($orderIds as $orderId) {
            $order = Mage::getModel('Mage_Sales_Model_Order')->load($orderId);
            if ($order->canCancel()) {
                $order->cancel()
                    ->save();
                $countCancelOrder++;
            }
        }
        if ($countCancelOrder>0) {
            $this->_getSession()->addSuccess($this->__('%s order(s) have been canceled.', $countCancelOrder));
        }
        else {
            // selected orders is not available for cancel
        }
        $this->_redirect('*/*/orders');
    }

    /**
     * Hold selected orders
     */
    public function massHoldAction()
    {
        $orderIds = $this->getRequest()->getPost('order_ids', array());
        $countHoldOrder = 0;
        foreach ($orderIds as $orderId) {
            $order = Mage::getModel('Mage_Sales_Model_Order')->load($orderId);
            if ($order->canHold()) {
                $order->hold()
                    ->save();
                $countHoldOrder++;
            }
        }
        if ($countHoldOrder>0) {
            $this->_getSession()->addSuccess($this->__('%s order(s) have been put on hold.', $countHoldOrder));
        }
        else {
            // selected orders is not available for hold
        }
        $this->_redirect('*/*/orders');
    }

    /**
     * Unhold selected orders
     */
    public function massUnholdAction()
    {
        $orderIds = $this->getRequest()->getPost('order_ids', array());
        $countUnholdOrder = 0;
        foreach ($orderIds as $orderId) {
            $order = Mage::getModel('Mage_Sales_Model_Order')->load($orderId);
            if ($order->canUnhold()) {
                $order->unhold()
                    ->save();
                $countUnholdOrder++;
            }
        }
        if ($countUnholdOrder>0) {
            $this->_getSession()->addSuccess($this->__('%s order(s) have been released from holding status.', $countUnholdOrder));
        }
        else {
            // selected orders is not available for hold
        }
        $this->_redirect('*/*/orders');
    }

    /**
     * Massaction for removing orders from archive
     *
     */
    public function massRemoveAction()
    {
        $orderIds = $this->getRequest()->getPost('order_ids', array());
        $removedFromArchive = Mage::getSingleton('Enterprise_SalesArchive_Model_Archive')
            ->removeOrdersFromArchiveById($orderIds);

        $removedFromArchiveCount = count($removedFromArchive);
        if ($removedFromArchiveCount>0) {
            $this->_getSession()->addSuccess($this->__('%s order(s) have been removed from archive.', $removedFromArchiveCount));
        }
        else {
            // selected orders is not available for removing from archive
        }
        $this->_redirect('*/*/orders');
    }

    /**
     * Massaction for adding orders to archive
     *
     */
    public function massAddAction()
    {
        $orderIds = $this->getRequest()->getPost('order_ids', array());
        $archivedIds = Mage::getSingleton('Enterprise_SalesArchive_Model_Archive')
            ->archiveOrdersById($orderIds);

        $archivedCount = count($archivedIds);
        if ($archivedCount>0) {
            $this->_getSession()->addSuccess($this->__('%s order(s) have been archived.', $archivedCount));
        } else {
            $this->_getSession()->addWarning($this->__('Selected order(s) cannot be archived.'));
        }
        $this->_redirect('*/sales_order/');
    }

    /**
     * Archive order action
     */
    public function addAction()
    {
        $orderId = $this->getRequest()->getParam('order_id');
        if ($orderId) {
            $archivedIds = Mage::getSingleton('Enterprise_SalesArchive_Model_Archive')
                ->archiveOrdersById($orderId);
            $this->_getSession()->addSuccess($this->__('The order has been archived.'));
            $this->_redirect('*/sales_order/view', array('order_id'=>$orderId));
        } else {
            $this->_getSession()->addError($this->__('Please specify order id to be archived.'));
            $this->_redirect('*/sales_order');
        }
    }

    /**
     * Unarchive order action
     */
    public function removeAction()
    {
        $orderId = $this->getRequest()->getParam('order_id');
        if ($orderId) {
            $orderIds = Mage::getSingleton('Enterprise_SalesArchive_Model_Archive')
                ->removeOrdersFromArchiveById($orderId);
            $this->_getSession()->addSuccess($this->__('The order has been removed from the archive.'));
            $this->_redirect('*/sales_order/view', array('order_id'=>$orderId));
        } else {
            $this->_getSession()->addError($this->__('Please specify order id to be removed from archive.'));
            $this->_redirect('*/sales_order');
        }
    }

    /**
     * Export order grid to CSV format
     */
    public function exportCsvAction()
    {
        $fileName   = 'orders_archive.csv';
        $grid       = $this->getLayout()
            ->createBlock('Enterprise_SalesArchive_Block_Adminhtml_Sales_Archive_Order_Grid');
        $this->_prepareDownloadResponse($fileName, $grid->getCsvFile());
    }

    /**
     *  Export order grid to Excel XML format
     */
    public function exportExcelAction()
    {
        $fileName   = 'orders_archive.xml';
        $grid       = $this->getLayout()
            ->createBlock('Enterprise_SalesArchive_Block_Adminhtml_Sales_Archive_Order_Grid');
        $this->_prepareDownloadResponse($fileName, $grid->getExcelFile($fileName));
    }

    /**
     * Check ACL permissions
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        switch (strtolower($this->getRequest()->getActionName())) {
            case 'orders':
            case 'ordersgrid':
                $acl = 'sales/archive/orders';
                break;

            case 'invoices':
            case 'invoicesgrid':
                $acl = 'sales/archive/invoices';
                break;

           case 'creditmemos':
           case 'creditmemosgrid':
                $acl = 'sales/archive/creditmemos';
                break;

           case 'shipments':
           case 'shipmentsgrid':
                $acl = 'sales/archive/shipments';
                break;

           case 'massadd':
           case 'add':
               $acl = 'sales/archive/orders/add';
                break;

           case 'massremove':
           case 'remove':
                $acl = 'sales/archive/orders/remove';
                break;

           default:
                $acl = 'sales/archive/orders';
                break;
        }

        return Mage::getSingleton('Mage_Admin_Model_Session')->isAllowed($acl);
    }
}
