<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Core_Model_Layout_Translator
{
    /**
     * Translate layout node
     *
     * @param Magento_Simplexml_Element $node
     * @param array $args
     **/
    public function translateActionParameters(Magento_Simplexml_Element $node, &$args)
    {
        if (false === $this->_isNodeTranslatable($node)) {
            return;
        }

        foreach ($this->_getNodeNamesToTranslate($node) as $translatableArg) {
            /*
             * .(dot) character is used as a path separator in nodes hierarchy
             * e.g. info.title means that Magento needs to translate value of <title> node
             * that is a child of <info> node
             */
            // @var $argumentHierarchy array - path to translatable item in $args array
            $argumentHierarchy = explode('.', $translatableArg);
            $argumentStack = &$args;
            $canTranslate = true;
            while (is_array($argumentStack) && count($argumentStack) > 0) {
                $argumentName = array_shift($argumentHierarchy);
                if (isset($argumentStack[$argumentName])) {
                    /*
                     * Move to the next element in arguments hierarchy
                     * in order to find target translatable argument
                     */
                    $argumentStack = &$argumentStack[$argumentName];
                } else {
                    // Target argument cannot be found
                    $canTranslate = false;
                    break;
                }
            }
            if ($canTranslate && is_string($argumentStack)) {
                // $argumentStack is now a reference to target translatable argument so it can be translated
                $argumentStack = $this->_translateValue($argumentStack);
            }
        }
    }

    /**
     * Translate argument value
     *
     * @param Magento_Simplexml_Element $node
     * @return string
     */
    public function translateArgument(Magento_Simplexml_Element $node)
    {
        $value = $this->_getNodeValue($node);

        if ($this->_isSelfTranslatable($node)) {
            $value = $this->_translateValue($value);
        } elseif ($this->_isNodeTranslatable($node->getParent())) {
            if (true === in_array($node->getName(), $this->_getNodeNamesToTranslate($node->getParent()))) {
                $value = $this->_translateValue($value);
            }
        }

        return $value;
    }

    /**
     * Get node names that have to be translated
     *
     * @param $node
     * @return array
     */
    protected function _getNodeNamesToTranslate(Magento_Simplexml_Element $node)
    {
        return explode(' ', (string)$node['translate']);
    }

    /**
     * Check if node has to be translated
     *
     * @param Magento_Simplexml_Element $node
     * @return bool
     */
    protected function _isNodeTranslatable(Magento_Simplexml_Element $node)
    {
        return isset($node['translate']);
    }

    /**
     * Check if node has to translate own value
     *
     * @param Magento_Simplexml_Element $node
     * @return bool
     */
    protected function _isSelfTranslatable(Magento_Simplexml_Element $node)
    {
        return $this->_isNodeTranslatable($node) && 'true' == (string)$node['translate'];
    }

    /**
     * Get node value
     *
     * @param Magento_Simplexml_Element $node
     * @return string
     */
    protected function _getNodeValue(Magento_Simplexml_Element $node)
    {
        return trim((string)$node);
    }

    /**
     * Translate node value
     *
     * @param string $value
     * @return string
     */
    protected function _translateValue($value)
    {
        return __($value);
    }
}
