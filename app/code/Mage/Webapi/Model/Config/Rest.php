<?php
/**
 * REST specific API config.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Webapi_Model_Config_Rest extends Mage_Webapi_Model_ConfigAbstract
{
    /** @var Magento_Controller_Router_Route_Factory */
    protected $_routeFactory;

    /**
     * Construct config with REST reader & route factory.
     *
     * @param Mage_Webapi_Model_Config_Reader_Rest $reader
     * @param Mage_Webapi_Helper_Config $helper
     * @param Mage_Core_Model_App $application
     * @param Magento_Controller_Router_Route_Factory $routeFactory
     */
    public function __construct(
        Mage_Webapi_Model_Config_Reader_Rest $reader,
        Mage_Webapi_Helper_Config $helper,
        Mage_Core_Model_App $application,
        Magento_Controller_Router_Route_Factory $routeFactory
    ) {
        parent::__construct($reader, $helper, $application);
        $this->_routeFactory = $routeFactory;
    }

    /**
     * Get all modules routes defined in config.
     *
     * @return Mage_Webapi_Controller_Router_Route_Rest[]
     * @throws LogicException When config data has invalid structure.
     */
    public function getAllRestRoutes()
    {
        $routes = array();
        foreach ($this->_data['rest_routes'] as $routePath => $routeData) {
            $routes[] = $this->_createRoute($routePath, $routeData['resourceName'], $routeData['actionType']);
        }
        return $routes;
    }

    /**
     * Retrieve a list of all route objects associated with specified method.
     *
     * @param string $resourceName
     * @param string $methodName
     * @param string $version
     * @return Mage_Webapi_Controller_Router_Route_Rest[]
     * @throws InvalidArgumentException
     */
    public function getMethodRestRoutes($resourceName, $methodName, $version)
    {
        $resourceData = $this->_getResourceData($resourceName, $version);
        if (!isset($resourceData['methods'][$methodName]['rest_routes'])) {
            throw new InvalidArgumentException(
                sprintf(
                    'The "%s" resource does not have any REST routes for "%s" method.',
                    $resourceName,
                    $methodName
                ));
        }
        $routes = array();
        foreach ($resourceData['methods'][$methodName]['rest_routes'] as $routePath) {
            $routes[] = $this->_createRoute(
                $routePath,
                $resourceName,
                Mage_Webapi_Controller_Request_Rest::getActionTypeByOperation($methodName)
            );
        }
        return $routes;
    }

    /**
     * Identify the shortest available route to the item of specified resource.
     *
     * @param string $resourceName
     * @return string
     * @throws InvalidArgumentException
     */
    public function getRestRouteToItem($resourceName)
    {
        $restRoutes = $this->_data['rest_routes'];
        /** The shortest routes must go first. */
        ksort($restRoutes);
        foreach ($restRoutes as $routePath => $routeMetadata) {
            if ($routeMetadata['actionType'] == Mage_Webapi_Controller_Request_Rest::ACTION_TYPE_ITEM
                && $routeMetadata['resourceName'] == $resourceName
            ) {
                return $routePath;
            }
        }
        throw new InvalidArgumentException(sprintf('No route to the item of "%s" resource was found.', $resourceName));
    }

    /**
     * Create route object.
     *
     * @param string $routePath
     * @param string $resourceName
     * @param string $actionType
     * @return Mage_Webapi_Controller_Router_Route_Rest
     */
    protected function _createRoute($routePath, $resourceName, $actionType)
    {
        $apiTypeRoutePath = $this->_application->getConfig()->getAreaFrontName()
            . '/:' . Mage_Webapi_Controller_Front::API_TYPE_REST;
        $fullRoutePath = $apiTypeRoutePath . $routePath;
        /** @var $route Mage_Webapi_Controller_Router_Route_Rest */
        $route = $this->_routeFactory->createRoute('Mage_Webapi_Controller_Router_Route_Rest', $fullRoutePath);
        $route->setResourceName($resourceName)->setResourceType($actionType);
        return $route;
    }
}