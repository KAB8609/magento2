<?php
/**
 * REST API request.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Webapi_Controller_Rest_Request extends Magento_Webapi_Controller_Request
{
    /**
     * Character set which must be used in request.
     */
    const REQUEST_CHARSET = 'utf-8';

    /** @var string */
    protected $_serviceName;

    /** @var string */
    protected $_serviceType;

    /** @var string */
    protected $_serviceVersion;

    /** @var Magento_Webapi_Controller_Rest_Request_InterpreterInterface */
    protected $_interpreter;

    /** @var array */
    protected $_bodyParams;

    /** @var Magento_Webapi_Controller_Rest_Request_Interpreter_Factory */
    protected $_interpreterFactory;

    /** @var Magento_Core_Model_App */
    protected $_application;

    /**
     * Initialize dependencies.
     *
     * @param Magento_Core_Model_App $application
     * @param Magento_Webapi_Controller_Rest_Request_Interpreter_Factory $interpreterFactory
     * @param string|null $uri
     */
    public function __construct(
        Magento_Core_Model_App $application,
        Magento_Webapi_Controller_Rest_Request_Interpreter_Factory $interpreterFactory,
        $uri = null
    ) {
        parent::__construct($application, $uri);
        $this->_interpreterFactory = $interpreterFactory;
    }

    /**
     * Get request interpreter.
     *
     * @return Magento_Webapi_Controller_Rest_Request_InterpreterInterface
     */
    protected function _getInterpreter()
    {
        if (null === $this->_interpreter) {
            $this->_interpreter = $this->_interpreterFactory->get($this->getContentType());
        }
        return $this->_interpreter;
    }

    /**
     * Retrieve accept types understandable by requester in a form of array sorted by quality in descending order.
     *
     * @return array
     */
    public function getAcceptTypes()
    {
        $qualityToTypes = array();
        $orderedTypes = array();

        foreach (preg_split('/,\s*/', $this->getHeader('Accept')) as $definition) {
            $typeWithQ = explode(';', $definition);
            $mimeType = trim(array_shift($typeWithQ));

            // check MIME type validity
            if (!preg_match('~^([0-9a-z*+\-]+)(?:/([0-9a-z*+\-\.]+))?$~i', $mimeType)) {
                continue;
            }
            $quality = '1.0'; // default value for quality

            if ($typeWithQ) {
                $qAndValue = explode('=', $typeWithQ[0]);

                if (2 == count($qAndValue)) {
                    $quality = $qAndValue[1];
                }
            }
            $qualityToTypes[$quality][$mimeType] = true;
        }
        krsort($qualityToTypes);

        foreach ($qualityToTypes as $typeList) {
            $orderedTypes += $typeList;
        }
        return array_keys($orderedTypes);
    }

    /**
     * Fetch data from HTTP Request body.
     *
     * @return array
     */
    public function getBodyParams()
    {
        if (null == $this->_bodyParams) {
            $this->_bodyParams = $this->_getInterpreter()->interpret((string)$this->getRawBody());
        }
        return $this->_bodyParams;
    }

    /**
     * Get Content-Type of request.
     *
     * @return string
     * @throws Magento_Webapi_Exception
     */
    public function getContentType()
    {
        $headerValue = $this->getHeader('Content-Type');

        if (!$headerValue) {
            throw new Magento_Webapi_Exception(__('Content-Type header is empty.'),
                Magento_Webapi_Exception::HTTP_BAD_REQUEST);
        }
        if (!preg_match('~^([a-z\d/\-+.]+)(?:; *charset=(.+))?$~Ui', $headerValue, $matches)) {
            throw new Magento_Webapi_Exception(__('Content-Type header is invalid.'),
                Magento_Webapi_Exception::HTTP_BAD_REQUEST);
        }
        // request encoding check if it is specified in header
        if (isset($matches[2]) && self::REQUEST_CHARSET != strtolower($matches[2])) {
            throw new Magento_Webapi_Exception(__('UTF-8 is the only supported charset.'),
                Magento_Webapi_Exception::HTTP_BAD_REQUEST);
        }

        return $matches[1];
    }

    /**
     * Retrieve current HTTP method.
     *
     * @return string
     * @throws Magento_Webapi_Exception
     */
    public function getHttpMethod()
    {
        if (!$this->isGet() && !$this->isPost() && !$this->isPut() && !$this->isDelete()) {
            throw new Magento_Webapi_Exception(__('Request method is invalid.'),
                Magento_Webapi_Exception::HTTP_BAD_REQUEST);
        }
        return $this->getMethod();
    }

    /**
     * Retrieve action version.
     *
     * @return int
     */
    public function getServiceVersion()
    {
        if (!$this->_serviceVersion) {
            // TODO: Default version can be identified and returned here
            return 1;
        }
        return $this->_serviceVersion;
    }

    /**
     * Set service version.
     *
     * @param string|int $serviceVersion Version number either with prefix or without it
     * @throws Magento_Webapi_Exception
     * @return Magento_Webapi_Controller_Rest_Request
     */
    public function setServiceVersion($serviceVersion)
    {
        $versionPrefix = Magento_Webapi_Model_Config::VERSION_NUMBER_PREFIX;
        if (preg_match("/^{$versionPrefix}?(\d+)$/i", $serviceVersion, $matches)) {
            $versionNumber = (int)$matches[1];
        } else {
            throw new Magento_Webapi_Exception(
                __("Service version is not specified or invalid one is specified."),
                Magento_Webapi_Exception::HTTP_BAD_REQUEST
            );
        }
        $this->_serviceVersion = $versionNumber;
        return $this;
    }

    /**
     * Fetch and return parameter data from the request.
     *
     * @return array
     */
    public function getRequestData()
    {
        $requestBody = array();

        $httpMethod = $this->getHttpMethod();
        if ($httpMethod == Magento_Webapi_Model_Rest_Config::HTTP_METHOD_POST
            || $httpMethod == Magento_Webapi_Model_Rest_Config::HTTP_METHOD_PUT
        ) {
            $requestBody = $this->getBodyParams();
        }

        return array_merge($requestBody, $this->getParams());
    }
}
