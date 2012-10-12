<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Adminhtml
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @group module:Mage_Adminhtml
 */
class Mage_Adminhtml_Model_Config_DataTest extends PHPUnit_Framework_TestCase
{
    /**
     * @param array $groups
     * @magentoDbIsolation enabled
     * @dataProvider saveDataProvider
     * @magentoConfigFixture current_store general/single_store_mode/enabled 1
     */
    public function testSaveWithSingleStoreModeEnabled($groups)
    {
        /** @var $_configDataObject Mage_Adminhtml_Model_Config_Data */
        $_configDataObject = Mage::getModel('Mage_Adminhtml_Model_Config_Data');
        $_configData = $_configDataObject->setSection('dev')
            ->setWebsite('base')
            ->load();
        $this->assertEmpty($_configData);

        /** @var $_configDataObject Mage_Adminhtml_Model_Config_Data */
        $_configDataObject = Mage::getModel('Mage_Adminhtml_Model_Config_Data');
        $_configDataObject->setSection('dev')
            ->setGroups($groups)
            ->save();

        Mage::getConfig()->reinit();
        Mage::app()->reinitStores();

        /** @var $_configDataObject Mage_Adminhtml_Model_Config_Data */
        $_configDataObject = Mage::getModel('Mage_Adminhtml_Model_Config_Data');
        $_configDataObject->setSection('dev')
            ->setWebsite('base');

        $_configData = $_configDataObject->load();
        $this->assertArrayHasKey('dev/debug/template_hints', $_configData);
        $this->assertArrayHasKey('dev/debug/template_hints_blocks', $_configData);

        /** @var $_configDataObject Mage_Adminhtml_Model_Config_Data */
        $_configDataObject = Mage::getModel('Mage_Adminhtml_Model_Config_Data');
        $_configDataObject->setSection('dev');
        $_configData = $_configDataObject->load();
        $this->assertArrayNotHasKey('dev/debug/template_hints', $_configData);
        $this->assertArrayNotHasKey('dev/debug/template_hints_blocks', $_configData);
    }

    public function saveDataProvider()
    {
        return require(__DIR__ . DIRECTORY_SEPARATOR . '_files' . DIRECTORY_SEPARATOR . 'config_groups.php');
    }
}
