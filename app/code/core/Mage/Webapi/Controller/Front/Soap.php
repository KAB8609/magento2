<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Webapi
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Front controller for SOAP API. At the same time it is a handler for SOAP server
 */
// TODO: Add profiler calls
class Mage_Webapi_Controller_Front_Soap extends Mage_Webapi_Controller_FrontAbstract
{
    const BASE_ACTION_CONTROLLER = 'Mage_Webapi_Controller_ActionAbstract';

    const FAULT_CODE_SENDER = 'Sender';
    const FAULT_CODE_RECEIVER = 'Receiver';

    const FAULT_REASON_INTERNAL = 'Internal Error.';

    const WEBSERVICE_CACHE_NAME = 'config_webservice';
    const WEBSERVICE_CACHE_TAG = 'WEBSERVICE';
    const WSDL_CACHE_ID = 'WSDL';

    /** @var Zend_Soap_Server */
    protected $_soapServer;

    /** @var Mage_Webapi_Model_Config_Soap */
    protected $_soapConfig;

    /**
     * WS-Security UsernameToken object from request
     *
     * @var stdClass
     */
    protected $_usernameTokenRequest;

    /**
     * Handler for all SOAP operations
     *
     * @param string $operation
     * @param array $arguments
     * @return stdClass
     */
    // TODO: Think about situations when custom error handler is required for this method (that can throw SOAP faults)
    public function __call($operation, $arguments)
    {
        if (in_array($operation, $this->_getRequestedHeaders())) {
            $this->_processSoapHeader($operation, $arguments);
        } else {
            $role = $this->_authenticate();
            $this->_checkOperationDeprecation($operation);
            $resourceName = $this->getResourceConfig()->getResourceNameByOperation($operation);
            if (!$resourceName) {
                $this->_soapFault(sprintf('Method "%s" not found.', $operation), self::FAULT_CODE_SENDER);
            }
            $controllerClass = $this->getSoapConfig()->getControllerClassByResourceName($resourceName);
            $controllerInstance = $this->_getActionControllerInstance($controllerClass);
            $method = $this->getResourceConfig()->getMethodNameByOperation($operation);
            try {
                $this->_checkResourceAcl($role, $resourceName, $method);

                $arguments = reset($arguments);
                /** @var Mage_Api_Helper_Data $apiHelper */
                $apiHelper = Mage::helper('Mage_Api_Helper_Data');
                $this->getHelper()->toArray($arguments);
                $action = $method . $this->_getVersionSuffix($operation, $controllerInstance);
                $arguments = $this->getHelper()->prepareMethodParams($controllerClass, $action, $arguments);
//            $inputData = $this->_presentation->fetchRequestData($operation, $controllerInstance, $action);
                $outputData = call_user_func_array(array($controllerInstance, $action), $arguments);
                // TODO: Implement response preparation according to current presentation
//            $this->_presentation->prepareResponse($operation, $outputData);
                // TODO: Move wsiArrayPacker from helper to this class
                $obj = $apiHelper->wsiArrayPacker($outputData);
                $stdObj = new stdClass();
                $stdObj->result = $obj;
                return $stdObj;
            } catch (Mage_Webapi_Exception $e) {
                $this->_soapFault($e->getMessage(), $e->getOriginator(), $e);
            } catch (Exception $e) {
                if (!Mage::getIsDeveloperMode()) {
                    Mage::logException($e);
                    $this->_soapFault($this->_helper->__("Internal Error. Details are available in Magento log file."));
                } else {
                    $this->_soapFault($this->_helper->__("Internal Error."), self::FAULT_CODE_RECEIVER, $e);
                }
            }
        }
    }

    /**
     * Handle SOAP headers.
     *
     * @param string $header
     * @param array $arguments
     */
    protected function _processSoapHeader($header, $arguments)
    {
        switch ($header) {
            case 'Security':
                foreach ($arguments as $argument) {
                    if (is_object($argument) && isset($argument->UsernameToken)) {
                        $this->_usernameTokenRequest = $argument->UsernameToken;
                    }
                }
                break;
        }
    }

    /**
     * Get SOAP Header names from request.
     *
     * @return array
     */
    protected function _getRequestedHeaders()
    {
        $dom = new DOMDocument();
        $dom->loadXML($this->_getSoapServer()->getLastRequest());
        $headers = array();
        /** @var DOMElement $header */
        foreach ($dom->getElementsByTagName('Header')->item(0)->childNodes as $header) {
            list($headerNs, $headerName) = explode(":", $header->nodeName);
            $headers[] = $headerName;
        }

        return $headers;
    }

