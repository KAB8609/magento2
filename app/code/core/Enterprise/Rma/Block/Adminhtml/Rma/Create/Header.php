<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Rma
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Admin RMA create form header
 *
 * @category    Enterprise
 * @package     Enterprise_Rma
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 */

class Enterprise_Rma_Block_Adminhtml_Rma_Create_Header extends Enterprise_Rma_Block_Adminhtml_Rma_Create_Abstract
{
    protected function _toHtml()
    {
        $customerId = $this->getCustomerId();
        $storeId    = $this->getStoreId();
        $out = '';
        if ($customerId && $storeId) {
            $storeName      = Mage::app()->getStore($storeId)->getName();
            $customerName   = $this->getCustomerName();
            $out .= Mage::helper('Enterprise_Rma_Helper_Data')->__('Create New RMA for %s in %s', $customerName, $storeName);
        } elseif ($customerId) {
            $out.= Mage::helper('Enterprise_Rma_Helper_Data')->__('Create New RMA for %s', $this->getCustomerName());
        } else {
            $out.= Mage::helper('Enterprise_Rma_Helper_Data')->__('Create New RMA');
        }
        $out = $this->escapeHtml($out);
        $out = '<h3 class="icon-head head-sales-order">' . $out . '</h3>';
        return $out;
    }
}
