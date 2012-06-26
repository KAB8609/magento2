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
 * Shopping Cart reports admin controller
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Report_ShopcartController extends Mage_Adminhtml_Controller_Action
{
    public function _initAction()
    {
        $act = $this->getRequest()->getActionName();
        $this->loadLayout()
            ->_addBreadcrumb(Mage::helper('Mage_Reports_Helper_Data')->__('Reports'), Mage::helper('Mage_Reports_Helper_Data')->__('Reports'))
            ->_addBreadcrumb(Mage::helper('Mage_Reports_Helper_Data')->__('Shopping Cart'), Mage::helper('Mage_Reports_Helper_Data')->__('Shopping Cart'));
        return $this;
    }

    public function customerAction()
    {
        $this->_title($this->__('Reports'))
             ->_title($this->__('Shopping Cart'))
             ->_title($this->__('Customer Shopping Carts'));

        $this->_initAction()
            ->_setActiveMenu('Mage_Reports::report_shopcart_customer')
            ->_addBreadcrumb(Mage::helper('Mage_Reports_Helper_Data')->__('Customers Report'), Mage::helper('Mage_Reports_Helper_Data')->__('Customers Report'))
            ->_addContent($this->getLayout()->createBlock('Mage_Adminhtml_Block_Report_Shopcart_Customer'))
            ->renderLayout();
    }

    /**
     * Export shopcart customer report to CSV format
     */
    public function exportCustomerCsvAction()
    {
        $fileName   = 'shopcart_customer.csv';
        $content    = $this->getLayout()->createBlock('Mage_Adminhtml_Block_Report_Shopcart_Customer_Grid')
            ->getCsvFile();

        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * Export shopcart customer report to Excel XML format
     */
    public function exportCustomerExcelAction()
    {
        $fileName   = 'shopcart_customer.xml';
        $content    = $this->getLayout()->createBlock('Mage_Adminhtml_Block_Report_Shopcart_Customer_Grid')
            ->getExcelFile($fileName);

        $this->_prepareDownloadResponse($fileName, $content);
    }

    public function productAction()
    {
        $this->_title($this->__('Reports'))
             ->_title($this->__('Shopping Cart'))
             ->_title($this->__('Products in Carts'));

        $this->_initAction()
            ->_setActiveMenu('Mage_Reports::report_shopcart_product')
            ->_addBreadcrumb(Mage::helper('Mage_Reports_Helper_Data')->__('Products Report'), Mage::helper('Mage_Reports_Helper_Data')->__('Products Report'))
            ->_addContent($this->getLayout()->createBlock('Mage_Adminhtml_Block_Report_Shopcart_Product'))
            ->renderLayout();
    }

    /**
     * Export products report grid to CSV format
     */
    public function exportProductCsvAction()
    {
        $fileName   = 'shopcart_product.csv';
        $content    = $this->getLayout()->createBlock('Mage_Adminhtml_Block_Report_Shopcart_Product_Grid')
            ->getCsvFile();

        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * Export products report to Excel XML format
     */
    public function exportProductExcelAction()
    {
        $fileName   = 'shopcart_product.xml';
        $content    = $this->getLayout()->createBlock('Mage_Adminhtml_Block_Report_Shopcart_Product_Grid')
            ->getExcelFile($fileName);

        $this->_prepareDownloadResponse($fileName, $content);
    }

    public function abandonedAction()
    {
        $this->_title($this->__('Reports'))
             ->_title($this->__('Shopping Cart'))
             ->_title($this->__('Abandoned Carts'));

        $this->_initAction()
            ->_setActiveMenu('Mage_Reports::report_shopcart_abandoned')
            ->_addBreadcrumb(Mage::helper('Mage_Reports_Helper_Data')->__('Abandoned Carts'), Mage::helper('Mage_Reports_Helper_Data')->__('Abandoned Carts'))
            ->_addContent($this->getLayout()->createBlock('Mage_Adminhtml_Block_Report_Shopcart_Abandoned'))
            ->renderLayout();
    }

    /**
     * Export abandoned carts report grid to CSV format
     */
    public function exportAbandonedCsvAction()
    {
        $fileName   = 'shopcart_abandoned.csv';
        $content    = $this->getLayout()->createBlock('Mage_Adminhtml_Block_Report_Shopcart_Abandoned_Grid')
            ->getCsvFile();

        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * Export abandoned carts report to Excel XML format
     */
    public function exportAbandonedExcelAction()
    {
        $fileName   = 'shopcart_abandoned.xml';
        $content    = $this->getLayout()->createBlock('Mage_Adminhtml_Block_Report_Shopcart_Abandoned_Grid')
            ->getExcelFile($fileName);

        $this->_prepareDownloadResponse($fileName, $content);
    }

    protected function _isAllowed()
    {
        switch ($this->getRequest()->getActionName()) {
            case 'customer':
                return Mage::getSingleton('Mage_Backend_Model_Auth_Session')->isAllowed('report/shopcart/customer');
                break;
            case 'product':
                return Mage::getSingleton('Mage_Backend_Model_Auth_Session')->isAllowed('report/shopcart/product');
                break;
            case 'abandoned':
                return Mage::getSingleton('Mage_Backend_Model_Auth_Session')->isAllowed('report/shopcart/abandoned');
                break;
            default:
                return Mage::getSingleton('Mage_Backend_Model_Auth_Session')->isAllowed('report/shopcart');
                break;
        }
    }
}
