<?php
/**
 * Magento_Webhook_Model_Job_QueueReader
 *
 * @magentoDbIsolation enabled
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Webhook_Model_Job_QueueReaderTest extends PHPUnit_Framework_TestCase
{
    public function testPoll()
    {
        $event = Mage::getModel('Magento_Webhook_Model_Event')
            ->setDataChanges(true)
            ->save();

        $subscription = Mage::getModel('Magento_Webhook_Model_Subscription')
            ->setDataChanges(true)
            ->save();

        /** @var Magento_Webhook_Model_Job $job */
        $job = Mage::getObjectManager()->create('Magento_Webhook_Model_Job');
        $job->setEventId($event->getId());
        $job->setSubscriptionId($subscription->getId());

        $queueWriter = Mage::getObjectManager()->create('Magento_Webhook_Model_Job_QueueWriter');
        $queueWriter->offer($job);

        /** @var Magento_Webhook_Model_Job_QueueReader $queueReader */
        $queueReader = Mage::getObjectManager()->create('Magento_Webhook_Model_Job_QueueReader');
        $this->assertEquals($job->getId(), $queueReader->poll()->getId());

        $this->assertNull($queueReader->poll());
    }
}