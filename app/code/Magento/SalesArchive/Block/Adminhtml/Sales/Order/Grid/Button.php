<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_SalesArchive
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 *  Add sales archiving to order's grid view massaction
 *
 */
class Magento_SalesArchive_Block_Adminhtml_Sales_Order_Grid_Button extends Magento_Adminhtml_Block_Sales_Order_Abstract
{
    protected function _prepareLayout()
    {
        $ordersCount = Mage::getResourceSingleton('Magento_SalesArchive_Model_Resource_Order_Collection')->getSize();
        $parent = $this->getLayout()->getBlock('sales_order.grid.container');
        if ($parent && $ordersCount) {
            $url = $this->getUrl('*/sales_archive/orders');
            $parent->addButton('go_to_archive',  array(
                'label'     => Mage::helper('Magento_SalesArchive_Helper_Data')->__('Go to Archive (%s orders)', $ordersCount),
                'onclick'   => 'setLocation(\'' . $url . '\')',
                'class'     => 'go'
            ));
        }
        return $this;
    }

    protected function _toHtml()
    {
        return '';
    }
}
