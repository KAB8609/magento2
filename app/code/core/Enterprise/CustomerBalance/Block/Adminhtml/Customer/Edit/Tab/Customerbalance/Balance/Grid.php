<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_CustomerBalance
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Enterprise_CustomerBalance_Block_Adminhtml_Customer_Edit_Tab_Customerbalance_Balance_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('balanceGrid');
        $this->setUseAjax(true);
        $this->setDefaultSort('name');
        $this->setFilterVisibility(false);
        $this->setPagerVisibility(false);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('Enterprise_CustomerBalance_Model_Balance')
            ->getCollection()
            ->addFieldToFilter('customer_id', $this->getRequest()->getParam('id'))
        ;
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('amount', array(
            'header'   => Mage::helper('Enterprise_CustomerBalance_Helper_Data')->__('Balance'),
            'width'    => 50,
            'index'    => 'amount',
            'sortable' => false,
            'renderer' => 'Enterprise_CustomerBalance_Block_Adminhtml_Widget_Grid_Column_Renderer_Currency',
        ));

        $this->addColumn('website_id', array(
            'header'   => Mage::helper('Enterprise_CustomerBalance_Helper_Data')->__('Website'),
            'index'    => 'website_id',
            'sortable' => false,
            'type'     => 'options',
            'options'  => Mage::getSingleton('Mage_Core_Model_System_Store')->getWebsiteOptionHash(),
        ));

        return parent::_prepareColumns();
    }
}
