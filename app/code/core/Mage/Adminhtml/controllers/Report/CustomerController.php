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
 *
 * Customer reports admin controller
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Report_CustomerController extends Mage_Adminhtml_Controller_Action
{
    public function _initAction()
    {
        $act = $this->getRequest()->getActionName();
        if(!$act)
            $act = 'default';

        $this->loadLayout()
            ->_addBreadcrumb(Mage::helper('Mage_Reports_Helper_Data')->__('Reports'), Mage::helper('Mage_Reports_Helper_Data')->__('Reports'))
            ->_addBreadcrumb(Mage::helper('Mage_Reports_Helper_Data')->__('Customers'), Mage::helper('Mage_Reports_Helper_Data')->__('Customers'));
        return $this;
    }

    public function accountsAction()
    {
        $this->_title($this->__('Reports'))
             ->_title($this->__('Customers'))
             ->_title($this->__('New Accounts'));

        $this->_initAction()
            ->_setActiveMenu('report/customer/accounts')
            ->_addBreadcrumb(Mage::helper('Mage_Adminhtml_Helper_Data')->__('New Accounts'), Mage::helper('Mage_Adminhtml_Helper_Data')->__('New Accounts'))
            ->_addContent($this->getLayout()->createBlock('Mage_Adminhtml_Block_Report_Customer_Accounts'))
            ->renderLayout();
    }

    /**
     * Export new accounts report grid to CSV format
     */
    public function exportAccountsCsvAction()
    {
        $fileName   = 'new_accounts.csv';
        $content    = $this->getLayout()->createBlock('Mage_Adminhtml_Block_Report_Customer_Accounts_Grid')
            ->getCsv();

        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * Export new accounts report grid to Excel XML format
     */
    public function exportAccountsExcelAction()
    {
        $fileName   = 'accounts.xml';
        $content    = $this->getLayout()->createBlock('Mage_Adminhtml_Block_Report_Customer_Accounts_Grid')
            ->getExcel($fileName);

        $this->_prepareDownloadResponse($fileName, $content);
    }

    public function ordersAction()
    {
        $this->_title($this->__('Reports'))
             ->_title($this->__('Customers'))
             ->_title($this->__('Customers by Number of Orders'));

        $this->_initAction()
            ->_setActiveMenu('report/customer/orders')
            ->_addBreadcrumb(Mage::helper('Mage_Reports_Helper_Data')->__('Customers by Number of Orders'),
                Mage::helper('Mage_Reports_Helper_Data')->__('Customers by Number of Orders'))
            ->_addContent($this->getLayout()->createBlock('Mage_Adminhtml_Block_Report_Customer_Orders'))
            ->renderLayout();
    }

    /**
     * Export customers most ordered report to CSV format
     */
    public function exportOrdersCsvAction()
    {
        $fileName   = 'customers_orders.csv';
        $content    = $this->getLayout()->createBlock('Mage_Adminhtml_Block_Report_Customer_Orders_Grid')
            ->getCsv();

        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * Export customers most ordered report to Excel XML format
     */
    public function exportOrdersExcelAction()
    {
        $fileName   = 'customers_orders.xml';
        $content    = $this->getLayout()->createBlock('Mage_Adminhtml_Block_Report_Customer_Orders_Grid')
            ->getExcel($fileName);

        $this->_prepareDownloadResponse($fileName, $content);
    }

    public function totalsAction()
    {
        $this->_title($this->__('Reports'))
             ->_title($this->__('Customers'))
             ->_title($this->__('Customers by Orders Total'));

        $this->_initAction()
            ->_setActiveMenu('report/customer/totals')
            ->_addBreadcrumb(Mage::helper('Mage_Reports_Helper_Data')->__('Customers by Orders Total'),
                Mage::helper('Mage_Reports_Helper_Data')->__('Customers by Orders Total'))
            ->_addContent($this->getLayout()->createBlock('Mage_Adminhtml_Block_Report_Customer_Totals'))
            ->renderLayout();
    }

    /**
     * Export customers biggest totals report to CSV format
     */
    public function exportTotalsCsvAction()
    {
        $fileName   = 'cuatomer_totals.csv';
        $content    = $this->getLayout()->createBlock('Mage_Adminhtml_Block_Report_Customer_Totals_Grid')
            ->getCsv();

        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * Export customers biggest totals report to Excel XML format
     */
    public function exportTotalsExcelAction()
    {
        $fileName   = 'customer_totals.xml';
        $content    = $this->getLayout()->createBlock('Mage_Adminhtml_Block_Report_Customer_Totals_Grid')
            ->getExcel($fileName);

        $this->_prepareDownloadResponse($fileName, $content);
    }

    protected function _isAllowed()
    {
        switch ($this->getRequest()->getActionName()) {
            case 'accounts':
                return Mage::getSingleton('Mage_Admin_Model_Session')->isAllowed('report/customers/accounts');
                break;
            case 'orders':
                return Mage::getSingleton('Mage_Admin_Model_Session')->isAllowed('report/customers/orders');
                break;
            case 'totals':
                return Mage::getSingleton('Mage_Admin_Model_Session')->isAllowed('report/customers/totals');
                break;
            default:
                return Mage::getSingleton('Mage_Admin_Model_Session')->isAllowed('report/customers');
                break;
        }
    }
}
