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
 * Adminhtml sales report page content block
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Magento_Adminhtml_Block_Report_Sales_Sales extends Magento_Adminhtml_Block_Widget_Grid_Container
{
    protected $_template = 'report/grid/container.phtml';

    protected function _construct()
    {
        $this->_controller = 'report_sales_sales';
        $this->_headerText = Mage::helper('Magento_Reports_Helper_Data')->__('Total Ordered Report');
        parent::_construct();

        $this->_removeButton('add');
        $this->addButton('filter_form_submit', array(
            'label'     => Mage::helper('Magento_Reports_Helper_Data')->__('Show Report'),
            'onclick'   => 'filterFormSubmit()',
            'class'     => 'primary'
        ));
    }

    public function getFilterUrl()
    {
        $this->getRequest()->setParam('filter', null);
        return $this->getUrl('*/*/sales', array('_current' => true));
    }
}
