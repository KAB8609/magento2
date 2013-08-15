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
 * Adminhtml sales orders controller
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Adminhtml_Controller_Sales_Creditmemo_CreditmemoAbstract extends Magento_Adminhtml_Controller_Action
{
    /**
     * Additional initialization
     *
     */
    protected function _construct()
    {
        $this->setUsedModuleName('Magento_Sales');
    }

    /**
     * Init layout, menu and breadcrumb
     *
     * @return Magento_Adminhtml_Controller_Sales_Creditmemo
     */
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('Magento_Sales::sales_creditmemo')
            ->_addBreadcrumb($this->__('Sales'), $this->__('Sales'))
            ->_addBreadcrumb($this->__('Credit Memos'),$this->__('Credit Memos'));
        return $this;
    }

    /**
     * Creditmemos grid
     */
    public function indexAction()
    {
        $this->_initAction()
            ->_addContent($this->getLayout()->createBlock('Magento_Adminhtml_Block_Sales_Creditmemo'))
            ->renderLayout();
    }

    /**
     * Creditmemo information page
     */
    public function viewAction()
    {
        if ($creditmemoId = $this->getRequest()->getParam('creditmemo_id')) {
            $this->_forward('view', 'sales_order_creditmemo', null, array('come_from' => 'sales_creditmemo'));
        } else {
            $this->_forward('noRoute');
        }
    }

    /**
     * Notify user
     */
    public function emailAction()
    {
        if ($creditmemoId = $this->getRequest()->getParam('creditmemo_id')) {
            if ($creditmemo = Mage::getModel('Magento_Sales_Model_Order_Creditmemo')->load($creditmemoId)) {
                $creditmemo->sendEmail();
                $historyItem = Mage::getResourceModel('Magento_Sales_Model_Resource_Order_Status_History_Collection')
                    ->getUnnotifiedForInstance($creditmemo, Magento_Sales_Model_Order_Creditmemo::HISTORY_ENTITY_NAME);
                if ($historyItem) {
                    $historyItem->setIsCustomerNotified(1);
                    $historyItem->save();
                }

                $this->_getSession()->addSuccess(Mage::helper('Magento_Sales_Helper_Data')->__('We sent the message.'));
                $this->_redirect('*/sales_order_creditmemo/view', array(
                    'creditmemo_id' => $creditmemoId
                ));
            }
        }
    }

    public function pdfcreditmemosAction(){
        $creditmemosIds = $this->getRequest()->getPost('creditmemo_ids');
        if (!empty($creditmemosIds)) {
            $invoices = Mage::getResourceModel('Magento_Sales_Model_Resource_Order_Creditmemo_Collection')
                ->addAttributeToSelect('*')
                ->addAttributeToFilter('entity_id', array('in' => $creditmemosIds))
                ->load();
            if (!isset($pdf)){
                $pdf = Mage::getModel('Magento_Sales_Model_Order_Pdf_Creditmemo')->getPdf($invoices);
            } else {
                $pages = Mage::getModel('Magento_Sales_Model_Order_Pdf_Creditmemo')->getPdf($invoices);
                $pdf->pages = array_merge ($pdf->pages, $pages->pages);
            }

            return $this->_prepareDownloadResponse('creditmemo'.Mage::getSingleton('Magento_Core_Model_Date')->date('Y-m-d_H-i-s').
                '.pdf', $pdf->render(), 'application/pdf');
        }
        $this->_redirect('*/*/');
    }

    public function printAction()
    {
        /** @see Magento_Adminhtml_Controller_Sales_Order_Invoice */
        if ($creditmemoId = $this->getRequest()->getParam('creditmemo_id')) {
            if ($creditmemo = Mage::getModel('Magento_Sales_Model_Order_Creditmemo')->load($creditmemoId)) {
                $pdf = Mage::getModel('Magento_Sales_Model_Order_Pdf_Creditmemo')->getPdf(array($creditmemo));
                $this->_prepareDownloadResponse('creditmemo'.Mage::getSingleton('Magento_Core_Model_Date')->date('Y-m-d_H-i-s').
                    '.pdf', $pdf->render(), 'application/pdf');
            }
        }
        else {
            $this->_forward('noRoute');
        }
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_Sales::sales_creditmemo');
    }
}
