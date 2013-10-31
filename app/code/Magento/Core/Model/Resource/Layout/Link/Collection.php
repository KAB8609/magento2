<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Core\Model\Resource\Layout\Link;

/**
 * Layout update collection model
 */
class Collection extends \Magento\Core\Model\Resource\Db\Collection\AbstractCollection
{
    /**
     * @var \Magento\Stdlib\DateTime
     */
    protected $dateTime;

    /**
     * @param \Magento\Event\ManagerInterface $eventManager
     * @param \Magento\Logger $logger
     * @param \Magento\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Core\Model\EntityFactory $entityFactory
     * @param \Magento\Stdlib\DateTime $dateTime
     * @param \Magento\Core\Model\Resource\Db\AbstractDb $resource
     */
    public function __construct(
        \Magento\Event\ManagerInterface $eventManager,
        \Magento\Logger $logger,
        \Magento\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Core\Model\EntityFactory $entityFactory,
        \Magento\Stdlib\DateTime $dateTime,
        \Magento\Core\Model\Resource\Db\AbstractDb $resource = null
    ) {
        $this->dateTime = $dateTime;
        parent::__construct($eventManager, $logger, $fetchStrategy, $entityFactory, $resource);
    }

    /**
     * Define resource model
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init('Magento\Core\Model\Layout\Link', 'Magento\Core\Model\Resource\Layout\Link');
    }

    /**
     * Add filter by theme id
     *
     * @param int $themeId
     * @return $this
     */
    public function addThemeFilter($themeId)
    {
        $this->addFieldToFilter('theme_id', $themeId);
        return $this;
    }

    /**
     * Join with layout update table
     *
     * @param array $fields
     * @return \Magento\Core\Model\Resource\Layout\Link\Collection
     */
    protected function _joinWithUpdate($fields = array())
    {
        $flagName = 'joined_with_update_table';
        if (!$this->getFlag($flagName)) {
            $this->getSelect()
                ->join(
                    array('update' => $this->getTable('core_layout_update')),
                    'update.layout_update_id = main_table.layout_update_id',
                    array($fields)
                );
            $this->setFlag($flagName, true);
        }

        return $this;
    }

    /**
     * Filter by temporary flag
     *
     * @param bool $isTemporary
     * @return \Magento\Core\Model\Resource\Layout\Link\Collection
     */
    public function addTemporaryFilter($isTemporary)
    {
        $this->addFieldToFilter('main_table.is_temporary', $isTemporary ? 1 : 0);
        return $this;
    }

    /**
     * Get links for layouts that are older then specified number of days
     *
     * @param $days
     * @return \Magento\Core\Model\Resource\Layout\Link\Collection
     */
    public function addUpdatedDaysBeforeFilter($days)
    {
        $datetime = new \DateTime();
        $storeInterval = new \DateInterval('P' . $days . 'D');
        $datetime->sub($storeInterval);
        $formattedDate = $this->dateTime->formatDate($datetime->getTimestamp());

        $this->_joinWithUpdate();
        $this->addFieldToFilter('update.updated_at', array('notnull' => true))
            ->addFieldToFilter('update.updated_at', array('lt' => $formattedDate));

        return $this;
    }
}
