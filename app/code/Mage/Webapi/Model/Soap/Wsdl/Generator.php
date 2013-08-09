<?php
use Zend\Soap\Wsdl;

/**
 * WSDL generator.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Webapi_Model_Soap_Wsdl_Generator
{
    const WSDL_NAME = 'MagentoWSDL';

    /**
     * WSDL factory instance.
     *
     * @var Mage_Webapi_Model_Soap_Wsdl_Factory
     */
    protected $_wsdlFactory;

    /**
     * @var Mage_Webapi_Helper_Config
     */
    protected $_helper;

    /**
     * TODO: Temporary variable for step-by-step refactoring according to new requirements
     *
     * @var Mage_Webapi_Model_Config
     */
    protected $_newApiConfig;

    /**
     * The list of registered complex types.
     *
     * @var string[]
     */
    protected $_registeredTypes = array();

    /**
     * Initialize dependencies.
     *
     * @param Mage_Webapi_Model_Config $newApiConfig
     * @param Mage_Webapi_Helper_Data $helper
     * @param Mage_Webapi_Model_Soap_Wsdl_Factory $wsdlFactory
     *
     * @throws InvalidArgumentException
     */
    public function __construct(
        Mage_Webapi_Model_Config $newApiConfig,
        Mage_Webapi_Helper_Data $helper,
        Mage_Webapi_Model_Soap_Wsdl_Factory $wsdlFactory

    ) {
        $this->_newApiConfig = $newApiConfig;
        $this->_wsdlFactory = $wsdlFactory;
        $this->_helper = $helper;
    }

    /**
     * Generate WSDL file based on requested services.
     *
     * @param array $requestedServices
     * @param string $endPointUrl
     * @return string
     * @throws Mage_Webapi_Exception
     */
    public function generate($requestedServices, $endPointUrl)
    {
        /** Sort requested services by names to prevent caching of the same wsdl file more than once. */
        ksort($requestedServices);
        $services = array();

        try {
            foreach ($requestedServices as $serviceName) {
                $services[$serviceName] = $this->_prepareServiceData($serviceName);
            }
        } catch (Mage_Webapi_Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new Mage_Webapi_Exception($e->getMessage(), Mage_Webapi_Exception::HTTP_BAD_REQUEST);
        }

        $wsdl = $this->_wsdlFactory->create(self::WSDL_NAME, $endPointUrl);
        $wsdl->addSchemaTypeSection();

        foreach ($services as $serviceId => $serviceData) {
            $portTypeName = $this->getPortTypeName($serviceId);
            $bindingName = $this->getBindingName($serviceId);
            $portType = $wsdl->addPortType($portTypeName);
            $binding = $wsdl->addBinding($bindingName, Wsdl::TYPES_NS . ':' . $portTypeName);
            $wsdl->addSoapBinding($binding, 'document', 'http://schemas.xmlsoap.org/soap/http', SOAP_1_2);
            $portName = $this->getPortName($serviceId);
            $serviceName = $this->getServiceName($serviceId);
            $wsdl->addService($serviceName, $portName, Wsdl::TYPES_NS . ':' . $bindingName, $endPointUrl, SOAP_1_2);

            foreach ($serviceData['methods'] as $methodName => $methodData) {
                $operationName = $this->getOperationName($serviceId, $methodName);
                $inputBinding = array('use' => 'literal');
                $inputMessageName = $this->_createOperationInput($wsdl, $operationName, $methodData);

                $outputMessageName = false;
                $outputBinding = false;
                if (isset($methodData['interface']['outputComplexTypes'])) {
                    $outputBinding = $inputBinding;
                    $outputMessageName = $this->_createOperationOutput($wsdl, $operationName, $methodData);
                }

                $wsdl->addPortOperation($portType, $operationName, $inputMessageName, $outputMessageName);
                $bindingOperation = $wsdl->addBindingOperation(
                    $binding,
                    $operationName,
                    $inputBinding,
                    $outputBinding,
                    false,
                    SOAP_1_2
                );
                $wsdl->addSoapOperation($bindingOperation, $operationName, SOAP_1_2);
                // @TODO: implement faults binding
            }
        }
        return $wsdl->toXML();
    }

    /**
     * Method to load Service specific XSD
     *
     * @param string $serviceName
     * @return DOMDocument
     */
    protected function _getServiceSchemaDOM($serviceName)
    {
        return $this->_newApiConfig->getServiceSchemaDOM($serviceName);
    }

    /**
     * Extract complex type element from dom document by type name (include referenced types as well).
     *
     * @param $typeName string Type name can be passed with schema target namespace prefix or without it.
     * @param $domDocument DOMDocument
     * @return DOMNode[]
     */
    public function getComplexTypeNodes($typeName, $domDocument)
    {
        $response = array();
        /** TODO: Use object manager to instantiate objects */
        $xpath = new DOMXPath($domDocument);
        /** @var $elemList DOMNode */
        $typeXPath = "//xsd:complexType[@name='{$this->_getUnprefixedTypeName($typeName, $domDocument)}']";
        $complexTypeNodes = $xpath->query($typeXPath);
        if ($complexTypeNodes) {
            $complexTypeNode = $complexTypeNodes->item(0);
        }
        if (isset($complexTypeNode)) {
            $this->_registeredTypes[] = $this->_getPrefixedTypeName($typeName, $domDocument);

            $referencedTypes = $xpath->query("{$typeXPath}//@type");
            foreach ($referencedTypes as $referencedType) {
                $referencedTypeName = $referencedType->value;
                $prefixedRefTypeName = $this->_getPrefixedTypeName($referencedTypeName, $domDocument);
                if ($this->isComplexType($referencedTypeName, $domDocument)
                    && !in_array($prefixedRefTypeName, $this->_registeredTypes)
                ) {
                    $response += $this->getComplexTypeNodes($referencedTypeName, $domDocument);
                    /** Add target namespace to the referenced type name */
                    $referencedType->value = Wsdl::TYPES_NS . ':' . $prefixedRefTypeName;
                }
            }
            $complexTypeNode->setAttribute(
                'name',
                $this->_getPrefixedTypeName($typeName, $domDocument)
            );
            $response[$this->_getPrefixedTypeName($typeName, $domDocument)]
                = $complexTypeNode->cloneNode(true);
        }
        return $response;
    }

    /**
     * Check if provided type is complex or simple type.
     *
     * Current implementation is based on the assumption that complex types are not prefixed with any namespace,
     * and simple types are prefixed.
     *
     * @param string $typeName
     * @return bool
     */
    public function isComplexType($typeName)
    {
        return !strpos($typeName, ':');
    }

    /**
     * Identify type name prefixed by target namespace defined in schema.
     *
     * @param string $typeName
     * @param DOMDocument $schemaDocument
     * @return string
     */
    protected function _getPrefixedTypeName($typeName, $schemaDocument)
    {
        $targetNamespace = $this->_getTargetNamespace($schemaDocument);
        return $targetNamespace . $this->_getUnprefixedTypeName($typeName, $schemaDocument);
    }

    /**
     * Identify type name without target namespace prefix.
     *
     * @param $typeName
     * @param $schemaDocument
     * @return mixed
     */
    protected function _getUnprefixedTypeName($typeName, $schemaDocument)
    {
        $targetNamespace = $this->_getTargetNamespace($schemaDocument);
        return str_replace($targetNamespace, '', $typeName);
    }

    /**
     * Identify schema target namespace.
     *
     * @param DOMDocument $payloadSchemaDom
     * @return string
     * @throws LogicException
     */
    protected function _getTargetNamespace($payloadSchemaDom)
    {
        $namespace = $payloadSchemaDom->getElementsByTagName('schema')->item(0)->getAttribute('targetNamespace');
        if (empty($namespace)) {
            // TODO: throw proper exception according to new error handling strategy
            throw new LogicException("Each service payload schema must have targetNamespace specified.");
        }
        /** Remove base url prefix and leave only the last part of the URL */
        $namespace = preg_replace('/^.+\/(.+)$/', '$1', $namespace);
        return $namespace;
    }

    /**
     * Create input message and corresponding element and complex types in WSDL.
     *
     * @param Mage_Webapi_Model_Soap_Wsdl $wsdl
     * @param string $operationName
     * @param array $methodData
     * @return string input message name
     */
    protected function _createOperationInput(Mage_Webapi_Model_Soap_Wsdl $wsdl, $operationName, $methodData)
    {
        /**
         * TODO: Make sure that complex type name is taken from a single place
         *
         * (currently we have two sources: XSD schema
         * and auto-generation mechanism: $this->getElementComplexTypeName($inputMessageName))
         */
        $inputMessageName = $this->getInputMessageName($operationName);
        $elementData = array(
            'name' => $inputMessageName,
            'type' => Wsdl::TYPES_NS . ':' . $inputMessageName
        );
        if (isset($methodData['interface']['inputComplexTypes'])) {
            foreach ($methodData['interface']['inputComplexTypes'] as $complexTypeNode) {
                $wsdl->addComplexType($complexTypeNode);
            }
        } else {
            $elementData['nillable'] = 'true';
        }
        $wsdl->addElement($elementData);
        $wsdl->addMessage(
            $inputMessageName,
            array(
                'messageParameters' => array(
                    'element' => Wsdl::TYPES_NS . ':' . $inputMessageName
                )
            )
        );
        return Wsdl::TYPES_NS . ':' . $inputMessageName;
    }

    /**
     * Create output message and corresponding element and complex types in WSDL.
     *
     * @param Mage_Webapi_Model_Soap_Wsdl $wsdl
     * @param string $operationName
     * @param array $methodData
     * @return string output message name
     */
    protected function _createOperationOutput(Mage_Webapi_Model_Soap_Wsdl $wsdl, $operationName, $methodData)
    {
        $outputMessageName = $this->getOutputMessageName($operationName);
        $wsdl->addElement(
            array(
                'name' => $outputMessageName,
                'type' => Wsdl::TYPES_NS . ':' . $outputMessageName
            )
        );
        if (isset($methodData['interface']['outputComplexTypes'])) {
            foreach ($methodData['interface']['outputComplexTypes'] as $complexTypeNode) {
                $wsdl->addComplexType($complexTypeNode);
            }
        }
        $wsdl->addMessage(
            $outputMessageName,
            array(
                'messageParameters' => array(
                    'element' => Wsdl::TYPES_NS . ':' . $outputMessageName
                )
            )
        );
        return Wsdl::TYPES_NS . ':' . $outputMessageName;
    }

    /**
     * Get name of complexType for message element.
     *
     * @param string $messageName
     * @return string
     */
    public function getElementComplexTypeName($messageName)
    {
        return ucfirst($messageName);
    }

    /**
     * Get name for service portType node.
     *
     * @param string $serviceName
     * @return string
     */
    public function getPortTypeName($serviceName)
    {
        return $serviceName . 'PortType';
    }

    /**
     * Get name for service binding node.
     *
     * @param string $serviceName
     * @return string
     */
    public function getBindingName($serviceName)
    {
        return $serviceName . 'Binding';
    }

    /**
     * Get name for service port node.
     *
     * @param string $serviceName
     * @return string
     */
    public function getPortName($serviceName)
    {
        return $serviceName . 'Port';
    }

    /**
     * Get name for service service.
     *
     * @param string $serviceName
     * @return string
     */
    public function getServiceName($serviceName)
    {
        return $serviceName . 'Service';
    }

    /**
     * Get name of operation based on service and method names.
     *
     * @param string $serviceName
     * @param string $methodName
     * @return string
     */
    public function getOperationName($serviceName, $methodName)
    {
        return $serviceName . ucfirst($methodName);
    }

    /**
     * Get input message node name for operation.
     *
     * @param string $operationName
     * @return string
     */
    public function getInputMessageName($operationName)
    {
        return $operationName . 'Request';
    }

    /**
     * Get output message node name for operation.
     *
     * @param string $operationName
     * @return string
     */
    public function getOutputMessageName($operationName)
    {
        return $operationName . 'Response';
    }

    /**
     * Prepare data about requested service for WSDL generator.
     *
     * @param string $serviceName
     * @return array
     * @throws Mage_Webapi_Exception
     * @throws LogicException
     */
    protected function _prepareServiceData($serviceName)
    {
        $requestedServices = $this->_newApiConfig->getRequestedSoapServices(array($serviceName));
        if (empty($requestedServices)) {
            throw new Mage_Webapi_Exception(
                $this->_helper->__('Service "%s" is not available.', $serviceName),
                Mage_Webapi_Exception::HTTP_NOT_FOUND
            );
        }
        /** $requestedServices is expected to contain exactly one item */
        $serviceData = reset($requestedServices);
        $serviceDataTypes = array('methods' => array());
        $serviceClass = $serviceData['class'];
        foreach ($serviceData['operations'] as $operationData) {
            $serviceMethod = $operationData['method'];
            /** @var $payloadSchemaDom DOMDocument */
            $payloadSchemaDom = $this->_getServiceSchemaDOM($serviceClass);
            $operationName = $this->getOperationName($serviceName, $serviceMethod);
            $inputParameterName = $this->getInputMessageName($operationName);
            $inputComplexTypes = $this->getComplexTypeNodes($inputParameterName, $payloadSchemaDom);
            if (empty($inputComplexTypes)) {
                if ($operationData['inputRequired']) {
                    throw new LogicException(
                        // @codingStandardsIgnoreStart
                        $this->_helper->__('The method "%s" of service "%s" must have "%s" complex type defined in its schema.',
                            $serviceMethod, $serviceName, $inputParameterName)
                        // @codingStandardsIgnoreEnd
                    );
                } else {
                    /** Generate empty input request to make WSDL compliant with WS-I basic profile */
                    $inputComplexTypes[] = $this->_generateEmptyComplexType($inputParameterName);
                }
            }
            $serviceDataTypes['methods'][$serviceMethod]['interface']['inputComplexTypes'] = $inputComplexTypes;
            $outputParameterName = $this->getOutputMessageName($operationName);
            $outputComplexTypes = $this->getComplexTypeNodes($outputParameterName, $payloadSchemaDom);
            if (!empty($outputComplexTypes)) {
                $serviceDataTypes['methods'][$serviceMethod]['interface']['outputComplexTypes'] = $outputComplexTypes;
            } else {
                throw new LogicException(
                    // @codingStandardsIgnoreStart
                    $this->_helper->__('The method "%s" of service "%s" must have "%s" complex type defined in its schema.',
                        $serviceMethod, $serviceName, $outputParameterName)
                    // @codingStandardsIgnoreEnd
                );
            }
        }
        return $serviceDataTypes;
    }

    /**
     * Generate empty complex type with the specified name.
     *
     * @param string $complexTypeName
     * @return DOMElement
     */
    protected function _generateEmptyComplexType($complexTypeName)
    {
        $domDocument = new DOMDocument("1.0");
        $complexTypeNode = $domDocument->createElement('xsd:complexType');
        $complexTypeNode->setAttribute('name', $complexTypeName);
        $xsdNamespace = 'http://www.w3.org/2001/XMLSchema';
        $complexTypeNode->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:xsd', $xsdNamespace);
        $domDocument->appendChild($complexTypeNode);
        $sequenceNode = $domDocument->createElement('xsd:sequence');
        $complexTypeNode->appendChild($sequenceNode);
        return $complexTypeNode;
    }
}
