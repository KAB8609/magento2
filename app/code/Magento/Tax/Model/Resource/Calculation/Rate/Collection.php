<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Tax
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Tax rate collection
 */
namespace Magento\Tax\Model\Resource\Calculation\Rate;

class Collection extends \Magento\Core\Model\Resource\Db\Collection\AbstractCollection
{
    /**
     * @var \Magento\Core\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @param \Magento\Core\Model\EntityFactory $entityFactory
     * @param \Magento\Logger $logger
     * @param \Magento\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Event\ManagerInterface $eventManager
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     * @param mixed $connection
     * @param \Magento\Core\Model\Resource\Db\AbstractDb $resource
     */
    public function __construct(
        \Magento\Core\Model\EntityFactory $entityFactory,
        \Magento\Logger $logger,
        \Magento\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Event\ManagerInterface $eventManager,
        \Magento\Core\Model\StoreManagerInterface $storeManager,
        $connection = null,
        \Magento\Core\Model\Resource\Db\AbstractDb $resource = null
    ) {
        $this->_storeManager = $storeManager;
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
    }

    /**
     * Resource initialization
     */
    protected function _construct()
    {
        $this->_init('Magento\Tax\Model\Calculation\Rate', 'Magento\Tax\Model\Resource\Calculation\Rate');
    }

    /**
     * Join country table to result
     *
     * @return \Magento\Tax\Model\Resource\Calculation\Rate\Collection
     */
    public function joinCountryTable()
    {
        $this->_select->join(
            array('country_table' => $this->getTable('directory_country')),
            'main_table.tax_country_id = country_table.country_id',
            array('country_name' => 'iso2_code')
        );

        return $this;
    }

    /**
     * Join Region Table
     *
     * @return \Magento\Tax\Model\Resource\Calculation\Rate\Collection
     */
    public function joinRegionTable()
    {
        $this->_select->joinLeft(
            array('region_table' => $this->getTable('directory_country_region')),
            'main_table.tax_region_id = region_table.region_id',
            array('region_name' => 'code')
        );
        return $this;
    }

    /**
     * Join rate title for specified store
     *
     * @param \Magento\Core\Model\Store|string|int $store
     * @return \Magento\Tax\Model\Resource\Calculation\Rate\Collection
     */
    public function joinTitle($store = null)
    {
        $storeId = (int)$this->_storeManager->getStore($store)->getId();
        $this->_select->joinLeft(
            array('title_table' => $this->getTable('tax_calculation_rate_title')),
            $this->getConnection()->quoteInto('main_table.tax_calculation_rate_id = title_table.tax_calculation_rate_id AND title_table.store_id = ?', $storeId),
            array('title' => 'value')
        );

        return $this;
    }

    /**
     * Joins store titles for rates
     *
     * @return \Magento\Tax\Model\Resource\Calculation\Rate\Collection
     */
    public function joinStoreTitles()
    {
        $storeCollection =  $this->_storeManager->getStores(true);
        foreach ($storeCollection as $store) {
            $tableAlias    = sprintf('title_table_%s', $store->getId());
            $joinCondition = implode(' AND ', array(
                "main_table.tax_calculation_rate_id = {$tableAlias}.tax_calculation_rate_id",
                $this->getConnection()->quoteInto($tableAlias . '.store_id = ?', $store->getId())
            ));
            $this->_select->joinLeft(
                array($tableAlias => $this->getTable('tax_calculation_rate_title')),
                $joinCondition,
                array($tableAlias => 'value')
            );
        }
        return $this;
    }

    /**
     * Add rate filter
     *
     * @param int $rateId
     * @return \Magento\Tax\Model\Resource\Calculation\Rate\Collection
     */
    public function addRateFilter($rateId)
    {
        if (is_int($rateId) && $rateId > 0) {
            return $this->addFieldToFilter('main_table.tax_rate_id', $rateId);
        }

        return $this;
    }

    /**
     * Retrieve option array
     *
     * @return array
     */
    public function toOptionArray()
    {
        return $this->_toOptionArray('tax_calculation_rate_id', 'code');
    }

    /**
     * Retrieve option hash
     *
     * @return array
     */
    public function toOptionHash()
    {
        return $this->_toOptionHash('tax_calculation_rate_id', 'code');
    }

    /**
     * Convert items array to hash for select options
     * unsing fetchItem method
     *
     * @see     _toOptionHashOptimized()
     *
     * @return  array
     */
    public function toOptionHashOptimized()
    {
        return $this->_toOptionHashOptimized('tax_calculation_rate_id', 'code');
    }
}

