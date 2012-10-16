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
        $this->_title($this->__('Sales'))
            ->_title($this->__('Archive'))
            ->_title($this->__('Orders'));

        $this->loadLayout();
        $this->_setActiveMenu('Enterprise_SalesArchive::sales_archive_orders');
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
        $this->_title($this->__('Sales'))
            ->_title($this->__('Archive'))
            ->_title($this->__('Invoices'));

        $this->loadLayout();
        $this->_setActiveMenu('Enterprise_SalesArchive::sales_archive_invoices');
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
        $this->_title($this->__('Sales'))
            ->_title($this->__('Archive'))
            ->_title($this->__('Creditmemos'));

        $this->loadLayout();
        $this->_setActiveMenu('Enterprise_SalesArchive::sales_archive_creditmemos');
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
        $this->_title($this->__('Sales'))
            ->_title($this->__('Archive'))
            ->_title($this->__('Shipments'));

        $this->loadLayout();
        $this->_setActiveMenu('Enterprise_SalesArchive::sales_archive_shipments');
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
     * Cancel orders mass action
     */
    public function massCancelAction()
    {
        $this->_forward('massCancel', 'sales_order', null, array('origin' => 'archive'));
    }

    /**
     * Hold orders mass action
     */
    public function massHoldAction()
    {
        $this->_forward('massHold', 'sales_order', null, array('origin' => 'archive'));
    }

    /**
     * Unhold orders mass action
     */
    public function massUnholdAction()
    {
        $this->_forward('massUnhold', 'sales_order', null, array('origin' => 'archive'));
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
     * Print invoices mass action
     */
    public function massPrintInvoicesAction()
    {
        $this->_forward('pdfinvoices', 'sales_order', null, array('origin' => 'archive'));
    }

    /**
     * Print Credit Memos mass action
     */
    public function massPrintCreditMemosAction()
    {
        $this->_forward('pdfcreditmemos', 'sales_order', null, array('origin' => 'archive'));
    }

    /**
     * Print all documents mass action
     */
    public function massPrintAllDocumentsAction()
    {
        $this->_forward('pdfdocs', 'sales_order', null, array('origin' => 'archive'));
    }

    /**
     * Print packing slips mass action
     */
    public function massPrintPackingSlipsAction()
    {
        $this->_forward('pdfshipments', 'sales_order', null, array('origin' => 'archive'));
    }

    /**
     * Print shipping labels mass action
     */
    public function massPrintShippingLabelAction()
    {
        $this->_forward('massPrintShippingLabel', 'sales_order_shipment', null, array('origin' => 'archive'));
    }

    /**
     * Export order grid to CSV format
     */
    public function exportCsvAction()
    {
        $this->_export('csv');
    }

    /**
     *  Export order grid to Excel XML format
     */
    public function exportExcelAction()
    {
        $this->_export('xml');
    }

    /**
     * Declare headers and content file in response for file download
     *
     * @param string $type
     */
    protected function _export($type)
    {
        $action = strtolower((string)$this->getRequest()->getParam('action'));
        $this->loadLayout(false);
        $layout = $this->getLayout();

        switch ($action) {
            case 'invoice':
                $fileName = 'invoice_archive.' . $type;
                $grid = $layout->createBlock('Enterprise_SalesArchive_Block_Adminhtml_Sales_Archive_Order_Invoice_Grid');
                break;
            case 'shipment':
                $fileName = 'shipment_archive.' . $type;
                $grid = $layout->createBlock('Enterprise_SalesArchive_Block_Adminhtml_Sales_Archive_Order_Shipment_Grid');
                break;
            case 'creditmemo':
                $fileName = 'creditmemo_archive.' . $type;
                $grid = $layout->createBlock('Enterprise_SalesArchive_Block_Adminhtml_Sales_Archive_Order_Creditmemo_Grid');
                break;
            default:
                $fileName = 'orders_archive.' . $type;
                /** @var Mage_Backend_Block_Widget_Grid_ExportInterface $grid  */
                $grid = $layout->getChildBlock('sales.order.grid', 'grid.export');
                break;
        }

        if ($type == 'csv') {
            $this->_prepareDownloadResponse($fileName, $grid->getCsvFile());
        } else {
            $this->_prepareDownloadResponse($fileName, $grid->getExcelFile($fileName));
        }
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
                $acl = 'Enterprise_SalesArchive::orders';
                break;

            case 'invoices':
            case 'invoicesgrid':
                $acl = 'Enterprise_SalesArchive::invoices';
                break;

           case 'creditmemos':
           case 'creditmemosgrid':
                $acl = 'Enterprise_SalesArchive::creditmemos';
                break;

           case 'shipments':
           case 'shipmentsgrid':
                $acl = 'Enterprise_SalesArchive::shipments';
                break;

           case 'massadd':
           case 'add':
               $acl = 'Enterprise_SalesArchive::add';
                break;

           case 'massremove':
           case 'remove':
                $acl = 'Enterprise_SalesArchive::remove';
                break;

           default:
                $acl = 'Enterprise_SalesArchive::orders';
                break;
        }

        return Mage::getSingleton('Mage_Core_Model_Authorization')->isAllowed($acl);
    }
}
