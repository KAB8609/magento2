<?php
use Zend\Soap\Wsdl\ComplexTypeStrategy\AbstractComplexTypeStrategy,
    Zend\Soap\Wsdl\ComplexTypeStrategy\ComplexTypeStrategyInterface,
    Zend\Soap\Wsdl;

/**
 * Magento-specific Complex type strategy for WSDL auto discovery.
 *
 * @copyright {}
 */
class Mage_Webapi_Model_Soap_Wsdl_ComplexTypeStrategy_ConfigBased extends AbstractComplexTypeStrategy
    implements ComplexTypeStrategyInterface
{
    /**
     *  Array item key value for element.
     */
    const ARRAY_ITEM_KEY_NAME = 'item';

    /**
     * Appinfo nodes namespace.
     */
    const APP_INF_NS = 'inf';

    /**
     * @var Mage_Webapi_Model_Config_Soap
     */
    protected $_config;

    /**
     * @var Mage_Webapi_Helper_Data
     */
    protected $_helper;

    /**
     * @var DOMDocument
     */
    protected $_dom;

    /**
     * Construct strategy with resource config.
     *
     * @param Mage_Webapi_Model_Config_Soap $config
     * @param Mage_Webapi_Helper_Data $helper
     */
    public function __construct(Mage_Webapi_Model_Config_Soap $config, Mage_Webapi_Helper_Data $helper)
    {
        $this->_config = $config;
        $this->_helper = $helper;
    }

    /**
     * Add complex type.
     *
     * @param string $type
     * @param array $parentCallInfo array of callInfo from parent complex type
     * @return string
     * @throws InvalidArgumentException
     */
    public function addComplexType($type, $parentCallInfo = array())
    {
        if (($soapType = $this->scanRegisteredTypes($type)) !== null) {
            return $soapType;
        }

        /** @var DOMDocument $dom */
        $dom = $this->getContext()->toDomDocument();
        $this->_dom = $dom;
        $soapType = Wsdl::TYPES_NS . ':' . $type;

        // Register type here to avoid recursion
        $this->getContext()->addType($type, $soapType);

        $complexType = $this->_dom->createElement(Wsdl::XSD_NS . ':complexType');
        $complexType->setAttribute('name', $type);
        $typeData = $this->_config->getTypeData($type);
        if (isset($typeData['documentation'])) {
            $this->addAnnotation($complexType, $typeData['documentation']);
        }

        if (isset($typeData['parameters']) && is_array($typeData['parameters'])) {
            $callInfo = isset($typeData['callInfo']) ? $typeData['callInfo'] : $parentCallInfo;
            $sequence = $this->_processParameters($typeData['parameters'], $callInfo);
            $complexType->appendChild($sequence);
        }

        $this->getContext()->getSchema()->appendChild($complexType);
        return $soapType;
    }

    /**
     * Process type parameters and create complex type sequence.
     *
     * @param array $parameters
     * @param array $callInfo
     * @return DOMElement
     */
    protected function _processParameters($parameters, $callInfo)
    {
        $sequence = $this->_dom->createElement(Wsdl::XSD_NS . ':sequence');
        foreach ($parameters as $parameterName => $parameterData) {
            $parameterType = $parameterData['type'];
            $element = $this->_dom->createElement(Wsdl::XSD_NS . ':element');
            $element->setAttribute('name', $parameterName);
            $isRequired = isset($parameterData['required']) && $parameterData['required'];
            $default = isset($parameterData['default']) ? $parameterData['default'] : null;
            $this->_revertRequiredCallInfo($isRequired, $callInfo);

            if ($this->_helper->isArrayType($parameterType)) {
                $this->_processArrayParameter($parameterType, $callInfo);
                $element->setAttribute(
                    'type',
                    Wsdl::TYPES_NS . ':' . $this->_helper->translateArrayTypeName($parameterType)
                );
            } else {
                $this->_processParameter($element, $isRequired, $parameterData, $parameterType, $callInfo);
            }

            $this->addAnnotation($element, $parameterData['documentation'], $default, $callInfo);
            $sequence->appendChild($element);
        }

        return $sequence;
    }

    /**
     * @param DOMElement $element
     * @param boolean $isRequired
     * @param array $parameterData
     * @param string $parameterType
     * @param array $callInfo
     */
    protected function _processParameter(DOMElement $element, $isRequired, $parameterData, $parameterType, $callInfo)
    {
        $element->setAttribute('minOccurs', $isRequired ? 1 : 0);
        $maxOccurs = (isset($parameterData['isArray']) && $parameterData['isArray']) ? 'unbounded' : 1;
        $element->setAttribute('maxOccurs', $maxOccurs);
        if ($this->_helper->isTypeSimple($parameterType)) {
            $typeNs = Wsdl::XSD_NS;
        } else {
            $typeNs = Wsdl::TYPES_NS;
            $this->addComplexType($parameterType, $callInfo);
        }
        $element->setAttribute('type', $typeNs . ':' . $parameterType);
    }

    /**
     * Process array of types.
     *
     * @param string $type
     * @param array $callInfo
     */
    protected function _processArrayParameter($type, $callInfo = array())
    {
        $arrayItemType = $this->_helper->getArrayItemType($type);
        $arrayTypeName = $this->_helper->translateArrayTypeName($type);
        if (!$this->_helper->isTypeSimple($arrayItemType)) {
            $this->addComplexType($arrayItemType, $callInfo);
        }
        $arrayTypeParameters = array(
            self::ARRAY_ITEM_KEY_NAME => array(
                'type' => $arrayItemType,
                'required' => false,
                'isArray' => true,
                'documentation' => sprintf('An item of %s.', $arrayTypeName)
            )
        );
        $arrayTypeData = array(
            'documentation' => sprintf('An array of %s items.', $arrayItemType),
            'parameters' => $arrayTypeParameters,
        );
        $this->_config->setTypeData($arrayTypeName, $arrayTypeData);
        $this->addComplexType($arrayTypeName, $callInfo);
    }

    /**
     * Revert required call info data if needed.
     *
     * @param boolean $isRequired
     * @param array $callInfo
     */
    protected function _revertRequiredCallInfo($isRequired, &$callInfo)
    {
        if (!$isRequired) {
            if (isset($callInfo['requiredInput']['yes'])) {
                $callInfo['requiredInput']['no']['calls'] = $callInfo['requiredInput']['yes']['calls'];
                unset($callInfo['requiredInput']['yes']);
            }
            if (isset($callInfo['returned']['always'])) {
                $callInfo['returned']['conditionally']['calls'] = $callInfo['returned']['always']['calls'];
                unset($callInfo['returned']['always']);
            }
        }
    }

    /**

    /**
     * Generate annotation data for WSDL.
     * Convert all {key:value} from documentation into appinfo nodes.
     * Override default callInfo values if defined in parameter documentation.
     *
     * @param DOMElement $element
     * @param string $documentation parameter documentation string
     * @param string|null $default
     * @param array $callInfo
     */
    public function addAnnotation(DOMElement $element, $documentation, $default = null, $callInfo = array())
    {
        $annotationNode = $this->_dom->createElement(Wsdl::XSD_NS . ':annotation');

        $elementType = $this->_getElementType($element);
        $appInfoNode = $this->_dom->createElement(Wsdl::XSD_NS . ':appinfo');
        $appInfoNode->setAttributeNS(
            Wsdl::XML_NS_URI,
            Wsdl::XML_NS . ':' . self::APP_INF_NS,
            $this->getContext()->getTargetNamespace()
        );

        $this->_processDefaultValueAnnotation($elementType, $default, $appInfoNode);
        $this->_processElementType($elementType, $documentation, $appInfoNode);

        if (preg_match_all('/{([a-z]+):(.+)}/Ui', $documentation, $matches)) {
            for ($i = 0; $i < count($matches[0]); $i++) {
                $appinfoTag = $matches[0][$i];
                $tagName = $matches[1][$i];
                $tagValue = $matches[2][$i];
                switch ($tagName) {
                    case 'callInfo':
                        $callInfoRegExp = '/([a-z].+):(returned|requiredInput):(yes|no|always|conditionally)/i';
                        if (preg_match($callInfoRegExp, $tagValue)) {
                            list($callName, $direction, $condition) = explode(':', $tagValue);
                            $condition = strtolower($condition);
                            if (preg_match('/allCallsExcept\(([a-zA-Z].+)\)/', $callName, $calls)) {
                                $callInfo[$direction][$condition] = array(
                                    'allCallsExcept' => $calls[1],
                                );
                            } else if (!isset($callInfo[$direction][$condition]['allCallsExcept'])) {
                                $this->_overrideCallInfoName($callInfo, $callName);
                                $callInfo[$direction][$condition]['calls'][] = $callName;
                            }
                        }
                        break;
                    case 'seeLink':
                        $this->_processSeeLink($appInfoNode, $tagValue);
                        break;
                    case 'docInstructions':
                        $this->_processDocInstructions($appInfoNode, $tagValue);
                        break;
                    default:
                        $nodeValue = trim($tagValue);
                        $simpleTextNode = $this->_dom->createElement(self::APP_INF_NS . ':' . $tagName);
                        $simpleTextNode->appendChild($this->_dom->createTextNode($nodeValue));
                        $appInfoNode->appendChild($simpleTextNode);
                        break;
                }
                $documentation = str_replace($appinfoTag, '', $documentation);
            }
        }
        $this->_processCallInfo($appInfoNode, $callInfo);
        $documentationNode = $this->_dom->createElement(Wsdl::XSD_NS . ':documentation');
        $documentationText = trim($documentation);
        $documentationNode->appendChild($this->_dom->createTextNode($documentationText));
        $annotationNode->appendChild($documentationNode);
        $annotationNode->appendChild($appInfoNode);
        $element->appendChild($annotationNode);
    }

    /**
     * Process different element types.
     *
     * @param string $elementType
     * @param string $documentation
     * @param DOMElement $appInfoNode
     */
    protected function _processElementType($elementType, $documentation, DOMElement $appInfoNode)
    {
        if ($elementType == 'int') {
            $this->_processRequiredAnnotation('min', $documentation, $appInfoNode);
            $this->_processRequiredAnnotation('max', $documentation, $appInfoNode);
        }
        if ($elementType == 'string') {
            $this->_processRequiredAnnotation('maxLength', $documentation, $appInfoNode);
        }

        if ($this->_helper->isArrayType($elementType)) {
            $natureOfTypeNode = $this->_dom->createElement(self::APP_INF_NS . ':natureOfType');
            $natureOfTypeNode->appendChild($this->_dom->createTextNode('array'));
            $appInfoNode->appendChild($natureOfTypeNode);
        }
    }

    /**
     * Process default value annotation.
     *
     * @param string $elementType
     * @param string $default
     * @param DOMElement $appInfoNode
     */
    protected function _processDefaultValueAnnotation($elementType, $default, DOMElement $appInfoNode)
    {
        if ($elementType == 'boolean') {
            $default = (bool)$default ? 'true' : 'false';
        }
        if ($default) {
            $defaultNode = $this->_dom->createElement(self::APP_INF_NS . ':default');
            $defaultNode->appendChild($this->_dom->createTextNode($default));
            $appInfoNode->appendChild($defaultNode);
        }
    }

    /**
     * Retrieve element type.
     *
     * @param DOMElement $element
     * @return string|null
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    protected function _getElementType(DOMElement $element)
    {
        $elementType = null;
        if ($element->hasAttribute('type')) {
            list($typeNs, $elementType) = explode(':', $element->getAttribute('type'));
        }
        return $elementType;
    }

    /**
     * Check if there is given annotation in documentation, and if not - create an empty one.
     *
     * @param $annotation
     * @param $documentation
     * @param DOMElement $appInfoNode
     */
    protected function _processRequiredAnnotation($annotation, $documentation, DOMElement $appInfoNode)
    {
        if (!preg_match("/{{$annotation}:.+}/Ui", $documentation)) {
            $annotationNode = $this->_dom->createElement(self::APP_INF_NS . ':' . $annotation);
            $appInfoNode->appendChild($annotationNode);
        }
    }

    /**
     * Process 'callInfo' appinfo tag.
     *
     * @param DOMElement $appInfoNode
     * @param $callInfo
     */
    protected function _processCallInfo(DOMElement $appInfoNode, $callInfo)
    {
        if (!empty($callInfo)) {
            foreach ($callInfo as $direction => $conditions) {
                foreach ($conditions as $condition => $info) {
                    $callInfoNode = $this->_dom->createElement(self::APP_INF_NS . ':callInfo');
                    if (isset($info['allCallsExcept'])) {
                        $allExceptNode = $this->_dom->createElement(self::APP_INF_NS . ':allCallsExcept');
                        $allExceptNode->appendChild($this->_dom->createTextNode($info['allCallsExcept']));
                        $callInfoNode->appendChild($allExceptNode);
                    } else if (isset($info['calls'])) {
                        foreach ($info['calls'] as $callName) {
                            $callNode = $this->_dom->createElement(self::APP_INF_NS . ':callName');
                            $callNode->appendChild($this->_dom->createTextNode($callName));
                            $callInfoNode->appendChild($callNode);
                        }
                    }
                    $directionNode = $this->_dom->createElement(self::APP_INF_NS . ':' . $direction);
                    $directionNode->appendChild($this->_dom->createTextNode(ucfirst($condition)));
                    $callInfoNode->appendChild($directionNode);
                    $appInfoNode->appendChild($callInfoNode);
                }
            }
        }
    }

    /**
     * Process 'docInstructions' appinfo tag.
     *
     * @param DOMElement $appInfoNode
     * @param $tagValue
     */
    protected function _processDocInstructions(DOMElement $appInfoNode, $tagValue)
    {
        if (preg_match('/(input|output):(.+)/', $tagValue, $docMatches)) {
            $docInstructionsNode = $this->_dom->createElement(self::APP_INF_NS . ':docInstructions');
            $directionNode = $this->_dom->createElement(self::APP_INF_NS . ':' . $docMatches[1]);
            $directionValueNode = $this->_dom->createElement(self::APP_INF_NS . ':' . $docMatches[2]);
            $directionNode->appendChild($directionValueNode);
            $docInstructionsNode->appendChild($directionNode);
            $appInfoNode->appendChild($docInstructionsNode);
        }
    }

    /**
     * Process 'seeLink' appinfo tag.
     *
     * @param DOMElement $appInfoNode
     * @param $tagValue
     */
    protected function _processSeeLink(DOMElement $appInfoNode, $tagValue)
    {
        if (preg_match('|([http://]?.+):(.+):(.+)|i', $tagValue, $matches)) {
            $seeLink = array(
                'url' => $matches[1],
                'title' => $matches[2],
                'for' => $matches[3],
            );
            $seeLinkNode = $this->_dom->createElement(self::APP_INF_NS . ':seeLink');
            foreach (array('url', 'title', 'for') as $subNodeName) {
                if (isset($seeLink[$subNodeName])) {
                    $seeLinkSubNode = $this->_dom->createElement(self::APP_INF_NS . ':' . $subNodeName);
                    $seeLinkSubNode->appendChild($this->_dom->createTextNode($seeLink[$subNodeName]));
                    $seeLinkNode->appendChild($seeLinkSubNode);
                }
            }
            $appInfoNode->appendChild($seeLinkNode);
        }
    }

    /**
     * Delete callName if it's already defined in some direction group.
     *
     * @param $callInfo
     * @param $callName
     */
    protected function _overrideCallInfoName(&$callInfo, $callName)
    {
        foreach ($callInfo as $direction => &$callInfoData) {
            foreach ($callInfoData as $condition => &$data) {
                if (isset($data['calls'])) {
                    $foundCallNameIndex = array_search($callName, $data['calls']);
                    if ($foundCallNameIndex !== false) {
                        unset($data['calls'][$foundCallNameIndex]);
                        if (empty($data['calls'])) {
                            unset($callInfo[$direction][$condition]);
                        }
                        break;
                    }
                }
            }
        }
    }
}
