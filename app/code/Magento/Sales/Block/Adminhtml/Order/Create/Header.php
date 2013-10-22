<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Create order form header
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Sales\Block\Adminhtml\Order\Create;

class Header extends \Magento\Sales\Block\Adminhtml\Order\Create\AbstractCreate
{
    protected function _toHtml()
    {
        if ($this->_getSession()->getOrder()->getId()) {
            return __('Edit Order #%1', $this->_getSession()->getOrder()->getIncrementId());
        }

        $customerId = $this->getCustomerId();
        $storeId    = $this->getStoreId();
        $out = '';
        if ($customerId && $storeId) {
            $out.= __('Create New Order for %1 in %2', $this->getCustomer()->getName(), $this->getStore()->getName());
        }
        elseif (!is_null($customerId) && $storeId){
            $out.= __('Create New Order for New Customer in %1', $this->getStore()->getName());
        }
        elseif ($customerId) {
            $out.= __('Create New Order for %1', $this->getCustomer()->getName());
        }
        elseif (!is_null($customerId)){
            $out.= __('Create New Order for New Customer');
        }
        else {
            $out.= __('Create New Order');
        }
        $out = $this->escapeHtml($out);
        return $out;
    }
}
