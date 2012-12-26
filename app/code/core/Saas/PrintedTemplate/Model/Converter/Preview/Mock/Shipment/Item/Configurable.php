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
 * Mock object for shipment item model
 *
 * @category   Saas
 * @package    Saas_PrintedTemplate
 * @subpackage Models
 */
class Saas_PrintedTemplate_Model_Converter_Preview_Mock_Shipment_Item_Configurable
    extends Mage_Sales_Model_Order_Shipment_Item
{
    /**
     * @var Saas_PrintedTemplate_Helper_Data
     */
    protected $_helper;

    /**
     * Initialize shipment item with mock data
     */
    public function init()
    {
        $this->_helper = Mage::helper('Saas_PrintedTemplate_Helper_Data');
        $this->setData($this->_getMockData());
    }

    /**
     * Get configurable children with mock up data
     *
     * @return array
     */
    public function getChildrenItemMocks()
    {
        return array (
            Mage::getModel('Mage_Sales_Model_Order_Shipment_Item')->setData($this->_getChildMockData())
        );
    }

    /**
     * Returns data for the shipment item
     *
     * @return array
     */
    protected function _getMockData()
    {
        return  array (
            'entity_id' => '17',
            'parent_id' => '-1',
            'row_total' => NULL,
            'price' => '179.9800',
            'weight' => '2.0000',
            'qty' => '1.0000',
            'product_id' => '108',
            'order_item_id' => '51',
            'additional_data' => NULL,
            'description' => NULL,
            'name' => $this->_helper->__('Nine West Women\'s Lucero Pump'),
            'sku' => 'nine_4',
        );
    }

    /**
     * Returns data for child of configurable product
     *
     * @return array
     */
    protected function _getChildMockData()
    {
        return array (
            'entity_id' => '18',
            'parent_id' => '-1',
            'row_total' => NULL,
            'price' => '0.0000',
            'weight' => '2.0000',
            'qty' => '1.0000',
            'product_id' => '109',
            'order_item_id' => '52',
            'additional_data' => NULL,
            'description' => NULL,
            'name' => $this->_helper->__('Nine West Women\'s Lucero Pump'),
            'sku' => 'nine_4',
        );
    }
}

