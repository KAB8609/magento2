<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Catalog\Model\ProductTypes\Config;

class Converter implements \Magento\Config\ConverterInterface
{
    /**
     * Convert dom node tree to array
     *
     * @param \DOMDocument $source
     * @return array
     * @throws \InvalidArgumentException
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function convert($source)
    {
        $output = array();
        $xpath = new \DOMXPath($source);
        $types = $xpath->evaluate('/config/type');
        /** @var $typeNode DOMNode */
        foreach ($types as $typeNode) {
            $typeName = $this->_getAttributeValue($typeNode, 'name');
            $isComposite = $this->_getAttributeValue($typeNode, 'composite', 'false');
            $isDecimal = $this->_getAttributeValue($typeNode, 'canUseQtyDecimals', 'true');
            $isQty = $this->_getAttributeValue($typeNode, 'isQty', 'false');
            $data = array();
            $data['name'] = $typeName;
            $data['label'] = $this->_getAttributeValue($typeNode, 'label', '');
            $data['model'] = $this->_getAttributeValue($typeNode, 'modelInstance');
            $data['composite'] = !empty($isComposite) && 'false' !== $isComposite;
            $data['index_priority'] = (int) $this->_getAttributeValue($typeNode, 'indexPriority', 0);
            $data['can_use_qty_decimals'] = !empty($isDecimal) && 'false' !== $isDecimal;
            $data['is_qty'] = !empty($isQty) && 'false' !== $isQty;

            /** @var $childNode DOMNode */
            foreach ($typeNode->childNodes as $childNode) {
                if ($childNode->nodeType != XML_ELEMENT_NODE) {
                    continue;
                }

                switch ($childNode->nodeName) {
                    case 'priceModel':
                        $data['price_model'] = $this->_getAttributeValue($childNode, 'instance');
                        break;
                    case 'indexerModel':
                        $data['price_indexer'] = $this->_getAttributeValue($childNode, 'instance');
                        break;
                    case 'stockIndexerModel':
                        $data['stock_indexer'] = $this->_getAttributeValue($childNode, 'instance');
                        break;
                    case 'allowProductTypes':
                        /** @var $allowedTypes DOMNode */
                        foreach ($childNode->childNodes as $allowedTypes) {
                            if ($allowedTypes->nodeType != XML_ELEMENT_NODE) {
                                continue;
                            }
                            $name = $this->_getAttributeValue($allowedTypes, 'name');
                            $data['allow_product_types'][$name] = $name;
                        }
                        break;
                    case 'allowedSelectionTypes':
                        /** @var $selectionsTypes DOMNode */
                        foreach ($childNode->childNodes as $selectionsTypes) {
                            if ($selectionsTypes->nodeType != XML_ELEMENT_NODE) {
                                continue;
                            }
                            $name = $this->_getAttributeValue($selectionsTypes, 'name');
                            $data['allowed_selection_types'][$name] = $name;
                        }
                        break;
                }

            }
            $output[$typeName] = $data;
        }
        return $output;
    }

    /**
     * Get attribute value
     *
     * @param DOMNode $input
     * @param string $attributeName
     * @param mixed $default
     * @return null|string
     */
    protected function _getAttributeValue(\DOMNode $input, $attributeName, $default = null)
    {
        $node = $input->attributes->getNamedItem($attributeName);
        return $node ? $node->nodeValue : $default;
    }
}