    /**
     * Authenticate user
     *
     * @return string
     */
    protected function _authenticate()
    {
        $roleId = null;
        if (is_null($this->_usernameTokenRequest)) {
            $this->_soapFault($this->_helper->__('No WS-Security UsernameToken found in SOAP-request.'),
                self::FAULT_CODE_SENDER);
        }

        try {
            $usernameToken = Mage::getModel('Mage_Webapi_Model_Soap_Security_UsernameToken', array(
                'username' => $this->_usernameTokenRequest->Username,
                'passwordType' => Mage_Webapi_Model_Soap_Security_UsernameToken::PASSWORD_TYPE_DIGEST,
                'password' => $this->_usernameTokenRequest->Password,
                'nonce' => $this->_usernameTokenRequest->Nonce,
                'created' => $this->_usernameTokenRequest->Created
            ));
            /** @var Mage_Webapi_Model_Authorization_Soap_RoleLocator $roleLocator */
            $roleLocator = Mage::getModel('Mage_Webapi_Model_Authorization_Soap_RoleLocator', array(
                'usernameToken' => $usernameToken
            ));

            $roleId = $roleLocator->getAclRoleId();
        } catch (Mage_Webapi_Model_Soap_Security_UsernameToken_NonceUsedException $e) {
            $this->_soapFault($this->_helper->__('WS-Security UsernameToken Nonce is already used.'),
                self::FAULT_CODE_SENDER);
        } catch (Mage_Webapi_Model_Soap_Security_UsernameToken_TimestampRefusedException $e) {
            $this->_soapFault($this->_helper->__('WS-Security UsernameToken Created timestamp is refused.'),
                self::FAULT_CODE_SENDER);
        } catch(Mage_Webapi_Model_Soap_Security_UsernameToken_InvalidCredentialException $e) {
            $this->_soapFault($this->_helper->__('Invalid Username or Password.'), self::FAULT_CODE_SENDER);
        } catch (Exception $e) {
            $this->_soapFault($this->_helper->__('Error during authenticating SOAP-request.'), self::FAULT_CODE_SENDER,
                $e);
        }

        return $roleId;
    }

    /**
     * Get SOAP request.
     *
     * @return Mage_Webapi_Controller_Request_Soap
     */
    public function getRequest()
    {
        return $this->_request;
    }

    /**
     * Extend parent with SOAP specific config initialization
     *
     * @return Mage_Webapi_Controller_Front_Soap|Mage_Core_Controller_FrontInterface
     */
    public function init()
    {
        $soapConfigFiles = Mage::getConfig()->getModuleConfigurationFiles('webapi/soap.xml');
        /** @var Mage_Webapi_Model_Config_Soap $soapConfig */
        $soapConfig = Mage::getModel('Mage_Webapi_Model_Config_Soap', $soapConfigFiles);
        $this->setSoapConfig($soapConfig);
        return $this;
    }

    /**
     * Dispatch request to SOAP endpoint.
     *
     * @return Mage_Webapi_Controller_Front_Soap
     */
    public function dispatch()
    {
        try {
            $this->_initResourceConfig($this->getRequest()->getRequestedModules());
            if ($this->getRequest()->getParam('wsdl') !== null) {
                $this->_setResponseContentType('text/xml');
                $responseBody = $this->_getWsdlContent();
            } else {
                $this->_setResponseContentType('application/soap+xml');
                $responseBody = $this->_getSoapServer()->handle();
            }
            $this->_setResponseBody($responseBody);
        } catch (Mage_Webapi_Exception $e) {
            self::_processBadRequest($e->getMessage());
        } catch (Exception $e) {
            self::_processBadRequest($this->_helper->__('Internal error.'));
        }

        $this->getResponse()->sendResponse();
        return $this;
    }

