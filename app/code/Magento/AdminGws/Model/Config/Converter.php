<?php
/**
 *  Converter of AdminGws configuration from \DOMDocument to tree array
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\AdminGws\Model\Config;

class Converter implements \Magento\Config\ConverterInterface
{
    /**
     * Convert config
     *
     * @param \DOMDocument $source
     * @return array
     */
    public function convert($source)
    {
        /** @var \DOMNodeList $groups */
        $groups = $source->getElementsByTagName('group');
        /** @var DOMNode $groupConfig */
        $callbacks = array();
        foreach ($groups as $groupConfig) {
            $groupName = $groupConfig->attributes->getNamedItem('name')->nodeValue;
            /** @var $callback DOMNode */
            foreach ($groupConfig->childNodes as $callback) {
                if ($callback->nodeType === XML_ELEMENT_NODE) {
                    $className = $callback->attributes->getNamedItem('class')->nodeValue;
                    $callbacks[$groupName][$className] = $callback->attributes->getNamedItem('method')->nodeValue;
                }
            }
        }

        /** @var \DOMNodeList $aclLevel */
        $aclLevel = $source->getElementsByTagName('level');
        /** @var DOMNode $groupConfig */
        $rules = array();
        foreach ($aclLevel as $levelConfig) {
            $levelName = $levelConfig->attributes->getNamedItem('name')->nodeValue;
            /** @var $rule DOMNode */
            foreach ($levelConfig->childNodes as $rule) {
                if ($rule->nodeType === XML_ELEMENT_NODE) {
                    $ruleName = $rule->attributes->getNamedItem('name')->nodeValue;
                    $rules[$levelName][$ruleName] = $rule->attributes->getNamedItem('resource')->nodeValue;
                }
            }
        }
        return array('callbacks' => $callbacks, 'acl' => $rules);
    }
}