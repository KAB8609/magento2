<?php
/**
 * Configures subscriptions based on information from config object
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Webhook
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Webhook\Model\Subscription;

class Config
{
    /**
     * @var \Magento\Webhook\Model\Resource\Subscription\Collection
     */
    protected $_subscriptionSet;

    /**
     * @var \Magento\Webhook\Model\Config
     */
    protected $_config;

    /**
     * @var Factory
     */
    protected $_subscriptionFactory;

    /**
     * @var \Magento\Core\Model\Logger
     */
    private $_logger;

    /**
     * @param \Magento\Webhook\Model\Resource\Subscription\Collection $subscriptionSet
     * @param \Magento\Webhook\Model\Config $config
     * @param \Magento\Webhook\Model\Subscription\Factory $subscriptionFactory
     * @param \Magento\Core\Model\Logger $logger
     */
    public function __construct(
        \Magento\Webhook\Model\Resource\Subscription\Collection $subscriptionSet,
        \Magento\Webhook\Model\Config $config,
        \Magento\Webhook\Model\Subscription\Factory $subscriptionFactory,
        \Magento\Core\Model\Logger $logger
    ) {
        $this->_subscriptionSet = $subscriptionSet;
        $this->_config = $config;
        $this->_subscriptionFactory = $subscriptionFactory;
        $this->_logger = $logger;
    }

    /**
     * Checks if new subscriptions need to be generated from config files
     *
     * @return \Magento\Webhook\Model\Subscription\Config
     */
    public function updateSubscriptionCollection()
    {
        foreach ($this->_config->getSubscriptions() as $alias => $subscriptionData) {
            try {
                $this->_validateConfigData($subscriptionData, $alias);
                $subscriptions = $this->_subscriptionSet->getSubscriptionsByAlias($alias);
                if (empty($subscriptions)) {
                    // add new subscription
                    $subscription = $this->_subscriptionFactory->create()
                        ->setAlias($alias)
                        ->setStatus(\Magento\Webhook\Model\Subscription::STATUS_INACTIVE);
                } else {
                    // get first subscription from array
                    $subscription = current($subscriptions);
                }

                // update subscription from config
                $this->_updateSubscriptionFromConfigData($subscription, $subscriptionData);
            } catch (\LogicException $e){
                $this->_logger->logException(new \Magento\Webhook\Exception($e->getMessage()));
            }
        }
        return $this;
    }

    /**
     * Validates config data by checking that $data is an array and that 'data' maps to some value
     *
     * @param mixed $data
     * @param string $alias
     * @throws \LogicException
     */
    protected function _validateConfigData($data, $alias)
    {
        //  We can't demand that every possible value be supplied as some of these can be supplied
        //  at a later point in time using the web API
        if (!( is_array($data) && isset($data['name']))) {
            throw new \LogicException(__(
                "Invalid config data for subscription '%1'.", $alias
            ));
        }
    }

    /**
     * Configures a subscription
     *
     * @param \Magento\Webhook\Model\Subscription $subscription
     * @param array $rawConfigData
     * @return \Magento\Core\Model\AbstractModel
     */
    protected function _updateSubscriptionFromConfigData(
        \Magento\Webhook\Model\Subscription $subscription,
        array $rawConfigData
    ) {
        // Set defaults for unset values
        $configData = $this->_processConfigData($rawConfigData);

        $subscription->setName($configData['name'])
            ->setFormat($configData['format'])
            ->setEndpointUrl($configData['endpoint_url'])
            ->setTopics($configData['topics'])
            ->setAuthenticationType($configData['authentication_type'])
            ->setRegistrationMechanism($configData['registration_mechanism']);

        return $subscription->save();
    }

    /**
     * Sets defaults for unset values
     *
     * @param array $configData
     * @return array
     */
    private function _processConfigData($configData)
    {
        $defaultData = array(
            'name' => null,
            'format' => \Magento\Outbound\EndpointInterface::FORMAT_JSON,
            'endpoint_url' => null,
            'topics' => array(),
            'authentication_type' => \Magento\Outbound\EndpointInterface::AUTH_TYPE_NONE,
            'registration_mechanism' => \Magento\Webhook\Model\Subscription::REGISTRATION_MECHANISM_MANUAL,
        );

        if (isset($configData['topics'])) {
            $configData['topics'] = $this->_getTopicsFlatList($configData['topics']);
        }

        return array_merge($defaultData, $configData);
    }

    /**
     * Convert topics into acceptable form for subscription
     *
     * @param array $topics
     * @return array
     */
    protected function _getTopicsFlatList(array $topics)
    {
        $flatList = array();

        foreach ($topics as $topicGroup => $topicNames) {
            $topicNamesKeys = array_keys($topicNames);
            foreach ($topicNamesKeys as $topicName) {
                $flatList[] = $topicGroup . '/' . $topicName;
            }
        }

        return $flatList;
    }
}
