<?php
/**
 * Magento-specific SOAP server.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Webapi\Model\Soap;

class Server
{
    const SOAP_DEFAULT_ENCODING = 'UTF-8';

    /**#@+
     * Path in config to Webapi settings.
     */
    const CONFIG_PATH_WSDL_CACHE_ENABLED = 'webapi/soap/wsdl_cache_enabled';
    const CONFIG_PATH_SOAP_CHARSET = 'webapi/soap/charset';
    /**#@-*/

    const REQUEST_PARAM_SERVICES = 'services';
    const REQUEST_PARAM_WSDL = 'wsdl';

    /** @var \Magento\Core\Model\Config */
    protected $_applicationConfig;

    /** @var \Magento\DomDocument\Factory */
    protected $_domDocumentFactory;

    /** @var \Magento\Webapi\Controller\Soap\Request */
    protected $_request;

    /** @var \Magento\Core\Model\StoreManagerInterface */
    protected $_storeManager;

    /** @var \Magento\Webapi\Model\Soap\Server\FactoryInterface */
    protected $_soapServerFactory;

    /**
     * Initialize dependencies, initialize WSDL cache.
     *
     * @param \Magento\Core\Model\Config $applicationConfig
     * @param \Magento\Webapi\Controller\Soap\Request $request
     * @param \Magento\DomDocument\Factory $domDocumentFactory
     * @param \Magento\Core\Model\StoreManagerInterface
     * @param \Magento\Webapi\Model\Soap\Server\Factory
     * @throws \Magento\Webapi\Exception with invalid SOAP extension
     */
    public function __construct(
        \Magento\Core\Model\Config $applicationConfig,
        \Magento\Webapi\Controller\Soap\Request $request,
        \Magento\DomDocument\Factory $domDocumentFactory,
        \Magento\Core\Model\StoreManagerInterface $storeManager,
        \Magento\Webapi\Model\Soap\Server\Factory $soapServerFactory
    ) {
        if (!extension_loaded('soap')) {
            throw new \Magento\Webapi\Exception('SOAP extension is not loaded.', 0,
                \Magento\Webapi\Exception::HTTP_INTERNAL_ERROR);
        }
        $this->_applicationConfig = $applicationConfig;
        $this->_request = $request;
        $this->_domDocumentFactory = $domDocumentFactory;
        $this->_storeManager = $storeManager;
        $this->_soapServerFactory = $soapServerFactory;
        /** Enable or disable SOAP extension WSDL cache depending on Magento configuration. */
        $wsdlCacheEnabled = (bool)$storeManager->getStore()->getConfig(self::CONFIG_PATH_WSDL_CACHE_ENABLED);
        if ($wsdlCacheEnabled) {
            ini_set('soap.wsdl_cache_enabled', '1');
        } else {
            ini_set('soap.wsdl_cache_enabled', '0');
        }
    }

    /**
     * Handle SOAP request.
     *
     * @return string
     */
    public function handle()
    {
        $rawRequestBody = file_get_contents('php://input');
        $this->_checkRequest($rawRequestBody);
        $options = array(
            'encoding' => $this->getApiCharset(),
            'soap_version' => SOAP_1_2
        );
        $soap = $this->_soapServerFactory->create($this->generateUri(true), $options);
        return $soap->handle($rawRequestBody);
    }

    /**
     * Retrieve charset used in SOAP API.
     *
     * @return string
     */
    public function getApiCharset()
    {
        $charset = $this->_storeManager->getStore()->getConfig(self::CONFIG_PATH_SOAP_CHARSET);
        return $charset ? $charset : \Magento\Webapi\Model\Soap\Server::SOAP_DEFAULT_ENCODING;
    }

    /**
     * Get SOAP endpoint URL.
     *
     * @param bool $isWsdl
     * @return string
     */
    public function generateUri($isWsdl = false)
    {
        $params = array(
            self::REQUEST_PARAM_SERVICES => $this->_request->getParam(
                \Magento\Webapi\Model\Soap\Server::REQUEST_PARAM_SERVICES
            )
        );
        if ($isWsdl) {
            $params[self::REQUEST_PARAM_WSDL] = true;
        }
        $query = http_build_query($params, '', '&');
        return $this->getEndpointUri() . '?' . $query;
    }

    /**
     * Generate URI of SOAP endpoint.
     *
     * @return string
     */
    public function getEndpointUri()
    {
        return $this->_storeManager->getStore()->getBaseUrl() . $this->_applicationConfig->getAreaFrontName();
    }

    /**
     * Get SOAP Header names from request.
     *
     * @return array
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     * @TODO Remove this method if it is not used after SOAP authentication implementation
     */
    public function getRequestHeaders()
    {
        $dom = $this->_domDocumentFactory->createDomDocument();
        $dom->loadXML($this->_request);
        $headers = array();
        /** @var \DOMElement $header */
        foreach ($dom->getElementsByTagName('Header')->item(0)->childNodes as $header) {
            list($headerNs, $headerName) = explode(":", $header->nodeName);
            $headers[] = $headerName;
        }

        return $headers;
    }

    /**
     * Generate exception if request is invalid.
     *
     * @param string $soapRequest
     * @throws \Magento\Webapi\Exception with invalid SOAP extension
     * @return \Magento\Webapi\Model\Soap\Server
     */
    protected function _checkRequest($soapRequest)
    {
        $dom = new \DOMDocument();
        if (strlen($soapRequest) == 0 || !$dom->loadXML($soapRequest)) {
            throw new \Magento\Webapi\Exception(__('Invalid XML'), 0, \Magento\Webapi\Exception::HTTP_INTERNAL_ERROR);
        }
        foreach ($dom->childNodes as $child) {
            if ($child->nodeType === XML_DOCUMENT_TYPE_NODE) {
                throw new \Magento\Webapi\Exception(__('Invalid XML: Detected use of illegal DOCTYPE'), 0,
                    \Magento\Webapi\Exception::HTTP_INTERNAL_ERROR);
            }
        }
        return $this;
    }
}
