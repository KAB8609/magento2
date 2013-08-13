<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Rma
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Enterprise_Rma_Block_Return_History extends Magento_Core_Block_Template
{
    public function _construct()
    {
        parent::_construct();
        $this->setTemplate('return/history.phtml');

        $returns = Mage::getResourceModel('Enterprise_Rma_Model_Resource_Rma_Grid_Collection')
            ->addFieldToSelect('*')
            ->addFieldToFilter('customer_id', Mage::getSingleton('Mage_Customer_Model_Session')->getCustomer()->getId())
            ->setOrder('date_requested', 'desc')
        ;


        $this->setReturns($returns);
    }

    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        $pager = $this->getLayout()
            ->createBlock('Mage_Page_Block_Html_Pager', 'sales.order.history.pager')
            ->setCollection($this->getReturns());
        $this->setChild('pager', $pager);
        $this->getReturns()->load();
        return $this;
    }

    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }

    public function getViewUrl($return)
    {
        return $this->getUrl('*/*/view', array('entity_id' => $return->getId()));
    }

    public function getBackUrl()
    {
        return $this->getUrl('customer/account/');
    }
}
