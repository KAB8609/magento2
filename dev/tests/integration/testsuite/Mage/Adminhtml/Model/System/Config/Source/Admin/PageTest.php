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
 * Test Mage_Backend_Model_Config_Source_Admin_Page
 */
class Mage_Backend_Model_Config_Source_Admin_PageTest extends Mage_Adminhtml_Utility_Controller
{
    public function testToOptionArray()
    {
        $this->dispatch('backend/admin/system_config/edit/section/admin');

        $dom = PHPUnit_Util_XML::load($this->getResponse()->getBody(), true);
        $select = $dom->getElementById('admin_startup_menu_item_id');

        $this->assertNotEmpty($select, 'Startup Page select missed');
        $options = $select->getElementsByTagName('option');
        $optionsCount = $options->length;

        $this->assertGreaterThan(98, $optionsCount, 'Paucity count of menu items in the list');

        $this->assertEquals('Dashboard', $options->item(0)->nodeValue, 'First element is not Dashboard');
        $this->assertContains('Configuration', $options->item($optionsCount - 1)->nodeValue);
    }
}
