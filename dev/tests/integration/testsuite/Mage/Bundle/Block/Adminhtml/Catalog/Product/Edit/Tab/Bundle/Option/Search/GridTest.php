<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Bundle
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Bundle_Block_Adminhtml_Catalog_Product_Edit_Tab_Bundle_Option_Search_GridTest
    extends PHPUnit_Framework_TestCase
{
    /**
     * @magentoAppIsolation enabled
     */
    public function testToHtmlHasOnClick()
    {
        $this->markTestIncomplete('Need to fix DI dependencies + block');

        Mage::getDesign()->setArea(Mage_Core_Model_App_Area::AREA_ADMINHTML);
        $layout = new Mage_Core_Model_Layout(array('area' => Mage_Core_Model_App_Area::AREA_ADMINHTML));
        $block = $layout->createBlock(
            'Mage_Bundle_Block_Adminhtml_Catalog_Product_Edit_Tab_Bundle_Option_Search_Grid',
            'block');
        $block->setId('temp_id');

        $html = $block->toHtml();

        $regexpTemplate = '/<button [^>]* onclick="temp_id[^"]*\\.%s/i';
        $jsFuncs = array('doFilter', 'resetFilter');
        foreach ($jsFuncs as $func) {
            $regexp = sprintf($regexpTemplate, $func);
            $this->assertRegExp($regexp, $html);
        }
    }
}
