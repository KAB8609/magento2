<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml sales orders controller
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Sales\Controller\Adminhtml\Invoice;

class AbstractInvoice
    extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\App\Response\Http\FileFactory
     */
    protected $_fileFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\App\Response\Http\FileFactory $fileFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\App\Response\Http\FileFactory $fileFactory
    ) {
        $this->_fileFactory = $fileFactory;
        parent::__construct($context);
    }
    /**
     * Init layout, menu and breadcrumb
     *
     * @return \Magento\Sales\Controller\Adminhtml\Invoice
     */
    protected function _initAction()
    {
        $this->_view->loadLayout();
        $this->_setActiveMenu('Magento_Sales::sales_invoice')
            ->_addBreadcrumb(__('Sales'), __('Sales'))
            ->_addBreadcrumb(__('Invoices'), __('Invoices'));
        return $this;
    }

    /**
     * Order grid
     */
    public function gridAction()
    {
        $this->_view->loadLayout();
        $this->getResponse()->setBody(
            $this->_view->getLayout()->createBlock('Magento\Sales\Block\Adminhtml\Invoice\Grid')->toHtml()
        );
    }

    /**
     * Invoices grid
     */
    public function indexAction()
    {
        $this->_title->add(__('Invoices'));

        $this->_initAction()
            ->_addContent($this->_view->getLayout()->createBlock('Magento\Sales\Block\Adminhtml\Invoice'));
        $this->_view->renderLayout();
    }

    /**
     * Invoice information page
     */
    public function viewAction()
    {
        if ($invoiceId = $this->getRequest()->getParam('invoice_id')) {
            $this->_forward('view', 'order_invoice', null, array('come_from'=>'invoice'));
        } else {
            $this->_forward('noroute');
        }
    }

    /**
     * Notify user
     */
    public function emailAction()
    {
        if ($invoiceId = $this->getRequest()->getParam('invoice_id')) {
            if ($invoice = $this->_objectManager->create('Magento\Sales\Model\Order\Invoice')->load($invoiceId)) {
                $invoice->sendEmail();
                $historyItem = $this->_objectManager->create('Magento\Sales\Model\Resource\Order\Status\History\Collection')
                    ->getUnnotifiedForInstance($invoice, \Magento\Sales\Model\Order\Invoice::HISTORY_ENTITY_NAME);
                if ($historyItem) {
                    $historyItem->setIsCustomerNotified(1);
                    $historyItem->save();
                }
                $this->messageManager->addSuccess(__('We sent the message.'));
                $this->_redirect('sales/invoice/view', array(
                    'order_id'  => $invoice->getOrder()->getId(),
                    'invoice_id'=> $invoiceId,
                ));
            }
        }
    }

    public function printAction()
    {
        $invoiceId = $this->getRequest()->getParam('invoice_id');
        if ($invoiceId) {
            $invoice = $this->_objectManager->create('Magento\Sales\Model\Order\Invoice')->load($invoiceId);
            if ($invoice) {
                $pdf = $this->_objectManager->create('Magento\Sales\Model\Order\Pdf\Invoice')->getPdf(array($invoice));
                $date = $this->_objectManager->get('Magento\Core\Model\Date')->date('Y-m-d_H-i-s');
                return $this->_fileFactory->create('invoice' . $date . '.pdf', $pdf->render(), 'application/pdf');
            }
        } else {
            $this->_forward('noroute');
        }
    }

    public function pdfinvoicesAction()
    {
        $invoicesIds = $this->getRequest()->getPost('invoice_ids');
        if (!empty($invoicesIds)) {
            $invoices = $this->_objectManager->create('Magento\Sales\Model\Resource\Order\Invoice\Collection')
                ->addAttributeToSelect('*')
                ->addAttributeToFilter('entity_id', array('in' => $invoicesIds))
                ->load();
            if (!isset($pdf)) {
                $pdf = $this->_objectManager->create('Magento\Sales\Model\Order\Pdf\Invoice')->getPdf($invoices);
            } else {
                $pages = $this->_objectManager->create('Magento\Sales\Model\Order\Pdf\Invoice')->getPdf($invoices);
                $pdf->pages = array_merge ($pdf->pages, $pages->pages);
            }
            $date = $this->_objectManager->get('Magento\Core\Model\Date')->date('Y-m-d_H-i-s');

            return $this->_fileFactory->create('invoice' . $date . '.pdf', $pdf->render(), 'application/pdf');
        }
        $this->_redirect('sales/*/');
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_Sales::sales_invoice');
    }
}
