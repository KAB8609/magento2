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
 * Sales report refunded collection
 *
 * @category    Magento
 * @package     Magento_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Sales_Model_Resource_Report_Refunded_Collection_Order
    extends Magento_Sales_Model_Resource_Report_Collection_Abstract
{
    /**
     * Period format
     *
     * @var string
     */
    protected $_periodFormat;

    /**
     * Selected columns
     *
     * @var array
     */
    protected $_selectedColumns    = array();

    /**
     * Initialize custom resource model
     *
     */
    public function __construct(
        Magento_Data_Collection_Db_FetchStrategyInterface $fetchStrategy,
        Magento_Sales_Model_Resource_Report $resource
    ) {
        $resource->init('sales_refunded_aggregated_order');
        parent::__construct($fetchStrategy, $resource);
    }

    /**
     * Retrieve selected columns
     *
     * @return array
     */
    protected function _getSelectedColumns()
    {
        $adapter = $this->getConnection();
        if ('month' == $this->_period) {
            $this->_periodFormat = $adapter->getDateFormatSql('period', '%Y-%m');
        } elseif ('year' == $this->_period) {
            $this->_periodFormat = $adapter->getDateExtractSql('period', Magento_DB_Adapter_Interface::INTERVAL_YEAR);
        } else {
            $this->_periodFormat = $adapter->getDateFormatSql('period', '%Y-%m-%d');
        }

        if (!$this->isTotals()) {
            $this->_selectedColumns = array(
                'period'            => $this->_periodFormat,
                'orders_count'      => 'SUM(orders_count)',
                'refunded'          => 'SUM(refunded)',
                'online_refunded'   => 'SUM(online_refunded)',
                'offline_refunded'  => 'SUM(offline_refunded)'
            );
        }

        if ($this->isTotals()) {
            $this->_selectedColumns = $this->getAggregatedColumns();
        }

        return $this->_selectedColumns;
    }

    /**
     * Add selected data
     *
     * @return Magento_Sales_Model_Resource_Report_Refunded_Collection_Order
     */
    protected function _initSelect()
    {
        $this->getSelect()->from($this->getResource()->getMainTable(), $this->_getSelectedColumns());
        if (!$this->isTotals()) {
            $this->getSelect()->group($this->_periodFormat);
        }
        return parent::_initSelect();
    }
}