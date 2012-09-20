<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Options_Type_SelectTest extends PHPUnit_Framework_TestCase
{
    public function testToHtmlFormId()
    {
        $this->markTestIncomplete('Need to fix DI dependencies + block');

        $layout = new Mage_Core_Model_Layout();
        $block = $layout->createBlock('Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Options_Type_Select', 'select');
        $html = $block->getPriceTypeSelectHtml();
        $this->assertContains('select_{{select_id}}', $html);
        $this->assertContains('[{{select_id}}]', $html);
    }
}
