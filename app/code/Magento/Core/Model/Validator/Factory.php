<?php
/**
 * Magento validator config factory
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Core_Model_Validator_Factory
{
    /**
     * @var \Magento\ObjectManager
     */
    protected $_objectManager;

    /**
     * @var Magento_Core_Model_Translate
     */
    protected $_translator;

    /**
     * Validator config files
     *
     * @var array
     */
    protected $_configFiles = null;

    /**
     * Initialize dependencies
     *
     * @param \Magento\ObjectManager $objectManager
     * @param Magento_Core_Model_Config_Modules_Reader $moduleReader
     * @param Magento_Core_Model_Translate $translator
     */
    public function __construct(
        \Magento\ObjectManager $objectManager,
        Magento_Core_Model_Config_Modules_Reader $moduleReader,
        Magento_Core_Model_Translate $translator
    ) {
        $this->_objectManager = $objectManager;
        $this->_translator = $translator;

        $this->_configFiles = $moduleReader->getConfigurationFiles('validation.xml');
        $this->_initializeDefaultTranslator();
    }

    /**
     * Create and set default translator to \Magento\Validator\ValidatorAbstract.
     */
    protected function _initializeDefaultTranslator()
    {
        $translateAdapter = $this->_translator;
        $objectManager = $this->_objectManager;
        // Pass translations to Magento_Core_Model_Translate from validators
        $translatorCallback = function () use ($translateAdapter, $objectManager) {
            /** @var Magento_Core_Model_Translate $translateAdapter */
            return $translateAdapter->translate(func_get_args());
        };
        /** @var \Magento\Translate\Adapter $translator */
        $translator = $this->_objectManager->create('Magento\Translate\Adapter');
        $translator->setOptions(array('translator' => $translatorCallback));
        \Magento\Validator\ValidatorAbstract::setDefaultTranslator($translator);
    }

    /**
     * Get validator config object.
     *
     * Will instantiate \Magento\Validator\Config
     *
     * @return \Magento\Validator\Config
     */
    public function getValidatorConfig()
    {
        return $this->_objectManager->create('Magento\Validator\Config', array('configFiles' => $this->_configFiles));
    }

    /**
     * Create validator builder instance based on entity and group.
     *
     * @param string $entityName
     * @param string $groupName
     * @param array|null $builderConfig
     * @return \Magento\Validator\Builder
     */
    public function createValidatorBuilder($entityName, $groupName, array $builderConfig = null)
    {
        return $this->getValidatorConfig()->createValidatorBuilder($entityName, $groupName, $builderConfig);
    }

    /**
     * Create validator based on entity and group.
     *
     * @param string $entityName
     * @param string $groupName
     * @param array|null $builderConfig
     * @return \Magento\Validator
     */
    public function createValidator($entityName, $groupName, array $builderConfig = null)
    {
        return $this->getValidatorConfig()->createValidator($entityName, $groupName, $builderConfig);
    }
}
