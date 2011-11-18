<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_GiftWrapping
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Gift wrapping total block for admin checkout
 *
 */
class Enterprise_GiftWrapping_Block_Adminhtml_Order_Create_Totals extends Mage_Adminhtml_Block_Sales_Order_Create_Totals_Default
{
    /**
     * Return information for showing
     *
     * @return array
     */
    public function getValues(){
        $values = array();
        $total = $this->getTotal();
        $totals = Mage::helper('Enterprise_GiftWrapping_Helper_Data')->getTotals($total);
        foreach ($totals as $total) {
            $values[$total['label']] = $total['value'];
        }
        return $values;
    }
}
