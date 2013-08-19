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
 * Sales archive order view replacer for archive
 *
 */
class Magento_SalesArchive_Block_Adminhtml_Sales_Order_View_Replacer
    extends Magento_Adminhtml_Block_Sales_Order_Abstract
{
    protected function _prepareLayout()
    {
        if ($this->getOrder()->getIsArchived()) {
            $this->getLayout()->getBlock('sales_order_tabs')->addTab(
                'enterprise_order_shipments',
                'Magento_SalesArchive_Block_Adminhtml_Sales_Order_View_Tab_Shipments'
            );
            $this->getLayout()->getBlock('sales_order_tabs')->addTab(
                'enterprise_order_invoices',
                'Magento_SalesArchive_Block_Adminhtml_Sales_Order_View_Tab_Invoices'
            );
            $this->getLayout()->getBlock('sales_order_tabs')->addTab(
                'enterprise_order_creditmemos',
                'Magento_SalesArchive_Block_Adminhtml_Sales_Order_View_Tab_Creditmemos'
            );

            $restoreUrl = $this->getUrl(
                '*/sales_archive/remove',
                array('order_id' => $this->getOrder()->getId())
            );
            if ($this->_authorization->isAllowed('Magento_SalesArchive::remove')) {
                $this->getLayout()->getBlock('sales_order_edit')->addButton('restore', array(
                    'label' => Mage::helper('Magento_SalesArchive_Helper_Data')->__('Move to Order Managment'),
                    'onclick' => 'setLocation(\'' . $restoreUrl . '\')',
                    'class' => 'cancel'
                ));
            }
        } elseif ($this->getOrder()->getIsMoveable() !== false) {
            $isActive = Mage::getSingleton('Magento_SalesArchive_Model_Config')->isArchiveActive();
            if ($isActive) {
                $archiveUrl = $this->getUrl(
                    '*/sales_archive/add',
                    array('order_id' => $this->getOrder()->getId())
                );
                if ($this->_authorization->isAllowed('Magento_SalesArchive::add')) {
                    $this->getLayout()->getBlock('sales_order_edit')->addButton('restore', array(
                        'label' => Mage::helper('Magento_SalesArchive_Helper_Data')->__('Move to Archive'),
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
