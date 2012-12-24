<?php
/**
 * {license_notice}
 *
 * @category    Saas
 * @package     Saas_PrintedTemplate
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Detailed tax infoirmation for order items
 *
 * @category    Saas
 * @package     Saas_PrintedTemplate
 * @subpackage  Models
 */
class Saas_PrintedTemplate_Model_Resource_Tax_Order_Item_Collection
    extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Collection constructor. Initialize collection's model.
     */
    protected function _construct()
    {
        $this->_init('Saas_Printedtemplate_Model_Resource_Tax_Order_Item');
    }

    /**
     * Adds percent, real_percent, priority, item_id columns
     * and adds sorting by item_id, priority and percent
     *
     * @see Mage_Core_Model_Resource_Db_Collection_Abstract::_initSelect()
     */
    protected function _initSelect()
    {
        parent::_initSelect();

        $this->addFieldToSelect('percent')
            ->addFieldToSelect('real_percent')
            ->addFieldToSelect('priority')
            ->addFieldToSelect('item_id')
            ->addFieldToSelect('is_tax_after_discount')
            ->addFieldToSelect('is_discount_on_incl_tax')
            ->setOrder('item_id', Varien_Data_Collection::SORT_ORDER_ASC)
            ->setOrder('priority', Varien_Data_Collection::SORT_ORDER_ASC)
            ->setOrder('real_percent', Varien_Data_Collection::SORT_ORDER_ASC);

        return $this;
    }

    /**
     * Joins items table (sales_flat_order_iem, sales_flat_incovice_item, ...)
     * Joined table should have entity_id, parent_id (order_id), tax_amount,
     * base_tax_amount and qty columns.
     *
     * @param string|Mage_Core_Model_Resource_Abstract|Mage_Core_Model_Abstract $table
     * @param string $targetColumn Foreign key in joined table
     * @return Saas_PrintedTemplate_Model_Resource_Tax_Order_Item_Collection Self
     */
    protected function _joinItems($table, $targetColumn)
    {
        $this->getSelect()
            ->join(
                array('item' => $this->_getTableName($table)),
                "main_table.item_id = item.$targetColumn",
                array(
                    'row_total'            => 'item.row_total',
                    'base_row_total'       => 'item.base_row_total',
                    'discount_amount'      => 'item.discount_amount',
                    'base_discount_amount' => 'item.base_discount_amount',
                    'tax_amount'           => 'item.tax_amount',
                    'base_tax_amount'      => 'item.base_tax_amount',
                )
            );

        return $this;
    }

    /**
     * Returns table name of collection, model if get string doesn't touch it.
     *
     * @param string|Mage_Core_Model_Mysql4_Abstract|Mage_Core_Model_Abstract $table
     * @return string Table name
     */
    protected function _getTableName($table)
    {
        if ($table instanceof Mage_Core_Model_Resource_Db_Collection_Abstract) {
            return $table->getMainTable();
        }
        if ($table instanceof Mage_Core_Model_Abstract) {
            return $table->getResource()->getMainTable();
        }

        return (string)$table;
    }

    /**
     * Add filter by order
     *
     * @param Mage_Sales_Model_Order $order
     * @return Saas_PrintedTemplate_Model_Resource_Tax_Order_Item_Collection Self
     */
    public function addFilterByOrder(Mage_Sales_Model_Order $order)
    {
        return $this->_joinItems($order->getItemsCollection(), 'item_id')
                    ->addFieldToFilter('parent_id', $order->getId())
                    ->addFieldToSelect('parent_id', 'order_id');
    }

    /**
     * Add filter by invoice
     *
     * @param Mage_Sales_Model_Order_Invoice $invoice
     * @return Saas_PrintedTemplate_Model_Resource_Tax_Order_Item_Collection Self
     */
    public function addFilterByInvoice(Mage_Sales_Model_Order_Invoice $invoice)
    {
        return $this->_joinItems($invoice->getItemsCollection(), 'order_item_id')
                    ->addFieldToFilter('parent_id', $invoice->getId());
    }

    /**
     * Add filter by creditmemo
     *
     * @param Mage_Sales_Model_Order_Creditmemo $creditmemo
     * @return Saas_PrintedTemplate_Model_Resource_Tax_Order_Item_Collection Self
     */
    public function addFilterByCreditmemo(Mage_Sales_Model_Order_Creditmemo $creditmemo)
    {
        return $this->_joinItems($creditmemo->getItemsCollection(), 'order_item_id')
                    ->addFieldToFilter('parent_id', $creditmemo->getId());
    }
}

