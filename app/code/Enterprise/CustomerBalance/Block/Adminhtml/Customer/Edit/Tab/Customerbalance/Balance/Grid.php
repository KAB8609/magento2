<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Enterprise_CustomerBalance_Block_Adminhtml_Customer_Edit_Tab_Customerbalance_Balance_Grid
    extends Mage_Backend_Block_Widget_Grid
{
    /**
     * @return Mage_Backend_Block_Widget_Grid
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('Enterprise_CustomerBalance_Model_Balance')
            ->getCollection()
            ->addFieldToFilter('customer_id', $this->getRequest()->getParam('id'));
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }
}
