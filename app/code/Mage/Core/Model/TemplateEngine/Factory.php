<?php
/**
 * Factory class for Template Engine
 *
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Core_Model_TemplateEngine_Factory
{
    protected $_objectManager;

    /**
     * Template engine types
     */
    const ENGINE_TWIG = 'twig';
    const ENGINE_PHTML = 'phtml';

    public function __construct(Magento_ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * Gets the singleton instance of the appropriate template engine
     *
     * @param string $name
     * @return Mage_Core_Model_TemplateEngine_EngineInterface
     * @throws InvalidArgumentException if template engine doesn't exist
     */
    public function get($name)
    {
        if (self::ENGINE_TWIG == $name) {
            return $this->_objectManager->get('Mage_Core_Model_TemplateEngine_Twig');
        } else if (self::ENGINE_PHTML == $name) {
            return $this->_objectManager->get('Mage_Core_Model_TemplateEngine_Php');
        }
        // unknown type, throw exception
        throw new InvalidArgumentException('Unknown template engine type: ' . $name);
    }
}
