<?php
/**
 * Dispatcher for SOAP API calls.
 *
 * @copyright {}
 */
class Mage_Webapi_Controller_Dispatcher_Soap extends Mage_Webapi_Controller_DispatcherAbstract
{
    /**
     * Cache ID for generated WSDL content.
     */
    const WSDL_CACHE_ID = 'WSDL';

    /** @var Mage_Webapi_Model_Config_Soap */
    protected $_apiConfig;

    /** @var Mage_Webapi_Model_Soap_Server */
    protected $_soapServer;

    /** @var Mage_Webapi_Model_Soap_AutoDiscover */
    protected $_autoDiscover;

    /** @var Mage_Core_Model_Cache */
    protected $_cache;

    /** @var Mage_Webapi_Controller_Request_Soap */
    protected $_request;

    /** @var Mage_Webapi_Model_Soap_Fault */
    protected $_soapFault;

    /** @var Mage_Webapi_Controller_Response */
    protected $_response;

    /**
     * Initialize dependencies.
     *
     * @param Mage_Webapi_Helper_Data $helper
     * @param Mage_Webapi_Model_Config_Soap $apiConfig
     * @param Mage_Webapi_Controller_Request_Soap $request
     * @param Mage_Webapi_Controller_Response $response
     * @param Mage_Webapi_Model_Soap_AutoDiscover $autoDiscover
     * @param Mage_Webapi_Model_Soap_Server $soapServer
     * @param Mage_Core_Model_Cache $cache
     * @param Mage_Webapi_Model_Soap_Fault $soapFault
     */
    public function __construct(
        Mage_Webapi_Helper_Data $helper,
        Mage_Webapi_Model_Config_Soap $apiConfig,
        Mage_Webapi_Controller_Request_Soap $request,
        Mage_Webapi_Controller_Response $response,
        Mage_Webapi_Model_Soap_AutoDiscover $autoDiscover,
        Mage_Webapi_Model_Soap_Server $soapServer,
        Mage_Core_Model_Cache $cache,
        Mage_Webapi_Model_Soap_Fault $soapFault
    ) {
        parent::__construct($helper);
        $this->_apiConfig = $apiConfig;
        $this->_autoDiscover = $autoDiscover;
        $this->_soapServer = $soapServer;
        $this->_cache = $cache;
        $this->_request = $request;
        $this->_soapFault = $soapFault;
        $this->_response = $response;
    }

    /**
     * Dispatch request to SOAP endpoint.
     *
     * @return Mage_Webapi_Controller_Dispatcher_Soap
     */
    public function dispatch()
    {
        try {
            if ($this->_request->getParam(Mage_Webapi_Model_Soap_Server::REQUEST_PARAM_WSDL) !== null) {
                $this->_setResponseContentType('text/xml');
                $responseBody = $this->_getWsdlContent();
            } else {
                $this->_setResponseContentType('application/soap+xml');
                $responseBody = $this->_soapServer->handle();
            }
            $this->_setResponseBody($responseBody);
        } catch (Mage_Webapi_Exception $e) {
            self::_processBadRequest($e->getMessage());
        } catch (Exception $e) {
            self::_processBadRequest($this->_helper->__('Internal error.'));
        }

        $this->_response->sendResponse();
        return $this;
    }


    /**
     * Retrieve SOAP API config.
     *
     * @return Mage_Webapi_Model_Config_Soap
     */
    public function getApiConfig()
    {
        return $this->_apiConfig;
    }

    /**
     * Process request as HTTP 400 and set error message.
     *
     * @param string $message
     */
    protected function _processBadRequest($message)
    {
        $this->_setResponseContentType('text/xml');
        $this->_response->setHttpResponseCode(400);
        $details = array();
        $resourceConfig = $this->getApiConfig();
        if (!is_null($resourceConfig)) {
            foreach ($resourceConfig->getAllResourcesVersions() as $resourceName => $versions) {
                foreach ($versions as $version) {
                    $details['availableResources'][$resourceName][$version] = sprintf(
                        '%s?wsdl&resources[%s]=%s',
                        $this->_soapServer->getEndpointUri(),
                        $resourceName,
                        $version
                    );
                }
            }
        }
        $this->_setResponseBody(
            $this->_soapFault->getSoapFaultMessage(
                $message,
                Mage_Webapi_Controller_Dispatcher_Soap_Handler::FAULT_CODE_SENDER,
                'en',
                $details
            )
        );
    }

    /**
     * Generate WSDL content based on resource config.
     *
     * @return string
     * @throws Mage_Webapi_Exception
     */
    protected function _getWsdlContent()
    {
        $requestedResources = $this->_request->getRequestedResources();
        $cacheId = self::WSDL_CACHE_ID . hash('md5', serialize($requestedResources));
        if ($this->_cache->canUse(Mage_Webapi_Model_ConfigAbstract::WEBSERVICE_CACHE_NAME)) {
            $cachedWsdlContent = $this->_cache->load($cacheId);
            if ($cachedWsdlContent !== false) {
                return $cachedWsdlContent;
            }
        }

        $resources = array();
        try {
            foreach ($requestedResources as $resourceName => $resourceVersion) {
                $resources[$resourceName] = $this->getApiConfig()
                    ->getResourceDataMerged($resourceName, $resourceVersion);
            }
        } catch (Exception $e) {
            throw new Mage_Webapi_Exception($e->getMessage(), Mage_Webapi_Exception::HTTP_BAD_REQUEST);
        }

        $wsdlContent = $this->_autoDiscover->generate($resources, $this->_soapServer->generateUri());

        if ($this->_cache->canUse(Mage_Webapi_Model_ConfigAbstract::WEBSERVICE_CACHE_NAME)) {
            $this->_cache->save($wsdlContent, $cacheId, array(Mage_Webapi_Model_ConfigAbstract::WEBSERVICE_CACHE_TAG));
        }

        return $wsdlContent;
    }

    /**
     * Set content type to response object.
     *
     * @param string $contentType
     * @return Mage_Webapi_Controller_Dispatcher_Soap
     */
    protected function _setResponseContentType($contentType = 'text/xml')
    {
        $this->_response->clearHeaders()
            ->setHeader('Content-Type', "$contentType; charset={$this->_soapServer->getApiCharset()}");
        return $this;
    }

    /**
     * Set body to response object.
     *
     * @param string $responseBody
     * @return Mage_Webapi_Controller_Dispatcher_Soap
     */
    protected function _setResponseBody($responseBody)
    {
        $this->_response->setBody(
            preg_replace(
                '/<\?xml version="([^\"]+)"([^\>]+)>/i',
                '<?xml version="$1" encoding="' . $this->_soapServer->getApiCharset() . '"?>',
                $responseBody
            )
        );
        return $this;
    }
}
