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
 * Sales report admin controller
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Adminhtml_Controller_Report_Sales extends Magento_Adminhtml_Controller_Report_Abstract
{
    /**
     * Add report/sales breadcrumbs
     *
     * @return Magento_Adminhtml_Controller_Report_Sales
     */
    public function _initAction()
    {
        parent::_initAction();
        $this->_addBreadcrumb(Mage::helper('Magento_Reports_Helper_Data')->__('Sales'), Mage::helper('Magento_Reports_Helper_Data')->__('Sales'));
        return $this;
    }

    public function salesAction()
    {
        $this->_title($this->__('Sales Report'));

        $this->_showLastExecutionTime(Magento_Reports_Model_Flag::REPORT_ORDER_FLAG_CODE, 'sales');

        $this->_initAction()
            ->_setActiveMenu('Magento_Reports::report_salesroot_sales')
            ->_addBreadcrumb(Mage::helper('Magento_Adminhtml_Helper_Data')->__('Sales Report'), Mage::helper('Magento_Adminhtml_Helper_Data')->__('Sales Report'));

        $gridBlock = $this->getLayout()->getBlock('report_sales_sales.grid');
        $filterFormBlock = $this->getLayout()->getBlock('grid.filter.form');

        $this->_initReportAction(array(
            $gridBlock,
            $filterFormBlock
        ));

        $this->renderLayout();
    }

    public function bestsellersAction()
    {
        $this->_title($this->__('Best Sellers Report'));

        $this->_showLastExecutionTime(Magento_Reports_Model_Flag::REPORT_BESTSELLERS_FLAG_CODE, 'bestsellers');

        $this->_initAction()
            ->_setActiveMenu('Magento_Reports::report_products_bestsellers')
            ->_addBreadcrumb(Mage::helper('Magento_Adminhtml_Helper_Data')->__('Products Bestsellers Report'), Mage::helper('Magento_Adminhtml_Helper_Data')->__('Products Bestsellers Report'));

        $gridBlock = $this->getLayout()->getBlock('report_sales_bestsellers.grid');
        $filterFormBlock = $this->getLayout()->getBlock('grid.filter.form');

        $this->_initReportAction(array(
            $gridBlock,
            $filterFormBlock
        ));

        $this->renderLayout();
    }

    /**
     * Export bestsellers report grid to CSV format
     */
    public function exportBestsellersCsvAction()
    {
        $fileName   = 'bestsellers.csv';
        $grid       = $this->getLayout()->createBlock('Magento_Adminhtml_Block_Report_Sales_Bestsellers_Grid');
        $this->_initReportAction($grid);
        $this->_prepareDownloadResponse($fileName, $grid->getCsvFile());
    }

    /**
     * Export bestsellers report grid to Excel XML format
     */
    public function exportBestsellersExcelAction()
    {
        $fileName   = 'bestsellers.xml';
        $grid       = $this->getLayout()->createBlock('Magento_Adminhtml_Block_Report_Sales_Bestsellers_Grid');
        $this->_initReportAction($grid);
        $this->_prepareDownloadResponse($fileName, $grid->getExcelFile($fileName));
    }

    /**
     * Refresh statistics for last 25 hours
     *
     * @return Magento_Adminhtml_Controller_Report_Sales
     */
    public function refreshRecentAction()
    {
        return $this->_forward('refreshRecent', 'report_statistics');
    }

    /**
     * Refresh statistics for all period
     *
     * @return Magento_Adminhtml_Controller_Report_Sales
     */
    public function refreshLifetimeAction()
    {
        return $this->_forward('refreshLifetime', 'report_statistics');
    }

    /**
     * Export sales report grid to CSV format
     */
    public function exportSalesCsvAction()
    {
        $fileName   = 'sales.csv';
        $grid       = $this->getLayout()->createBlock('Magento_Adminhtml_Block_Report_Sales_Sales_Grid');
        $this->_initReportAction($grid);
        $this->_prepareDownloadResponse($fileName, $grid->getCsvFile());
    }

    /**
     * Export sales report grid to Excel XML format
     */
    public function exportSalesExcelAction()
    {
        $fileName   = 'sales.xml';
        $grid       = $this->getLayout()->createBlock('Magento_Adminhtml_Block_Report_Sales_Sales_Grid');
        $this->_initReportAction($grid);
        $this->_prepareDownloadResponse($fileName, $grid->getExcelFile($fileName));
    }

    public function taxAction()
    {
        $this->_title($this->__('Tax Report'));

        $this->_showLastExecutionTime(Magento_Reports_Model_Flag::REPORT_TAX_FLAG_CODE, 'tax');

        $this->_initAction()
            ->_setActiveMenu('Magento_Reports::report_salesroot_tax')
            ->_addBreadcrumb(Mage::helper('Magento_Adminhtml_Helper_Data')->__('Tax'), Mage::helper('Magento_Adminhtml_Helper_Data')->__('Tax'));

        $gridBlock = $this->getLayout()->getBlock('report_sales_tax.grid');
        $filterFormBlock = $this->getLayout()->getBlock('grid.filter.form');

        $this->_initReportAction(array(
            $gridBlock,
            $filterFormBlock
        ));

        $this->renderLayout();
    }

    /**
     * Export tax report grid to CSV format
     */
    public function exportTaxCsvAction()
    {
        $fileName   = 'tax.csv';
        $grid       = $this->getLayout()->createBlock('Magento_Adminhtml_Block_Report_Sales_Tax_Grid');
        $this->_initReportAction($grid);
        $this->_prepareDownloadResponse($fileName, $grid->getCsvFile());
    }

    /**
     * Export tax report grid to Excel XML format
     */
    public function exportTaxExcelAction()
    {
        $fileName   = 'tax.xml';
        $grid       = $this->getLayout()->createBlock('Magento_Adminhtml_Block_Report_Sales_Tax_Grid');
        $this->_initReportAction($grid);
        $this->_prepareDownloadResponse($fileName, $grid->getExcelFile($fileName));
    }

    public function shippingAction()
    {
        $this->_title($this->__('Shipping Report'));

        $this->_showLastExecutionTime(Magento_Reports_Model_Flag::REPORT_SHIPPING_FLAG_CODE, 'shipping');

        $this->_initAction()
            ->_setActiveMenu('Magento_Reports::report_salesroot_shipping')
            ->_addBreadcrumb(Mage::helper('Magento_Adminhtml_Helper_Data')->__('Shipping'), Mage::helper('Magento_Adminhtml_Helper_Data')->__('Shipping'));

        $gridBlock = $this->getLayout()->getBlock('report_sales_shipping.grid');
        $filterFormBlock = $this->getLayout()->getBlock('grid.filter.form');

        $this->_initReportAction(array(
            $gridBlock,
            $filterFormBlock
        ));

        $this->renderLayout();
    }

    /**
     * Export shipping report grid to CSV format
     */
    public function exportShippingCsvAction()
    {
        $fileName   = 'shipping.csv';
        $grid       = $this->getLayout()->createBlock('Magento_Adminhtml_Block_Report_Sales_Shipping_Grid');
        $this->_initReportAction($grid);
        $this->_prepareDownloadResponse($fileName, $grid->getCsvFile());
    }

    /**
     * Export shipping report grid to Excel XML format
     */
    public function exportShippingExcelAction()
    {
        $fileName   = 'shipping.xml';
        $grid       = $this->getLayout()->createBlock('Magento_Adminhtml_Block_Report_Sales_Shipping_Grid');
        $this->_initReportAction($grid);
        $this->_prepareDownloadResponse($fileName, $grid->getExcelFile($fileName));
    }

    public function invoicedAction()
    {
        $this->_title($this->__('Invoice Report'));

        $this->_showLastExecutionTime(Magento_Reports_Model_Flag::REPORT_INVOICE_FLAG_CODE, 'invoiced');

        $this->_initAction()
            ->_setActiveMenu('Magento_Reports::report_salesroot_invoiced')
            ->_addBreadcrumb(Mage::helper('Magento_Adminhtml_Helper_Data')->__('Total Invoiced'), Mage::helper('Magento_Adminhtml_Helper_Data')->__('Total Invoiced'));

        $gridBlock = $this->getLayout()->getBlock('report_sales_invoiced.grid');
        $filterFormBlock = $this->getLayout()->getBlock('grid.filter.form');

        $this->_initReportAction(array(
            $gridBlock,
            $filterFormBlock
        ));

        $this->renderLayout();
    }

    /**
     * Export invoiced report grid to CSV format
     */
    public function exportInvoicedCsvAction()
    {
        $fileName   = 'invoiced.csv';
        $grid       = $this->getLayout()->createBlock('Magento_Adminhtml_Block_Report_Sales_Invoiced_Grid');
        $this->_initReportAction($grid);
        $this->_prepareDownloadResponse($fileName, $grid->getCsvFile());
    }

    /**
     * Export invoiced report grid to Excel XML format
     */
    public function exportInvoicedExcelAction()
    {
        $fileName   = 'invoiced.xml';
        $grid       = $this->getLayout()->createBlock('Magento_Adminhtml_Block_Report_Sales_Invoiced_Grid');
        $this->_initReportAction($grid);
        $this->_prepareDownloadResponse($fileName, $grid->getExcelFile($fileName));
    }

    public function refundedAction()
    {
        $this->_title($this->__('Refunds Report'));

        $this->_showLastExecutionTime(Magento_Reports_Model_Flag::REPORT_REFUNDED_FLAG_CODE, 'refunded');

        $this->_initAction()
            ->_setActiveMenu('Magento_Reports::report_salesroot_refunded')
            ->_addBreadcrumb(Mage::helper('Magento_Adminhtml_Helper_Data')->__('Total Refunded'), Mage::helper('Magento_Adminhtml_Helper_Data')->__('Total Refunded'));

        $gridBlock = $this->getLayout()->getBlock('report_sales_refunded.grid');
        $filterFormBlock = $this->getLayout()->getBlock('grid.filter.form');

        $this->_initReportAction(array(
            $gridBlock,
            $filterFormBlock
        ));

        $this->renderLayout();
    }

    /**
     * Export refunded report grid to CSV format
     */
    public function exportRefundedCsvAction()
    {
        $fileName   = 'refunded.csv';
        $grid       = $this->getLayout()->createBlock('Magento_Adminhtml_Block_Report_Sales_Refunded_Grid');
        $this->_initReportAction($grid);
        $this->_prepareDownloadResponse($fileName, $grid->getCsvFile());
    }

    /**
     * Export refunded report grid to Excel XML format
     */
    public function exportRefundedExcelAction()
    {
        $fileName   = 'refunded.xml';
        $grid       = $this->getLayout()->createBlock('Magento_Adminhtml_Block_Report_Sales_Refunded_Grid');
        $this->_initReportAction($grid);
        $this->_prepareDownloadResponse($fileName, $grid->getExcelFile($fileName));
    }

    public function couponsAction()
    {
        $this->_title($this->__('Coupons Report'));

        $this->_showLastExecutionTime(Magento_Reports_Model_Flag::REPORT_COUPONS_FLAG_CODE, 'coupons');

        $this->_initAction()
            ->_setActiveMenu('Magento_Reports::report_salesroot_coupons')
            ->_addBreadcrumb(Mage::helper('Magento_Adminhtml_Helper_Data')->__('Coupons'), Mage::helper('Magento_Adminhtml_Helper_Data')->__('Coupons'));

        $gridBlock = $this->getLayout()->getBlock('report_sales_coupons.grid');
        $filterFormBlock = $this->getLayout()->getBlock('grid.filter.form');

        $this->_initReportAction(array(
            $gridBlock,
            $filterFormBlock
        ));

        $this->renderLayout();
    }

    /**
     * Export coupons report grid to CSV format
     */
    public function exportCouponsCsvAction()
    {
        $fileName   = 'coupons.csv';
        $grid       = $this->getLayout()->createBlock('Magento_Adminhtml_Block_Report_Sales_Coupons_Grid');
        $this->_initReportAction($grid);
        $this->_prepareDownloadResponse($fileName, $grid->getCsvFile());
    }

    /**
     * Export coupons report grid to Excel XML format
     */
    public function exportCouponsExcelAction()
    {
        $fileName   = 'coupons.xml';
        $grid       = $this->getLayout()->createBlock('Magento_Adminhtml_Block_Report_Sales_Coupons_Grid');
        $this->_initReportAction($grid);
        $this->_prepareDownloadResponse($fileName, $grid->getExcelFile($fileName));
    }

    public function refreshStatisticsAction()
    {
        return $this->_forward('index', 'report_statistics');
    }

    protected function _isAllowed()
    {
        switch ($this->getRequest()->getActionName()) {
            case 'sales':
                return $this->_authorization->isAllowed('Magento_Reports::salesroot_sales');
                break;
            case 'tax':
                return $this->_authorization->isAllowed('Magento_Reports::tax');
                break;
            case 'shipping':
                return $this->_authorization->isAllowed('Magento_Reports::shipping');
                break;
            case 'invoiced':
                return $this->_authorization->isAllowed('Magento_Reports::invoiced');
                break;
            case 'refunded':
                return $this->_authorization->isAllowed('Magento_Reports::refunded');
                break;
            case 'coupons':
                return $this->_authorization->isAllowed('Magento_Reports::coupons');
                break;
            case 'shipping':
                return $this->_authorization->isAllowed('Magento_Reports::shipping');
                break;
            case 'bestsellers':
                return $this->_authorization->isAllowed('Magento_Reports::bestsellers');
                break;
            default:
                return $this->_authorization->isAllowed('Magento_Reports::salesroot');
                break;
        }
    }
}
