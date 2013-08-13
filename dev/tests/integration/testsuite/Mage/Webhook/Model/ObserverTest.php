<?php
/**
 * Mage_Webhook_Model_Observer
 *
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Webhook
 *
 * @copyright   {copyright}
 * @license     {license_link}
 *
 * @magentoDbIsolation enabled
 */
class Mage_Webhook_Model_ObserverTest extends PHPUnit_Framework_TestCase
{
    /** @var Mage_Webhook_Model_Subscription */
    private $_subscription;

    /** @var Magento_Webapi_Model_Acl_User */
    private $_user;

    /** @var Magento_Webapi_Model_Acl_Role */
    private $_role;

    /** @var Mage_Webhook_Model_Endpoint */
    private $_endpoint;

    /** @var Mage_Webhook_Model_Subscription_Factory */
    private $_subscriptionFactory;

    public function setUp()
    {
        $objectManager = Mage::getObjectManager();

        /** @var $factory Mage_Webhook_Model_Subscription_Factory */
        $this->_subscriptionFactory = Mage::getObjectManager()->create('Mage_Webhook_Model_Subscription_Factory');

        $this->_subscription = $objectManager->create('Mage_Webhook_Model_Subscription_Factory')
            ->create()
            ->setName('dummy')
            ->setEndpointUrl('http://localhost')
            ->save();

        $this->_role = $objectManager->create('Magento_Webapi_Model_Acl_Role')
            ->setData(array( 'role_name' => 'Test role'))
            ->save();

        $allowResourceId = 'test/get';
        /** @var Magento_Webapi_Model_Acl_Rule $rule */
        $rule = $objectManager->create('Magento_Webapi_Model_Acl_Rule');
        $rule->setData(array(
            'resource_id' => $allowResourceId,
            'role_id' => $this->_role->getId()
        ));
        $rule->save();

        $this->_user = Mage::getModel('Magento_Webapi_Model_Acl_User')
            ->setData(array(
            'api_key' => 'webhook_test_username',
            'secret' => 'webhook_test_secret',
            'contact_email' => 'null@null.com',
            'role_id' => $this->_role->getId()
        ))->save();

        $this->_endpoint = $objectManager->create('Mage_Webhook_Model_Endpoint')
            ->load($this->_subscription->getEndpointId());
    }

    public function testAfterWebapiUserDelete()
    {
        //setup
        $this->_subscription->setStatus(Magento_PubSub_SubscriptionInterface::STATUS_ACTIVE)
            ->save();

        //action
        $this->_user->delete();

        //verify
        $this->assertEquals((Magento_PubSub_SubscriptionInterface::STATUS_INACTIVE),
            $this->_subscriptionFactory->create()->load($this->_subscription->getId())->getStatus());
    }

    /**
     * @magentoConfigFixture               global/webhook/webhooks/test/hook/label 'Test Hook'
     */
    public function testAfterWebapiUserChange()
    {
        //setup
        $this->_subscription->setStatus(Magento_PubSub_SubscriptionInterface::STATUS_ACTIVE)
            ->setTopics(array('test/hook'))
            ->save();
        $this->_endpoint->setApiUserId($this->_user->getUserId())
            ->save();

        //action
        $this->_user->setSecret('new secret')->save();

        //verify
        $this->assertEquals((Magento_PubSub_SubscriptionInterface::STATUS_INACTIVE),
            $this->_subscriptionFactory->create()->load($this->_subscription->getId())->getStatus());
    }

    /**
     * @magentoConfigFixture               global/webhook/webhooks/test/hook/label 'Test Hook'
     */
    public function testAfterWebapiRoleChange()
    {
        //setup
        $this->_subscription->setStatus(Magento_PubSub_SubscriptionInterface::STATUS_ACTIVE)
            ->setTopics(array('test/hook'))
            ->save();
        $this->_endpoint->setApiUserId($this->_user->getUserId())
            ->save();

        //action
        $this->_role->setRoleName('a new name')->save();

        //verify
        $this->assertEquals((Magento_PubSub_SubscriptionInterface::STATUS_INACTIVE),
            $this->_subscriptionFactory->create()->load($this->_subscription->getId())->getStatus());
    }

    public function tearDown()
    {
        $this->_subscription->delete();
        $this->_user->delete();
        $this->_role->delete();
    }
}
