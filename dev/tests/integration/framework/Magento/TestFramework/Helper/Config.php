<?php
/**
 * {license_notice}
 * 
 * @category    Magento
 * @package     Magento_Test
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Helper with routines to work with Magento config
 */
class Magento_TestFramework_Helper_Config
{
    /**
     * Returns enabled modules in the system
     *
     * @return array
     */
    public function getEnabledModules()
    {
        $result = array();
        $moduleList = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->get('Magento\Core\Model\ModuleListInterface');
        foreach ($moduleList->getModules() as $module) {
            $result[] = str_replace('_','\\',$module['name']);
        }
        return $result;
    }
}
