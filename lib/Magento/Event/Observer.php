<?php
/**
 * {license_notice}
 *
 * @category   Magento
 * @package    \Magento\Event
 * @copyright  {copyright}
 * @license    {license_link}
 */


/**
 * Event observer object
 *
 * @category   Magento
 * @package    \Magento\Event
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Event;

class Observer extends \Magento\Object
{
    /**
     * Checkes the observer's event_regex against event's name
     *
     * @param \Magento\Event $event
     * @return boolean
     */
    public function isValidFor(\Magento\Event $event)
    {
        return $this->getEventName()===$event->getName();
    }

    /**
     * Dispatches an event to observer's callback
     *
     * @param \Magento\Event $event
     * @return \Magento\Event\Observer
     */
    public function dispatch(\Magento\Event $event)
    {
        if (!$this->isValidFor($event)) {
            return $this;
        }

        $callback = $this->getCallback();
        $this->setEvent($event);

        $_profilerKey = 'OBSERVER: '.(is_object($callback[0]) ? get_class($callback[0]) : (string)$callback[0]).' -> '.$callback[1];
        \Magento\Profiler::start($_profilerKey);
        call_user_func($callback, $this);
        \Magento\Profiler::stop($_profilerKey);

        return $this;
    }

    public function getName()
    {
        return $this->getData('name');
    }

    public function setName($data)
    {
        return $this->setData('name', $data);
    }

    public function getEventName()
    {
        return $this->getData('event_name');
    }

    public function setEventName($data)
    {
        return $this->setData('event_name', $data);
    }

    public function getCallback()
    {
        return $this->getData('callback');
    }

    public function setCallback($data)
    {
        return $this->setData('callback', $data);
    }

    /**
     * Get observer event object
     *
     * @return \Magento\Event
     */
    public function getEvent()
    {
        return $this->getData('event');
    }

    public function setEvent($data)
    {
        return $this->setData('event', $data);
    }
}
