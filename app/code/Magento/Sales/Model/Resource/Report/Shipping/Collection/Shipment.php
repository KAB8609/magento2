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
 * Sales report shipping collection
 *
 * @category    Magento
 * @package     Magento_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Sales_Model_Resource_Report_Shipping_Collection_Shipment
    extends Magento_Sales_Model_Resource_Report_Shipping_Collection_Order
{
    /**
     * Initialize custom resource model
     *
     */
    public function __construct(
        Magento_Data_Collection_Db_FetchStrategyInterface $fetchStrategy,
        Magento_Sales_Model_Resource_Report $resource
    ) {
        $resource->init('sales_shipping_aggregated');
        parent::__construct($fetchStrategy, $resource);
    }
}