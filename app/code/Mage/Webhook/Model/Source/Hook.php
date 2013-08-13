<?php
/**
 * The list of available hooks
 *
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Webhook
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Webhook_Model_Source_Hook
{
    /**
     * Path to environments section in the config
     */
    const XML_PATH_WEBHOOK = 'global/webhook/webhooks';

    /**
     * Cache of options
     *
     * @var null|array
     */
    protected $_options = null;

    /** @var Magento_Core_Model_Translate  */
    private $_translator;

    /** @var  Magento_Core_Model_Config */
    private $_config;

    /**
     * @param Magento_Core_Model_Translate $translator
     * @param Magento_Core_Model_Config $config
     */
    public function __construct(Magento_Core_Model_Translate $translator, Magento_Core_Model_Config $config )
    {
        $this->_translator = $translator;
        $this->_config = $config;
    }

    /**
     * Get available topics
     *
     * @return array
     */
    public function toOptionArray()
    {
        if (!$this->_options) {
            $this->_options = array();

            $configElement = $this->_config->getNode(self::XML_PATH_WEBHOOK);
            if ($configElement) {
                $this->_options = $configElement->asArray();
            }
        }

        return $this->_options;
    }

    /**
     * Scan config element to retrieve topics
     *
     * @return array
     */
    public function getTopicsForForm()
    {
        $elements = array();

        // process groups
        $elements = $this->_getTopicsForForm($this->toOptionArray(), array(), $elements);

        return $elements;
    }

    /**
     * Recursive helper function to dynamically build topic information for our form.
     * Seeks out nodes under 'webhook' stopping when it finds a leaf that contains 'label'
     * The value is constructed using the XML tree parents.
     * @param array $node
     * @param array $path
     * @param array $elements
     * @return array
     */
    protected function _getTopicsForForm($node, $path, $elements)
    {
        if (!empty($node['label'])) {
            $value = join('/', $path);

            $label = $this->_translator->translate(array($node['label']));

            $elements[] = array(
                'label' => $label,
                'value' => $value,
            );

            return $elements;
        }

        foreach ($node as $group => $child) {
            $path[] = $group;
            $elements = $this->_getTopicsForForm($child, $path, $elements);
            array_pop($path);
        }

        return $elements;
    }
}
