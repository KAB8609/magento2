<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Weee
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Magento_Weee_Model_Total_Creditmemo_Weee extends Mage_Sales_Model_Order_Creditmemo_Total_Abstract
{
    public function collect(Mage_Sales_Model_Order_Creditmemo $creditmemo)
    {
        $store = $creditmemo->getStore();

        $totalTax              = 0;
        $baseTotalTax          = 0;

        foreach ($creditmemo->getAllItems() as $item) {
            if ($item->getOrderItem()->isDummy()) {
                continue;
            }
            $orderItemQty = $item->getOrderItem()->getQtyOrdered();

            $totalTax += $item->getWeeeTaxAppliedAmount()*$item->getQty();
            $baseTotalTax += $item->getBaseWeeeTaxAppliedAmount()*$item->getQty();

            $newApplied = array();
            $applied = Mage::helper('Magento_Weee_Helper_Data')->getApplied($item);
            foreach ($applied as $one) {
                $one['base_row_amount'] = $one['base_amount']*$item->getQty();
                $one['row_amount'] = $one['amount']*$item->getQty();
                $one['base_row_amount_incl_tax'] = $one['base_amount_incl_tax']*$item->getQty();
                $one['row_amount_incl_tax'] = $one['amount_incl_tax']*$item->getQty();

                $newApplied[] = $one;
            }
            Mage::helper('Magento_Weee_Helper_Data')->setApplied($item, $newApplied);

            $item->setWeeeTaxRowDisposition($item->getWeeeTaxDisposition()*$item->getQty());
            $item->setBaseWeeeTaxRowDisposition($item->getBaseWeeeTaxDisposition()*$item->getQty());
        }

        if (Mage::helper('Magento_Weee_Helper_Data')->includeInSubtotal($store)) {
            $creditmemo->setSubtotal($creditmemo->getSubtotal() + $totalTax);
            $creditmemo->setBaseSubtotal($creditmemo->getBaseSubtotal() + $baseTotalTax);
        } else {
            $creditmemo->setTaxAmount($creditmemo->getTaxAmount() + $totalTax);
            $creditmemo->setBaseTaxAmount($creditmemo->getBaseTaxAmount() + $baseTotalTax);
        }

        $creditmemo->setGrandTotal($creditmemo->getGrandTotal() + $totalTax);
        $creditmemo->setBaseGrandTotal($creditmemo->getBaseGrandTotal() + $baseTotalTax);

        return $this;
    }
}
