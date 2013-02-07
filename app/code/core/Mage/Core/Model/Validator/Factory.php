<?php
/**
 * Magento validator config factory
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Core_Model_Validator_Factory
{
    /**
     * @var Magento_ObjectManager
     */
    protected $_objectManager;

    /**
     * @var Mage_Core_Model_Translate
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
     * @param Magento_ObjectManager $objectManager
     * @param Mage_Core_Model_Config_Modules_Reader $moduleReader
     * @param Mage_Core_Model_Translate $translator
     */
    public function __construct(
        Magento_ObjectManager $objectManager,
        Mage_Core_Model_Config_Modules_Reader $moduleReader,
        Mage_Core_Model_Translate $translator
    ) {
        $this->_objectManager = $objectManager;
        $this->_translator = $translator;

        $this->_configFiles = $moduleReader->getModuleConfigurationFiles('validation.xml');
        $this->_initializeDefaultTranslator();
    }

    /**
     * Create and set default translator to Magento_Validator_ValidatorAbstract.
     */
    protected function _initializeDefaultTranslator()
    {
        $translateAdapter = $this->_translator;
        $objectManager = $this->_objectManager;
        // Pass translations to Mage_Core_Model_Translate from validators
        $translatorCallback = function () use ($translateAdapter, $objectManager) {
            /** @var Mage_Core_Model_Translate $translateAdapter */
            $args = func_get_args();
            $expr = $objectManager->create('Mage_Core_Model_Translate_Expr');
            $expr->setText($args[0]);
            array_unshift($args, $expr);
            return $translateAdapter->translate($args);
        };
        /** @var Magento_Translate_Adapter $translator */
        $translator = $this->_objectManager->create('Magento_Translate_Adapter');
        $translator->setOptions(array('translator' => $translatorCallback));
        Magento_Validator_ValidatorAbstract::setDefaultTranslator($translator);
    }

    /**
     * Get validator config object.
     *
     * Will instantiate Magento_Validator_Config
     *
     * @return Magento_Validator_Config
     */
    public function getValidatorConfig()
    {
        return $this->_objectManager->get('Magento_Validator_Config', array('configFiles' => $this->_configFiles));
    }

    /**
     * Create validator builder instance based on entity and group.
     *
     * @param string $entityName
     * @param string $groupName
     * @param array|null $builderConfig
     * @return Magento_Validator_Builder
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
     * @return Magento_Validator
     */
    public function createValidator($entityName, $groupName, array $builderConfig = null)
    {
        return $this->getValidatorConfig()->createValidator($entityName, $groupName, $builderConfig);
    }
}
