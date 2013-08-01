<?php
/**
 * Web API REST response.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Webapi_Controller_Response_Rest extends Mage_Webapi_Controller_Response
{
    /**#@+
     * Success HTTP response codes.
     */
    const HTTP_OK = 200;
    const HTTP_CREATED = 201;
    const HTTP_MULTI_STATUS = 207;
    /**#@-*/

    /** @var Mage_Webapi_Controller_Dispatcher_ErrorProcessor */
    protected $_errorProcessor;

    /** @var Mage_Webapi_Controller_Response_Rest_RendererInterface */
    protected $_renderer;

    /** @var Mage_Webapi_Helper_Data */
    protected $_helper;

    /** @var Mage_Core_Model_App */
    protected $_app;

    /**
     * Initialize dependencies.
     *
     * @param Mage_Webapi_Controller_Response_Rest_Renderer_Factory $rendererFactory
     * @param Mage_Webapi_Controller_Dispatcher_ErrorProcessor $errorProcessor
     * @param Mage_Webapi_Helper_Data $helper
     * @param Mage_Core_Model_App $app
     */
    public function __construct(
        Mage_Webapi_Controller_Response_Rest_Renderer_Factory $rendererFactory,
        Mage_Webapi_Controller_Dispatcher_ErrorProcessor $errorProcessor,
        Mage_Webapi_Helper_Data $helper,
        Mage_Core_Model_App $app
    ) {
        $this->_renderer = $rendererFactory->get();
        $this->_errorProcessor = $errorProcessor;
        $this->_helper = $helper;
        $this->_app = $app;
    }

    /**
     * Add exception to the list of exceptions.
     *
     * Replace real error message of untrusted exceptions to prevent potential vulnerability.
     *
     * @param Exception $exception
     * @return Mage_Webapi_Controller_Response_Rest
     */
    public function setException(Exception $exception)
    {
        return parent::setException($exception);
    }

    /**
     * Send response to the client, render exceptions if they are present.
     */
    public function sendResponse()
    {
        try {
            if ($this->isException()) {
                $this->_renderMessages();
            }
            parent::sendResponse();
        } catch (Exception $e) {
            // If the server does not support all MIME types accepted by the client it SHOULD send 406 (not acceptable).
            $httpCode = $e->getCode() == Mage_Webapi_Exception::HTTP_NOT_ACCEPTABLE
                ? Mage_Webapi_Exception::HTTP_NOT_ACCEPTABLE
                : Mage_Webapi_Exception::HTTP_INTERNAL_ERROR;

            /** If error was encountered during "error rendering" process then use error renderer. */
            $this->_errorProcessor->renderException($e, $httpCode);
        }
    }

    /**
     * Generate and set HTTP response code, error messages to Response object.
     */
    protected function _renderMessages()
    {
        $formattedMessages = $this->getMessages();
        $responseHttpCode = null;
        /** @var Exception $exception */
        foreach ($this->getException() as $exception) {
            if ($exception instanceof Mage_Service_ResourceNotFoundException) {
                $code = Mage_Webapi_Exception::HTTP_NOT_FOUND;
            } elseif ($exception instanceof Mage_Service_AuthorizationException) {
                $code = Mage_Webapi_Exception::HTTP_UNAUTHORIZED;
            } elseif ($exception instanceof Mage_Service_Exception) {
                $code = Mage_Webapi_Exception::HTTP_BAD_REQUEST;
            } elseif ($exception instanceof Mage_Webapi_Exception) {
                $code = $exception->getCode();
            } else {
                $code = Mage_Webapi_Exception::HTTP_INTERNAL_ERROR;
            }

            $messageData = array('code' => $exception->getCode(), 'message' => $exception->getMessage());
            if ($exception instanceof Mage_Service_Exception) {
                /** @var Mage_Service_Exception $exception */
                $messageData['parameters'] = $exception->getParameters();
            }
            if ($this->_app->isDeveloperMode()) {
                $messageData['trace'] = $exception->getTraceAsString();
            }
            $formattedMessages['errors'][] = $messageData;
            // keep HTTP code for response
            $responseHttpCode = $code;
        }
        // set HTTP code of the last error, Content-Type, and all rendered error messages to body
        $this->setHttpResponseCode($responseHttpCode);
        $this->setMimeType($this->_renderer->getMimeType());
        $this->setBody($this->_renderer->render($formattedMessages));
        return $this;
    }
}
