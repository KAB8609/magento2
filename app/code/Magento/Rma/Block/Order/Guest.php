<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Rma
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Rma\Block\Order;

class Guest extends \Magento\Core\Block\Template
{
    public function _construct()
    {
        parent::_construct();

        if (\Mage::helper('Magento\Rma\Helper\Data')->isEnabled()) {
            $returns = \Mage::getResourceModel('\Magento\Rma\Model\Resource\Rma\Grid\Collection')
                ->addFieldToSelect('*')
                ->addFieldToFilter('order_id', \Mage::registry('current_order')->getId())
                ->count()
            ;

            if (!empty($returns)) {
                \Mage::app()->getLayout()
                    ->getBlock('sales.order.info')
                    ->addLink('returns', 'rma/guest/returns', 'Returns');
            }
        }
    }
}
