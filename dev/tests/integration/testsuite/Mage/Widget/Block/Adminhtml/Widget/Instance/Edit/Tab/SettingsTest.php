<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Widget
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Widget_Block_Adminhtml_Widget_Instance_Edit_Tab_SettingsTest extends PHPUnit_Framework_TestCase
{
    public function testGetPackageThemeOptionsArray()
    {
        $this->markTestIncomplete('Need to fix DI dependencies');

        $block = new Mage_Widget_Block_Adminhtml_Widget_Instance_Edit_Tab_Settings;
        $options = $block->getPackageThemeOptionsArray();
        $this->assertArrayHasKey(0, $options); // -- please select --
        $this->assertArrayHasKey(1, $options); // at least one design package
    }
}
