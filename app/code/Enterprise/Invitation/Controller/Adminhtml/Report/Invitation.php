<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Invitation
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Invitation reports controller
 *
 * @category   Enterprise
 * @package    Enterprise_Invitation
 */

class Enterprise_Invitation_Controller_Adminhtml_Report_Invitation extends Mage_Adminhtml_Controller_Action
{
    /**
     * Init action breadcrumbs
     *
     * @return Enterprise_Invitation_Controller_Adminhtml_Report_Invitation
     */
    public function _initAction()
    {
        $this->loadLayout()
            ->_addBreadcrumb(
                __('Reports'),
                __('Reports')
            )
            ->_addBreadcrumb(
                __('Invitations'),
                __('Invitations')
            );
        return $this;
    }

    /**
     * General report action
     */
    public function indexAction()
    {
        $this->_title(__('Invitations Report'));

        $this->_initAction()
            ->_setActiveMenu('Enterprise_Invitation::report_enterprise_invitation_general')
            ->_addBreadcrumb(__('General Report'),
            __('General Report'))
            ->renderLayout();
    }

    /**
     * Export invitation general report grid to CSV format
     */
    public function exportCsvAction()
    {
        $this->loadLayout();
        $fileName   = 'invitation_general.csv';
        /** @var Mage_Backend_Block_Widget_Grid_ExportInterface $exportBlock */
        $exportBlock = $this->getLayout()->getChildBlock('adminhtml.report.grid', 'grid.export');
        $this->_prepareDownloadResponse($fileName, $exportBlock->getCsvFile());
    }

    /**
     * Export invitation general report grid to Excel XML format
     */
    public function exportExcelAction()
    {
        $this->loadLayout();
        $fileName = 'invitation_general.xml';
        /** @var Mage_Backend_Block_Widget_Grid_ExportInterface $exportBlock */
        $exportBlock = $this->getLayout()->getChildBlock('adminhtml.report.grid', 'grid.export');
        $this->_prepareDownloadResponse($fileName, $exportBlock->getExcelFile($fileName));
    }

    /**
     * Report by customers action
     */
    public function customerAction()
    {
        $this->_title(__('Invited Customers Report'));

        $this->_initAction()
            ->_setActiveMenu('Enterprise_Invitation::report_enterprise_invitation_customer')
            ->_addBreadcrumb(__('Invitation Report by Customers'),
            __('Invitation Report by Customers'))
            ->renderLayout();
    }

    /**
     * Export invitation customer report grid to CSV format
     */
    public function exportCustomerCsvAction()
    {
        $this->loadLayout();
        $fileName = 'invitation_customer.csv';
        /** @var Mage_Backend_Block_Widget_Grid_ExportInterface $exportBlock */
        $exportBlock = $this->getLayout()->getChildBlock('adminhtml.report.grid', 'grid.export');
        $this->_prepareDownloadResponse($fileName, $exportBlock->getCsvFile());
    }

    /**
     * Export invitation customer report grid to Excel XML format
     */
    public function exportCustomerExcelAction()
    {
        $this->loadLayout();
        /** @var Mage_Backend_Block_Widget_Grid_ExportInterface $exportBlock */
        $exportBlock = $this->getLayout()->getChildBlock('adminhtml.report.grid', 'grid.export');
        $fileName = 'invitation_customer.xml';
        $this->_prepareDownloadResponse($fileName, $exportBlock->getExcelFile($fileName));
    }

    /**
     * Report by order action
     */
    public function orderAction()
    {
        $this->_title(__('Conversion Rate Report'));

        $this->_initAction()->_setActiveMenu('Enterprise_Invitation::report_enterprise_invitation_order')
            ->_addBreadcrumb(__('Invitation Report by Customers'),
            __('Invitation Report by Order Conversion Rate'))
            ->renderLayout();
    }

    /**
     * Export invitation order report grid to CSV format
     */
    public function exportOrderCsvAction()
    {
        $this->loadLayout();
        $fileName = 'invitation_order.csv';
        /** @var Mage_Backend_Block_Widget_Grid_ExportInterface $exportBlock */
        $exportBlock = $this->getLayout()->getChildBlock('adminhtml.report.grid', 'grid.export');
        $this->_prepareDownloadResponse($fileName, $exportBlock->getCsvFile());
    }

    /**
     * Export invitation order report grid to Excel XML format
     */
    public function exportOrderExcelAction()
    {
        $this->loadLayout();
        $fileName = 'invitation_order.xml';
        /** @var Mage_Backend_Block_Widget_Grid_ExportInterface $exportBlock */
        $exportBlock = $this->getLayout()->getChildBlock('adminhtml.report.grid', 'grid.export');
        $this->_prepareDownloadResponse($fileName, $exportBlock->getExcelFile($fileName));
    }

    /**
     * Acl admin user check
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('Enterprise_Invitation_Model_Config')->isEnabled() &&
            $this->_authorization->isAllowed('Enterprise_Invitation::report_enterprise_invitation');
    }
}
