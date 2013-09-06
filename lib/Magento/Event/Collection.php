<?php
/**
 * {license_notice}
 *
 * @category   Magento
 * @package    Magento_Event
 * @copyright  {copyright}
 * @license    {license_link}
 */


/**
 * Collection of events
 *
 * @category   Magento
 * @package    Magento_Event
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Event_Collection
{
    /**
     * Array of events in the collection
     *
     * @var array
     */
    protected $_events;
    
    /**
     * Global observers
     * 
     * For example regex observers will watch all events that 
     *
     * @var Magento_Event_Observer_Collection
     */
    protected $_observers;
    
    /**
     * Initializes global observers collection
     * 
     */
    public function __construct()
    {
        $this->_events = array();
        $this->_globalObservers = new Magento_Event_Observer_Collection();
    }
    
    /**
     * Returns all registered events in collection
     *
     * @return array
     */
    public function getAllEvents()
    {
        return $this->_events;
    }
    
    /**
     * Returns all registered global observers for the collection of events
     *
     * @return Magento_Event_Observer_Collection
     */
    public function getGlobalObservers()
    {
        return $this->_globalObservers;
    }
    
    /**
     * Returns event by its name
     *
     * If event doesn't exist creates new one and returns it
     * 
     * @param string $eventName
     * @return Magento_Event
     */
    public function getEventByName($eventName)
    {
        if (!isset($this->_events[$eventName])) {
            $this->addEvent(new Magento_Event(array('name'=>$eventName)));
        }
        return $this->_events[$eventName];
    }
    
    /**
     * Register an event for this collection
     *
     * @param Magento_Event $event
     * @return Magento_Event_Collection
     */
    public function addEvent(Magento_Event $event)
    {
        $this->_events[$event->getName()] = $event;
        return $this;
    }
    
    /**
     * Register an observer
     * 
     * If observer has event_name property it will be regitered for this specific event.
     * If not it will be registered as global observer
     *
     * @param Magento_Event_Observer $observer
     * @return Magento_Event_Collection
     */
    public function addObserver(Magento_Event_Observer $observer)
    {
        $eventName = $observer->getEventName();
        if ($eventName) {
            $this->getEventByName($eventName)->addObserver($observer);
        } else {
            $this->getGlobalObservers()->addObserver($observer);
        }
        return $this;
    }
    
    /**
     * Dispatch event name with optional data
     *
     * Will dispatch specific event and will try all global observers
     * 
     * @param string $eventName
     * @param array $data
     * @return Magento_Event_Collection
     */
    public function dispatch($eventName, array $data=array())
    {
        $event = $this->getEventByName($eventName);
        $event->addData($data)->dispatch();
        $this->getGlobalObservers()->dispatch($event);
        return $this;
    }
}