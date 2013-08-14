<?php
/**
 * Magento_PubSub_Message_DispatcherAsync
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * @magentoDbIsolation enabled
 */
class Magento_PubSub_Message_DispatcherAsyncTests extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_PubSub_Message_DispatcherAsync
     */
    protected $_model;

    /**
     * Initialize the model
     */
    public function setUp()
    {
        /** @var Mage_Webhook_Model_Resource_Event_Collection $eventCollection */
        $eventCollection = Mage::getObjectManager()->create('Mage_Webhook_Model_Resource_Event_Collection');
        /** @var array $event */
        $events = $eventCollection->getItems();
        /** @var Mage_Webhook_Model_Event $event */
        foreach ($events as $event) {
            $event->markAsProcessed();
            $event->save();
        }

        $this->_model = Mage::getObjectManager()->create('Magento_PubSub_Message_DispatcherAsync');
    }

    /**
     * Test the maing flow of event dispatching
     */
    public function testDispatch()
    {
        $topic = 'webhooks/dispatch/tested';

        $data = array(
            'testKey' => 'testValue'
        );

        $this->_model->dispatch($topic, $data);

        $queue = Mage::getObjectManager()->get('Magento_PubSub_Event_QueueReaderInterface');
        $event = $queue->poll();

        $this->assertEquals($topic, $event->getTopic());
        $this->assertEquals($data, $event->getBodyData());
    }
}