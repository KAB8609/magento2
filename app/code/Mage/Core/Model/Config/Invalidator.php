<?php
/**
 * Configuration objects invalidator. Invalidates all required configuration objects for total config reinitialisation
 *
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Core_Model_Config_Invalidator implements Mage_Core_Model_Config_InvalidatorInterface
{
    /**
     * Primary configuration
     *
     * @var Mage_Core_Model_ConfigInterface
     */
    protected $_primaryConfig;

    /**
     * Modules configuration
     *
     * @var Mage_Core_Model_ConfigInterface
     */
    protected $_modulesConfig;

    /**
     * Locales configuration
     *
     * @var Mage_Core_Model_ConfigInterface
     */
    protected $_localesConfig;

    /**
     * @param Mage_Core_Model_ConfigInterface $primaryConfig
     * @param Mage_Core_Model_ConfigInterface $modulesConfig
     * @param Mage_Core_Model_ConfigInterface $localesConfig
     */
    public function __construct(
        Mage_Core_Model_ConfigInterface $primaryConfig,
        Mage_Core_Model_ConfigInterface $modulesConfig,
        Mage_Core_Model_ConfigInterface $localesConfig
    ) {
        $this->_primaryConfig = $primaryConfig;
        $this->_modulesConfig = $modulesConfig;
        $this->_localesConfig = $localesConfig;
    }

    /**
     * Invalidate config objects
     */
    public function invalidate()
    {
        $this->_primaryConfig->reinit();
        $this->_modulesConfig->reinit();
        $this->_localesConfig->reinit();
    }
}
