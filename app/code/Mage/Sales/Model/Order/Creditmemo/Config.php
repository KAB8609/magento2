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
 * Order creditmemo configuration model
 *
 * @category   Mage
 * @package    Mage_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Sales_Model_Order_Creditmemo_Config extends Mage_Sales_Model_Order_Total_Config_Base
{
    /**
     * Cache key for collectors
     *
     * @var string
     */
    protected $_collectorsCacheKey = 'sorted_order_creditmemo_collectors';

    /**
     * Constructor
     *
     * @param Mage_Core_Model_Cache_Type_Config $configCacheType
     */
    public function __construct(Mage_Core_Model_Cache_Type_Config $configCacheType)
    {
        parent::__construct($configCacheType, Mage::getConfig()->getNode('global/sales/order_creditmemo'));
    }
}