    /**
     * Process request as HTTP 400 and set error message.
     *
     * @param string $message
     */
    protected function _processBadRequest($message)
    {
        $this->_setResponseContentType('text/xml');
        $this->getResponse()->setHttpResponseCode(400);

        $apiUrl = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB) . 'api/soap';
        // TODO: Collect the following details dynamically after Auto Discovery proposal implementation
        $details = array(
            "availableResources" => array(
                'customers' => array(
                    'v1' => $apiUrl . '?wsdl&modules[Mage_Customer]=v1',
                    'v2' => $apiUrl . '?wsdl&modules[Mage_Customer]=v2',
                ),
                'catalogProducts' => array(
                    'v1' => $apiUrl . '?wsdl&modules[Mage_Catalog]=v1',
                ),
            )
        );
        $this->_setResponseBody($this->_getSoapFaultMessage($message, self::FAULT_CODE_SENDER, 'en', $details));
    }

    /**
     * Generate WSDL content based on resource config.
     *
     * @return string
     */
    protected function _getWsdlContent()
    {
        $requestedModules = $this->getRequest()->getRequestedModules();
        $cacheId = self::WSDL_CACHE_ID . hash('md5', serialize($requestedModules));
        if (Mage::app()->getCacheInstance()->canUse(self::WEBSERVICE_CACHE_NAME)) {
            $cachedWsdlContent = Mage::app()->getCacheInstance()->load($cacheId);
            if ($cachedWsdlContent !== false) {
                return $cachedWsdlContent;
            }
        }

        /** @var Mage_Webapi_Model_Config_Wsdl $wsdlConfig */
        $wsdlConfig = Mage::getModel('Mage_Webapi_Model_Config_Wsdl', array(
            'resource_config' => $this->getResourceConfig(),
            'endpoint_url' => $this->_getEndpointUrl(),
        ));
        $wsdlContent = $wsdlConfig->generate();

        if (Mage::app()->getCacheInstance()->canUse(self::WEBSERVICE_CACHE_NAME)) {
            Mage::app()->getCacheInstance()->save($wsdlContent, $cacheId, array(self::WEBSERVICE_CACHE_TAG));
        }

        return $wsdlContent;
    }

    /**
     * Retrieve SOAP server. Instantiate it during the first execution
     *
     * @return Zend_Soap_Server
     * @throws SoapFault
     */
    protected function _getSoapServer()
    {
        if (is_null($this->_soapServer)) {
            $this->_initWsdlCache();
            $soapSchemaImportTriesCount = 0;
            do {
                $soapSchemaImportFailed = false;
                try {
                    $this->_soapServer = new Zend_Soap_Server($this->_getWsdlUrl(),
                        array('encoding' => $this->_getApiCharset()));
                } catch (SoapFault $e) {
                    if (false !== strpos($e->getMessage(),
                        "Can't import schema from 'http://schemas.xmlsoap.org/soap/encoding/'")
                    ) {
                        $soapSchemaImportFailed = true;
                        $soapSchemaImportTriesCount++;
                        sleep(1);
                    } else {
                        throw $e;
                    }
                }
            } while ($soapSchemaImportFailed && $soapSchemaImportTriesCount < 5);
            use_soap_error_handler(false);
            // Front controller plays the role of SOAP handler
            $this->_soapServer->setReturnResponse(true)->setObject($this);
        }
        return $this->_soapServer;
    }

    /**
     * Set content type to response object
     *
     * @param string $contentType
     * @return Mage_Webapi_Controller_Front_Soap
     */
    protected function _setResponseContentType($contentType = 'text/xml')
    {
        $this->getResponse()->clearHeaders()
            ->setHeader('Content-Type', "$contentType; charset={$this->_getApiCharset()}");
        return $this;
    }

    /**
     * Set body to response object
     *
     * @param string $responseBody
     * @return Mage_Webapi_Controller_Front_Soap
     */
    protected function _setResponseBody($responseBody)
    {
        $this->getResponse()->setBody(preg_replace(
                '/<\?xml version="([^\"]+)"([^\>]+)>/i',
                '<?xml version="$1" encoding="' . $this->_getApiCharset() . '"?>',
                $responseBody
            )
        );
        return $this;
    }

    /**
     * Retrieve charset used in API
     *
     * @return string
     */
    protected function _getApiCharset()
    {
        // TODO: What do we need this charset for?
        return Mage::getStoreConfig('api/config/charset');
    }

    /**
     * Enable or disable SOAP extension WSDL cache depending on Magento configuration
     */
    protected function _initWsdlCache()
    {
        $wsdlCacheEnabled = (bool)Mage::getStoreConfig('api/config/wsdl_cache_enabled');
        if ($wsdlCacheEnabled) {
            ini_set('soap.wsdl_cache_enabled', '1');
        } else {
            ini_set('soap.wsdl_cache_enabled', '0');
        }
    }

    /**
     * Get WSDL file URL
     *
     * @return string
     */
    protected function _getWsdlUrl()
    {
        return $this->_getEndpointUrl(true);
    }

    /**
     * Get SOAP endpoint URL
     *
     * @param bool $isWsdl
     * @return string
     */
    protected function _getEndpointUrl($isWsdl = false)
    {
        $params = array(
            'modules' => $this->getRequest()->getRequestedModules()
        );
        if ($isWsdl) {
            $params['wsdl'] = true;
        }
        $query = http_build_query($params, '', '&');
        // @TODO: Implement proper endpoint URL retrieval mechanism in APIA-718 story
        return Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB) . 'api/soap?' . $query;
    }

    /**
     * Generate SOAP fault
     *
     * @param string $reason Human-readable explanation of the fault
     * @param string $code SOAP fault code
     * @param Exception $e Exception can be used to add information to Detail node of SOAP message
     * @throws SoapFault
     */
    protected function _soapFault($reason = self::FAULT_REASON_INTERNAL, $code = self::FAULT_CODE_RECEIVER,
        Exception $e = null
    ) {
        header('Content-type: application/soap+xml; charset=UTF-8');
        if ($this->_isSoapExtensionLoaded()) {
            $details = null;
            if (!is_null($e)) {
                $details = array('ExceptionCode' => $e->getCode());
                // add detailed message only if it differs from fault reason
                if ($e->getMessage() != $reason) {
                    $details['ExceptionMessage'] = $e->getMessage();
                }
                if (Mage::getIsDeveloperMode()) {
                    $details['ExceptionTrace'] = "<![CDATA[{$e->getTraceAsString()}]]>";
                }
            }
            // TODO: Implement Current language definition
            $language = 'en';
            die($this->_getSoapFaultMessage($reason, $code, $language, $details));
        } else {
            die($this->_getSoapFaultMessage(self::FAULT_CODE_RECEIVER, 'SOAP extension is not loaded.'));
        }
    }

    /**
     * Generate SOAP fault message in xml format
     *
     * @param string $reason Human-readable explanation of the fault
     * @param string $code SOAP fault code
     * @param string $language Reason message language
     * @param string|array|null $details Detailed reason message(s)
     * @return string
     */
    protected function _getSoapFaultMessage($reason = self::FAULT_REASON_INTERNAL, $code = self::FAULT_CODE_RECEIVER,
        $language = 'en', $details = null
    ) {
        if (is_string($details)) {
            $detailsXml = "<env:Detail>" . htmlspecialchars($details) . "</env:Detail>";
        } elseif (is_array($details)) {
            $detailsXml = "<env:Detail>" . $this->_convertDetailsToXml($details) . "</env:Detail>";
        } else {
            $detailsXml = '';
        }
        $reason = htmlentities($reason);
        $message = <<<FAULT_MESSAGE
<?xml version="1.0" encoding="utf-8" ?>
<env:Envelope xmlns:env="http://www.w3.org/2003/05/soap-envelope">
   <env:Body>
      <env:Fault>
         <env:Code>
            <env:Value>$code</env:Value>
         </env:Code>
         <env:Reason>
            <env:Text xml:lang="$language">$reason</env:Text>
         </env:Reason>
         $detailsXml
      </env:Fault>
   </env:Body>
</env:Envelope>
FAULT_MESSAGE;
        return $message;
    }

    /**
     * Recursively convert details array into xml structure.
     *
     * @param array $details
     * @return string
     */
    protected function _convertDetailsToXml($details)
    {
        $detailsXml = '';
        foreach ($details as $detailNode => $detailValue) {
            $detailNode = htmlspecialchars($detailNode);
            if (is_numeric($detailNode)) {
                continue;
            }
            if (is_string($detailValue)) {
                $detailsXml .= "<$detailNode>" . htmlspecialchars($detailValue) . "</$detailNode>";
            } elseif (is_array($detailValue)) {
                $detailsXml .= "<$detailNode>" . $this->_convertDetailsToXml($detailValue) . "</$detailNode>";
            }
        }
        return $detailsXml;
    }

    /**
     * Check whether SOAP extension is loaded or not
     *
     * @return boolean
     */
    protected function _isSoapExtensionLoaded()
    {
        return class_exists('SoapServer', false);
    }

    /**
     * Set SOAP config
     *
     * @param Mage_Webapi_Model_Config_Soap $config
     * @return Mage_Webapi_Model_Config_Soap
     */
    public function setSoapConfig(Mage_Webapi_Model_Config_Soap $config)
    {
        $this->_soapConfig = $config;
        return $this;
    }

    /**
     * Retrieve SOAP specific config
     *
     * @return Mage_Webapi_Model_Config_Soap
     */
    public function getSoapConfig()
    {
        return $this->_soapConfig;
    }
}
