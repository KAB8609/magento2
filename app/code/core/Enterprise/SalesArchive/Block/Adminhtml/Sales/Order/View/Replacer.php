<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_SalesArchive
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Sales archive order view replacer for archive
 *
 */
class Enterprise_SalesArchive_Block_Adminhtml_Sales_Order_View_Replacer extends Mage_Adminhtml_Block_Sales_Order_Abstract
{
    protected function _prepareLayout()
    {
        if ($this->getOrder()->getIsArchived()) {
            $this->getLayout()->getBlock('sales_order_tabs')->addTab(
                'order_shipments',
                'Enterprise_SalesArchive_Block_Adminhtml_Sales_Order_View_Tab_Shipments'
            );
            $this->getLayout()->getBlock('sales_order_tabs')->addTab(
                'order_invoices',
                'Enterprise_SalesArchive_Block_Adminhtml_Sales_Order_View_Tab_Invoices'
            );
            $this->getLayout()->getBlock('sales_order_tabs')->addTab(
                'order_creditmemos',
                'Enterprise_SalesArchive_Block_Adminhtml_Sales_Order_View_Tab_Creditmemos'
            );

            $restoreUrl = $this->getUrl(
                '*/sales_archive/remove',
                array('order_id' => $this->getOrder()->getId())
            );
            if (Mage::getSingleton('Mage_Backend_Model_Auth_Session')->isAllowed('sales/archive/orders/remove')) {
                $this->getLayout()->getBlock('sales_order_edit')->addButton('restore',  array(
                    'label' => Mage::helper('Enterprise_SalesArchive_Helper_Data')->__('Move to Order Managment'),
                    'onclick' => 'setLocation(\'' . $restoreUrl . '\')',
                    'class' => 'cancel'
                ));
            }
        } elseif ($this->getOrder()->getIsMoveable() !== false) {
            $isActive = Mage::getSingleton('Enterprise_SalesArchive_Model_Config')->isArchiveActive();
            if ($isActive) {
                $archiveUrl = $this->getUrl(
                    '*/sales_archive/add',
                    array('order_id' => $this->getOrder()->getId())
                );
                if (Mage::getSingleton('Mage_Backend_Model_Auth_Session')->isAllowed('sales/archive/orders/add')) {
                    $this->getLayout()->getBlock('sales_order_edit')->addButton('restore',  array(
                        'label' => Mage::helper('Enterprise_SalesArchive_Helper_Data')->__('Move to Archive'),
                        'onclick' => 'setLocation(\'' . $archiveUrl . '\')',
                    ));
                }
            }
        }

        return $this;
    }

    protected function _toHtml()
    {
        return '';
    }
}
