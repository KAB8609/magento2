<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Review reports admin controller
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Report_ReviewController extends Mage_Adminhtml_Controller_Action
{
    public function _initAction()
    {
        $act = $this->getRequest()->getActionName();
        if(!$act)
            $act = 'default';

        $this->loadLayout()
            ->_addBreadcrumb(
                Mage::helper('Mage_Reports_Helper_Data')->__('Reports'),
                Mage::helper('Mage_Reports_Helper_Data')->__('Reports')
            )
            ->_addBreadcrumb(
                Mage::helper('Mage_Reports_Helper_Data')->__('Review'),
                Mage::helper('Mage_Reports_Helper_Data')->__('Reviews')
            );
        return $this;
    }

    public function customerAction()
    {
        $this->_title($this->__('Reports'))
             ->_title($this->__('Reviews'))
             ->_title($this->__('Customer Reviews'));

        $this->_initAction()
            ->_setActiveMenu('Mage_Review::report_review_customer')
            ->_addBreadcrumb(
                Mage::helper('Mage_Reports_Helper_Data')->__('Customers Report'),
                Mage::helper('Mage_Reports_Helper_Data')->__('Customers Report')
            );
         $this->renderLayout();
    }

    /**
     * Export review customer report to CSV format
     */
    public function exportCustomerCsvAction()
    {
        $this->loadLayout();
        $fileName = 'review_customer.csv';
        $exportBlock = $this->getLayout()->getChildBlock('adminhtml.block.report.review.customer.grid','grid.export');
        $this->_prepareDownloadResponse($fileName, $exportBlock->getCsvFile());
    }

    /**
     * Export review customer report to Excel XML format
     */
    public function exportCustomerExcelAction()
    {
        $this->loadLayout();
        $fileName = 'review_customer.xml';
        $exportBlock = $this->getLayout()->getChildBlock('adminhtml.block.report.review.customer.grid','grid.export');
        $this->_prepareDownloadResponse($fileName, $exportBlock->getExcelFile());

    }

    public function productAction()
    {
        $this->_title($this->__('Reports'))
             ->_title($this->__('Reviews'))
             ->_title($this->__('Product Reviews'));

        $this->_initAction()
            ->_setActiveMenu('Mage_Review::report_review_product')
            ->_addBreadcrumb(Mage::helper('Mage_Reports_Helper_Data')->__('Products Report'), Mage::helper('Mage_Reports_Helper_Data')->__('Products Report'))
            ->_addContent($this->getLayout()->createBlock('Mage_Adminhtml_Block_Report_Review_Product'))
            ->renderLayout();
    }

    /**
     * Export review product report to CSV format
     */
    public function exportProductCsvAction()
    {
        $fileName   = 'review_product.csv';
        $content    = $this->getLayout()->createBlock('Mage_Adminhtml_Block_Report_Review_Product_Grid')
            ->getCsv();

        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * Export review product report to Excel XML format
     */
    public function exportProductExcelAction()
    {
        $fileName   = 'review_product.xml';
        $content    = $this->getLayout()->createBlock('Mage_Adminhtml_Block_Report_Review_Product_Grid')
            ->getExcel($fileName);

        $this->_prepareDownloadResponse($fileName, $content);
    }

    public function productDetailAction()
    {
        $this->_title($this->__('Reports'))
             ->_title($this->__('Reviews'))
             ->_title($this->__('Product Reviews'))
             ->_title($this->__('Details'));

        $this->_initAction()
            ->_setActiveMenu('Mage_Review::report_review')
            ->_addBreadcrumb(Mage::helper('Mage_Reports_Helper_Data')->__('Products Report'), Mage::helper('Mage_Reports_Helper_Data')->__('Products Report'))
            ->_addBreadcrumb(Mage::helper('Mage_Reports_Helper_Data')->__('Product Reviews'), Mage::helper('Mage_Reports_Helper_Data')->__('Product Reviews'))
            ->_addContent($this->getLayout()->createBlock('Mage_Adminhtml_Block_Report_Review_Detail'))
            ->renderLayout();
    }

    /**
     * Export review product detail report to CSV format
     */
    public function exportProductDetailCsvAction()
    {
        $fileName   = 'review_product_detail.csv';
        $content    = $this->getLayout()->createBlock('Mage_Adminhtml_Block_Report_Review_Detail_Grid')
            ->getCsv();

        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * Export review product detail report to ExcelXML format
     */
    public function exportProductDetailExcelAction()
    {
        $fileName   = 'review_product_detail.xml';
        $content    = $this->getLayout()->createBlock('Mage_Adminhtml_Block_Report_Review_Detail_Grid')
            ->getExcel($fileName);

        $this->_prepareDownloadResponse($fileName, $content);
    }

    protected function _isAllowed()
    {
        switch ($this->getRequest()->getActionName()) {
            case 'customer':
                return Mage::getSingleton('Mage_Core_Model_Authorization')->isAllowed('Mage_Reports::review_customer');
                break;
            case 'product':
                return Mage::getSingleton('Mage_Core_Model_Authorization')->isAllowed('Mage_Reports::review_product');
                break;
            default:
                return Mage::getSingleton('Mage_Core_Model_Authorization')->isAllowed('Mage_Reports::review');
                break;
        }
    }
}
