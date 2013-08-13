<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Order entity resource model
 *
 * @category    Mage
 * @package     Mage_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Sales_Model_Resource_Report_Order extends Mage_Sales_Model_Resource_Report_Abstract
{
    /**
     * Model initialization
     *
     */
    protected function _construct()
    {
        $this->_init('sales_order_aggregated_created', 'id');
    }

    /**
     * Aggregate Orders data
     *
     * @param mixed $from
     * @param mixed $to
     * @return Mage_Sales_Model_Resource_Report_Order
     */
    public function aggregate($from = null, $to = null)
    {
        Mage::getResourceModel('Mage_Sales_Model_Resource_Report_Order_Createdat')->aggregate($from, $to);
        Mage::getResourceModel('Mage_Sales_Model_Resource_Report_Order_Updatedat')->aggregate($from, $to);
        $this->_setFlagData(Magento_Reports_Model_Flag::REPORT_ORDER_FLAG_CODE);

        return $this;
    }
}
