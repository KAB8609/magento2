<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftWrapping
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Customer balance block for order
 *
 */
class Magento_GiftWrapping_Block_Sales_Totals extends Magento_Core_Block_Template
{
    /**
     * Initialize gift wrapping and printed card totals for order/invoice/creditmemo
     *
     * @return Magento_GiftWrapping_Block_Sales_Totals
     */
    public function initTotals()
    {
        $parent = $this->getParentBlock();
        $source  = $parent->getSource();
        $totals = Mage::helper('Magento_GiftWrapping_Helper_Data')->getTotals($source);
        foreach ($totals as $total) {
            $this->getParentBlock()->addTotalBefore(new \Magento\Object($total), 'tax');
        }
        return $this;
    }
}
