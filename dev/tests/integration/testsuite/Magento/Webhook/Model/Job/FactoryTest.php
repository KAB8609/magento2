<?php
/**
 * Magento_Webhook_Model_Job_Factory
 *
 * @magentoDbIsolation enabled
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Webhook_Model_Job_FactoryTest extends PHPUnit_Framework_TestCase
{
    public function testCreate()
    {
        $factory = Mage::getObjectManager()->create('Magento_Webhook_Model_Job_Factory');
        $event = Mage::getModel('Magento_Webhook_Model_Event')
            ->setDataChanges(true)
            ->save();
        $subscription = Mage::getModel('Magento_Webhook_Model_Subscription')
            ->setDataChanges(true)
            ->save();
        $job = $factory->create($subscription, $event);

        $this->assertInstanceOf('Magento_Webhook_Model_Job', $job);
        $this->assertEquals($event->getId(), $job->getEventId());
        $this->assertEquals($subscription->getId(), $job->getSubscriptionId());
    }
}
