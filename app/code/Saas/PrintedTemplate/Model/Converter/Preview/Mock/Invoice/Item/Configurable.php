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
 * Mock object for invoice item model
 *
 * @category   Saas
 * @package    Saas_PrintedTemplate
 * @subpackage Models
 */
class Saas_PrintedTemplate_Model_Converter_Preview_Mock_Invoice_Item_Configurable
    extends Mage_Sales_Model_Order_Invoice_Item
{
    /**
     * @var Saas_PrintedTemplate_Helper_Data
     */
    protected $_helper;

    /**
     * Initialize invoice item with mock data
     */
    public function init()
    {
        $this->_helper = Mage::helper('Saas_PrintedTemplate_Helper_Data');
        $this->setData($this->_getMockData());
    }

    /**
     * Returns mocks of children
     *
     * @return array Array that contains one Mage_Sales_Model_Order_Invoice_Item
     */
    public function getChildrenItemMocks()
    {
        return array (
            Mage::getModel('Mage_Sales_Model_Order_Invoice_Item')->setData($this->_getChildMockData())
        );
    }

    /**
     * Returns data for the invoice item
     *
     * @return array
     */
    protected function _getMockData()
    {
        return  array (
            'entity_id' => '50',
            'parent_id' => '-1',
            'base_price' => '89.9900',
            'base_weee_tax_row_disposition' => '0.0000',
            'weee_tax_applied_row_amount' => '0.0000',
            'base_weee_tax_applied_amount' => '0.0000',
            'tax_amount' => '15.9800',
            'base_row_total' => '89.9900',
            'discount_amount' => '18.0000',
            'row_total' => '179.9800',
            'weee_tax_row_disposition' => '0.0000',
            'base_discount_amount' => '9.0000',
            'base_weee_tax_disposition' => '0.0000',
            'price_incl_tax' => '195.9600',
            'weee_tax_applied_amount' => '0.0000',
            'base_tax_amount' => '7.9900',
            'base_price_incl_tax' => '97.9800',
            'qty' => '1.0000',
            'weee_tax_disposition' => '0.0000',
            'base_cost' => '29.9900',
            'base_weee_tax_applied_row_amount' => '0.0000',
            'price' => '179.9800',
            'base_row_total_incl_tax' => '97.9800',
            'row_total_incl_tax' => '195.9600',
            'product_id' => '108',
            'order_item_id' => '51',
            'additional_data' => NULL,
            'description' => NULL,
            'weee_tax_applied' => 'a:0:{}',
            'sku' => 'nine_4',
            'name' => __('Nine West Women\'s Lucero Pump'),
            'shipment_id' => NULL,
            'hidden_tax_amount' => '0.0000',
            'base_hidden_tax_amount' => '0.0000',
        );
    }

    /**
     * Returns data for child of configurable product
     *
     * @return array
     */
    protected function _getChildMockData()
    {
        return   array (
            'entity_id' => '51',
            'parent_id' => '-1',
            'base_price' => '0.0000',
            'base_weee_tax_row_disposition' => '0.0000',
            'weee_tax_applied_row_amount' => '0.0000',
            'base_weee_tax_applied_amount' => '0.0000',
            'tax_amount' => NULL,
            'base_row_total' => '0.0000',
            'discount_amount' => NULL,
            'row_total' => '0.0000',
            'weee_tax_row_disposition' => '0.0000',
            'base_discount_amount' => NULL,
            'base_weee_tax_disposition' => '0.0000',
            'price_incl_tax' => '0.0000',
            'weee_tax_applied_amount' => '0.0000',
            'base_tax_amount' => NULL,
            'base_price_incl_tax' => '0.0000',
            'qty' => '1.0000',
            'weee_tax_disposition' => '0.0000',
            'base_cost' => '29.9900',
            'base_weee_tax_applied_row_amount' => '0.0000',
            'price' => '0.0000',
            'base_row_total_incl_tax' => '0.0000',
            'row_total_incl_tax' => '0.0000',
            'product_id' => '109',
            'order_item_id' => '52',
            'additional_data' => NULL,
            'description' => NULL,
            'weee_tax_applied' => 'a:0:{}',
            'sku' => 'nine_4',
            'name' => __('Nine West Women\'s Lucero Pump'),
            'shipment_id' => NULL,
            'hidden_tax_amount' => NULL,
            'base_hidden_tax_amount' => NULL,
        );
    }
}
