<?php
/**
 * Default no route handler
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Core_Model_Router_NoRouteHandler implements Magento_Core_Model_Router_NoRouteHandlerInterface
{
    /**
     * @var Magento_Core_Model_Config
     */
    protected $_config;

    /**
     * @param Magento_Core_Model_Config $config
     */
    public function __construct(Magento_Core_Model_Config $config)
    {
        $this->_config = $config;
    }

    /**
     * Check and process no route request
     *
     * @param Magento_Core_Controller_Request_Http $request
     * @return bool
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function process(Magento_Core_Controller_Request_Http $request)
    {
        $noRoutePath = $this->_config->getValue('web/default/no_route', 'default');

        if ($noRoutePath) {
            $noRoute = explode('/', $noRoutePath);
        } else {
            $noRoute = array();
        }

        $moduleName     = isset($noRoute[0]) ? $noRoute[0] : 'core';
        $controllerName = isset($noRoute[1]) ? $noRoute[1] : 'index';
        $actionName     = isset($noRoute[2]) ? $noRoute[2] : 'index';

        $request->setModuleName($moduleName)
            ->setControllerName($controllerName)
            ->setActionName($actionName);

        return true;
    }
}