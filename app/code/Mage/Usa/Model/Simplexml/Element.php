<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Usa
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Extends SimpleXML to add valuable functionality to SimpleXMLElement class
 *
 * @category Mage
 * @package  Mage_Usa
 * @author   Magento Core Team <core@magentocommerce.com>
 */
class Mage_Usa_Model_Simplexml_Element extends SimpleXMLElement
{
    /**
     * Adds an attribute to the SimpleXML element
     *
     * @param string $name The name of the attribute to add.
     * @param string $value If specified, the value of the attribute.
     * @param string $namespace If specified, the namespace to which the attribute belongs.
     * @return void
     */
    public function addAttribute($name, $value = null, $namespace = null)
    {
        if (!is_null($value)) {
            $value = $this->xmlentities($value);
        }
        return parent::addAttribute($name, $value, $namespace);
    }

    /**
     * Adds a child element to the XML node
     *
     * @param string $name The name of the child element to add.
     * @param string $value If specified, the value of the child element.
     * @param string $namespace If specified, the namespace to which the child element belongs.
     * @return Mage_Usa_Model_Simplexml_Element
     */
    public function addChild($name, $value = null, $namespace = null)
    {
        if (!is_null($value)) {
            $value = $this->xmlentities($value);
        }
        return parent::addChild($name, $value, $namespace);
    }

    /**
     * Converts meaningful xml characters to xml entities
     *
     * @param string
     * @return string
     */
    public function xmlentities($value)
    {
        $value = str_replace('&amp;', '&', $value);
        $value = str_replace('&', '&amp;', $value);
        return $value;
    }
}
