<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Rating
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Rating grid collection
 *
 * @category    Magento
 * @package     Magento_Rating
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Rating\Model\Resource\Rating\Grid;

class Collection extends \Magento\Rating\Model\Resource\Rating\Collection
{
    /**
     * Core registry
     *
     * @var \Magento\Core\Model\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\Event\ManagerInterface $eventManager
     * @param \Magento\Logger $logger
     * @param \Magento\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Core\Model\EntityFactory $entityFactory
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     * @param \Magento\Rating\Model\Resource\Rating\Option\CollectionFactory $ratingCollectionF
     * @param \Magento\Core\Model\Registry $coreRegistry
     * @param \Magento\Core\Model\Resource\Db\AbstractDb $resource
     */
    public function __construct(
        \Magento\Event\ManagerInterface $eventManager,
        \Magento\Logger $logger,
        \Magento\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Core\Model\EntityFactory $entityFactory,
        \Magento\Core\Model\StoreManagerInterface $storeManager,
        \Magento\Rating\Model\Resource\Rating\Option\CollectionFactory $ratingCollectionF,
        \Magento\Core\Model\Registry $coreRegistry,
        \Magento\Core\Model\Resource\Db\AbstractDb $resource = null
    ) {
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($eventManager, $logger, $fetchStrategy, $entityFactory, $storeManager, $ratingCollectionF,
            $resource);
    }

    /**
     * Add entity filter
     *
     * @return \Magento\Core\Model\Resource\Db\Collection\AbstractCollection|\Magento\Rating\Model\Resource\Rating\Grid\Collection
     */
    public function _initSelect()
    {
        parent::_initSelect();
        $this->addEntityFilter($this->_coreRegistry->registry('entityId'));
        return $this;
    }
}
