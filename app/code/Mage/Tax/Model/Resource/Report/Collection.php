<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Tax
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Tax report collection
 *
 * @category    Mage
 * @package     Mage_Tax
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Tax_Model_Resource_Report_Collection extends Mage_Sales_Model_Resource_Report_Collection_Abstract
{
    /**
     * @var Zend_Db_Expr
     */
    protected $_periodFormat;

    /**
     * Aggregated Data Table
     *
     * @var string
     */
    protected $_aggregationTable = 'tax_order_aggregated_created';

    /**
     * @var array
     */
    protected $_selectedColumns    = array();

    /**
     * Initialize custom resource model
     *
     */
    public function __construct(
        Varien_Data_Collection_Db_FetchStrategyInterface $fetchStrategy,
        Mage_Sales_Model_Resource_Report $resource
    ) {
        $resource->init($this->_aggregationTable);
        parent::__construct($fetchStrategy, $resource);
    }

    /**
     * @return array
     */
    protected function _getSelectedColumns()
    {
        if ('month' == $this->_period) {
            $this->_periodFormat = $this->getConnection()->getDateFormatSql('period', '%Y-%m');
        } elseif ('year' == $this->_period) {
            $this->_periodFormat = $this->getConnection()->getDateFormatSql('period', '%Y');
        } else {
            $this->_periodFormat = $this->getConnection()->getDateFormatSql('period', '%Y-%m-%d');
        }

        if (!$this->isTotals() && !$this->isSubTotals()) {
            $this->_selectedColumns = array(
                'period'                => $this->_periodFormat,
                'code'                  => 'code',
                'percent'               => 'percent',
                'orders_count'          => 'SUM(orders_count)',
                'tax_base_amount_sum'   => 'SUM(tax_base_amount_sum)'
            );
        }

        if ($this->isTotals()) {
            $this->_selectedColumns = $this->getAggregatedColumns();
        }

        if ($this->isSubTotals()) {
            $this->_selectedColumns = $this->getAggregatedColumns() + array('period' => $this->_periodFormat);
        }

        return $this->_selectedColumns;
    }

    /**
     * Add selected data
     *
     * @return Mage_Tax_Model_Resource_Report_Collection
     */
    protected function _initSelect()
    {
        $this->getSelect()->from($this->getResource()->getMainTable(), $this->_getSelectedColumns());
        if (!$this->isTotals() && !$this->isSubTotals()) {
            $this->getSelect()->group(array($this->_periodFormat, 'code', 'percent'));
        }

        if ($this->isSubTotals()) {
            $this->getSelect()->group(array(
                $this->_periodFormat
            ));
        }
        return parent::_initSelect();
    }
}
