<?php
/**
 * Subscription collection resource
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Webhook
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Webhook\Model\Resource\Subscription;

class Collection
    extends \Magento\Core\Model\Resource\Db\Collection\AbstractCollection
    implements \Magento\PubSub\Subscription\CollectionInterface
{
    /**
     * @var \Magento\Webhook\Model\Resource\Endpoint
     */
    protected $_endpointResource;

    /**
     * @param \Magento\Core\Model\EntityFactory $entityFactory
     * @param \Magento\Logger $logger
     * @param \Magento\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Event\ManagerInterface $eventManager
     * @param \Magento\Webhook\Model\Resource\Endpoint $endpointResource
     * @param mixed $connection
     * @param \Magento\Core\Model\Resource\Db\AbstractDb $resource
     */
    public function __construct(
        \Magento\Core\Model\EntityFactory $entityFactory,
        \Magento\Logger $logger,
        \Magento\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Event\ManagerInterface $eventManager,
        \Magento\Webhook\Model\Resource\Endpoint $endpointResource,
        $connection = null,
        \Magento\Core\Model\Resource\Db\AbstractDb $resource = null
    ) {
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
        $this->_endpointResource = $endpointResource;
    }

    /**
     * Initialization here
     */
    public function _construct()
    {
        parent::_construct();
        $this->_init('Magento\Webhook\Model\Subscription', 'Magento\Webhook\Model\Resource\Subscription');
    }


    /**
     * Return all subscriptions by topic
     *
     * @param string $topic
     * @return \Magento\PubSub\SubscriptionInterface[]
     */
    public function getSubscriptionsByTopic($topic)
    {
        return $this->clearFilters()
            ->addTopicFilter($topic)
            ->addIsActiveFilter(true)
            ->clear()
            ->getItems();
    }

    /**
     * Returns all subscriptions that match a given alias
     *
     * @param string $alias
     * @return \Magento\Webhook\Model\Subscription[]
     */
    public function getSubscriptionsByAlias($alias)
    {
        return $this->clearFilters()
            ->addAliasFilter($alias)
            ->clear()
            ->getItems();
    }

    /**
     * Get subscriptions whose endpoint has no api user
     *
     * @return \Magento\Webhook\Model\Subscription[]
     */
    public function getActivatedSubscriptionsWithoutApiUser()
    {
        $endpointIds = $this->_endpointResource->getEndpointsWithoutApiUser();

        return $this->clearFilters()
            ->addEndpointIdsFilter($endpointIds)
            ->addNotInactiveFilter()
            ->clear()
            ->getItems();
    }

    /**
     * Get api user subscriptions
     *
     * @param int|int[] $apiUserIds
     * @return \Magento\Webhook\Model\Subscription[]
     */
    public function getApiUserSubscriptions($apiUserIds)
    {
        $endpointIds = $this->_endpointResource->getApiUserEndpoints($apiUserIds);

        return $this->clearFilters()
            ->addEndpointIdsFilter($endpointIds)
            ->clear()
            ->getItems();
    }

    /**
     * Clear the select object
     *
     * @return \Magento\Webhook\Model\Resource\Subscription\Collection
     */
    public function clearFilters()
    {
        $this->_select = $this->_conn->select();
        $this->_initSelect();
        return $this;
    }

    /**
     * Select subscriptions whose endpoint's id is in given array
     *
     * @param array $endpointIds
     * @return \Magento\Webhook\Model\Resource\Subscription\Collection
     */
    public function addEndpointIdsFilter($endpointIds)
    {
        $this->getSelect()->where('endpoint_id IN (?)', $endpointIds);

        return $this;
    }

    /**
     * Add filter by topic field to collection
     *
     * @param string $topic
     * @return \Magento\Webhook\Model\Resource\Subscription\Collection
     */
    public function addTopicFilter($topic)
    {
        $this->getSelect()
            ->joinInner(array('hooks' => $this->getTable('webhook_subscription_hook')),
                $this->getConnection()
                    ->quoteInto('hooks.subscription_id=main_table.subscription_id AND hooks.topic=?', $topic)
        );
        return $this;
    }

    /**
     * Add filter by alias field to collection
     * 
     * @param string|array $alias
     * @return \Magento\Webhook\Model\Resource\Subscription\Collection
     */
    public function addAliasFilter($alias)
    {
        $this->addFieldToFilter('alias', $alias);
        return $this;
    }

    /**
     * Adds filter by status field to collection based on parameter
     *
     * @param bool $isActive
     * @return \Magento\Webhook\Model\Resource\Subscription\Collection
     */
    public function addIsActiveFilter($isActive)
    {
        if ($isActive) {
            $this->addFieldToFilter('status', \Magento\PubSub\SubscriptionInterface::STATUS_ACTIVE);
        } else {
            $this->addFieldToFilter('status', \Magento\PubSub\SubscriptionInterface::STATUS_INACTIVE);
        }
        return $this;
    }

    /**
     * Filter out anything in the INACTIVE state
     *
     * @return \Magento\Webhook\Model\Resource\Subscription\Collection
     */
    public function addNotInactiveFilter()
    {
        $this->getSelect()->where('status IN (?)', array(
            \Magento\Webhook\Model\Subscription::STATUS_ACTIVE,
            \Magento\Webhook\Model\Subscription::STATUS_REVOKED));

        return $this;
    }
}
