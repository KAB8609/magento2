<?php
/**
 * Front controller for WebAPI SOAP area.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Webapi_Controller_Soap implements Magento_Core_Controller_FrontInterface
{
    /** @var Magento_Webapi_Model_Soap_Server */
    protected $_soapServer;

    /** @var Magento_Webapi_Model_Soap_Wsdl_Generator */
    protected $_wsdlGenerator;

    /** @var Magento_Webapi_Controller_Soap_Request */
    protected $_request;

    /** @var Magento_Webapi_Controller_Response */
    protected $_response;

    /** @var Magento_Webapi_Controller_ErrorProcessor */
    protected $_errorProcessor;

    /** @var Magento_Core_Model_App_State */
    protected $_appState;

    /** @var Magento_Core_Model_App */
    protected $_application;

    /**
     * Initialize dependencies.
     *
     * @param Magento_Webapi_Controller_Soap_Request $request
     * @param Magento_Webapi_Controller_Response $response
     * @param Magento_Webapi_Model_Soap_Wsdl_Generator $wsdlGenerator
     * @param Magento_Webapi_Model_Soap_Server $soapServer
     * @param Magento_Webapi_Controller_ErrorProcessor $errorProcessor
     * @param Magento_Core_Model_App_State $appState
     * @param Magento_Core_Model_App $application
     */
    public function __construct(
        Magento_Webapi_Controller_Soap_Request $request,
        Magento_Webapi_Controller_Response $response,
        Magento_Webapi_Model_Soap_Wsdl_Generator $wsdlGenerator,
        Magento_Webapi_Model_Soap_Server $soapServer,
        Magento_Webapi_Controller_ErrorProcessor $errorProcessor,
        Magento_Core_Model_App_State $appState,
        Magento_Core_Model_App $application
    ) {
        $this->_request = $request;
        $this->_response = $response;
        $this->_wsdlGenerator = $wsdlGenerator;
        $this->_soapServer = $soapServer;
        $this->_errorProcessor = $errorProcessor;
        $this->_appState = $appState;
        $this->_application = $application;
    }

    /**
     * Initialize front controller
     *
     * @return Magento_Webapi_Controller_Soap
     */
    public function init()
    {
        return $this;
    }

    /**
     * Dispatch request to SOAP endpoint.
     *
     * @return Magento_Webapi_Controller_Soap
     */
    public function dispatch()
    {
        try {
            if (!$this->_appState->isInstalled()) {
                throw new Magento_Webapi_Exception(
                    __('Magento is not yet installed'),
                    Magento_Webapi_Exception::HTTP_BAD_REQUEST
                );
            }
            if ($this->_isWsdlRequest()) {
                $responseBody = $this->_wsdlGenerator->generate(
                    $this->_request->getRequestedServices(),
                    $this->_soapServer->generateUri()
                );
                $this->_setResponseContentType('text/xml');
            } else {
                $responseBody = $this->_initSoapServer()->handle();
                $this->_setResponseContentType('application/soap+xml');
            }
            $this->_setResponseBody($responseBody);
        } catch (Exception $e) {
            $this->_prepareErrorResponse($e);
        }
        $this->_response->sendResponse();
        return $this;
    }

    /**
     * Check if current request is WSDL request. SOAP operation execution request is another type of requests.
     *
     * @return bool
     */
    protected function _isWsdlRequest()
    {
        return $this->_request->getParam(Magento_Webapi_Model_Soap_Server::REQUEST_PARAM_WSDL) !== null;
    }

    /**
     * Set body and status code to response using information extracted from provided exception.
     *
     * @param Exception $exception
     */
    protected function _prepareErrorResponse($exception)
    {
        $maskedException = $this->_errorProcessor->maskException($exception);
        $this->_setResponseContentType('text/xml');
        $soapFault = new Magento_Webapi_Model_Soap_Fault($this->_application, $maskedException);
        $httpCode = $this->_isWsdlRequest()
            ? $maskedException->getHttpCode()
            : Magento_Webapi_Controller_Rest_Response::HTTP_OK;
        $this->_response->setHttpResponseCode($httpCode);
        // TODO: Generate list of available URLs when invalid WSDL URL specified
        $this->_setResponseBody($soapFault->toXml());
    }

    /**
     * Set content type to response object.
     *
     * @param string $contentType
     * @return Magento_Webapi_Controller_Soap
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
     * @return Magento_Webapi_Controller_Soap
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

    /**
     * Initialize SOAP Server.
     *
     * @return Magento_Webapi_Model_Soap_Server
     */
    protected function _initSoapServer()
    {
        use_soap_error_handler(false);
        // TODO: Headers are not available at this point.
        // $this->_soapHandler->setRequestHeaders($this->_getRequestHeaders());

        return $this->_soapServer;
    }
}
