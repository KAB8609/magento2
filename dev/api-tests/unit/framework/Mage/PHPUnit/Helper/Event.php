<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_PHPUnit
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Helper class for observer's events.
 *
 * @category    Mage
 * @package     Mage_PHPUnit
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_PHPUnit_Helper_Event extends Mage_PHPUnit_Helper_Abstract
{
    /**
     * Remove event observers from config
     *
     * @param array $eventNames - remove observers for events from this array. if array is empty, remove for all events
     * @return Mage_PHPUnit_Helper_Event
     */
    public function disableObservers($eventNames = array())
    {
        $events = array();
        foreach ($eventNames as $name) {
            $events[] = "name() = '{$name}'";
        }
        $query = !empty($events) ? "[" . implode(' or ', $events) . "]" : "";

        $elements = Mage::getConfig()->getXpath("//*/events/*" . $query);
        foreach ($elements as $element) {
            $element->setNode('observers', null);
        }

        return $this;
    }

    /**
     * Adds event to observer.
     *
     * @param string $eventName
     * @param string $observerName
     * @param string $modelName
     * @param string $methodName
     */
    public function addObserverToEvent($eventName, $observerName, $modelName, $methodName)
    {
        $eventNode = new Varien_Simplexml_Element(
            "<config>
                <global>
                    <events>
                        <{$eventName}>
                            <observers>
                                <{$observerName}>
                                    <type>singleton</type>
                                    <class>{$modelName}</class>
                                    <method>{$methodName}</method>
                                 </{$observerName}>
                            </observers>
                        </{$eventName}>
                    </events>
                </global>
            </config>"
        );
        Mage::getConfig()->getNode()->extend($eventNode);
    }
}
