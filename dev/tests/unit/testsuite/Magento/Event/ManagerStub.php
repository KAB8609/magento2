<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Event manager stub
 */

namespace Magento\Event;

class ManagerStub extends \Magento\Event\ManagerInterface
{
    /**
     * Stub dispatch event
     *
     * @param string $eventName
     * @param array $params
     * @return \Magento\Core\Model\App|null
     */
    public function dispatch($eventName, array $params = array())
    {
        switch ($eventName) {
            case 'cms_controller_router_match_before' :
                $params['condition']->setRedirectUrl('http://www.example.com/');
                break;
        }

        return null;
    }
}