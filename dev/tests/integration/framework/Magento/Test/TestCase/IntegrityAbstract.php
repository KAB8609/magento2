<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Test
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * An ancestor class for integrity tests
 */
abstract class Magento_Test_TestCase_IntegrityAbstract extends PHPUnit_Framework_TestCase
{
    /**
     * Cached index of enabled modules
     *
     * @var array
     */
    protected $_enabledModules = null;

    /**
     * Returns array of enabled modules
     *
     * @return array
     */
    protected function _getEnabledModules()
    {
        if ($this->_enabledModules === null) {
            /** @var $helper Magento_Test_Helper_Config */
            $helper = Magento_Test_Helper_Factory::getHelper('config');
            $enabledModules = $helper->getEnabledModules();
            $this->_enabledModules = array_combine($enabledModules, $enabledModules);
        }
        return $this->_enabledModules;
    }

    /**
     * Checks resource file declaration - whether it is for disabled module (e.g. 'Disabled_Module::file.ext').
     *
     * @param string $file
     * @return bool
     */
    protected function _isFileForDisabledModule($file)
    {
        $enabledModules = $this->_getEnabledModules();
        if (preg_match('/^(.*)::/', $file, $matches)) {
            $module = $matches[1];
            if (!isset($enabledModules[$module])) {
                return true;
            }
        }
        return false;
    }

    /**
     * Returns flat array of themes currently located in system
     *
     * @return Mage_Core_Model_Theme[]
     */
    protected function _getDesignThemes()
    {
        /** @var $themeCollection Mage_Core_Model_Resource_Theme_Collection */
        $themeCollection = Mage::getObjectManager()->create('Mage_Core_Model_Resource_Theme_Collection');
        return $themeCollection->getItems();
    }
}
