<?php
/**
 * Magento_PubSub_Event
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_PubSub_EventTest extends PHPUnit_Framework_TestCase
{
    public function testGetters()
    {
        $bodyData = array('some' => 'body');
        $topic = 'topic';

        $event = new Magento_PubSub_Event($topic, $bodyData);

        $this->assertEquals(array(), $event->getHeaders());
        $this->assertEquals($bodyData, $event->getBodyData());
        $this->assertEquals($topic, $event->getTopic());
        $this->assertEquals(Magento_PubSub_Event::PREPARING, $event->getStatus());
    }

    public function testMarkProcessed()
    {
        $bodyData = array('some' => 'body');
        $topic = 'topic';
        $event = new Magento_PubSub_Event($topic, $bodyData);

        $event->markAsProcessed();

        $this->assertEquals(Magento_PubSub_Event::PROCESSED, $event->getStatus());
    }

    public function testMarkReadyToSend()
    {
        $bodyData = array('some' => 'body');
        $topic = 'topic';
        $event = new Magento_PubSub_Event($topic, $bodyData);

        $event->markAsReadyToSend();

        $this->assertEquals(Magento_PubSub_Event::READY_TO_SEND, $event->getStatus());
    }
}