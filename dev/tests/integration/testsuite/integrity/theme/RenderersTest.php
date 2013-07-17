<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Core
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Integrity_Theme_RenderersTest extends Magento_Test_TestCase_IntegrityAbstract
{
    /**
     * @param string $module
     * @param string $xpath
     * @dataProvider rendererDeclarationsDataProvider
     */
    public function testRendererDeclarations($module, $xpath)
    {
        $this->_getEnabledModules();
        if (!isset($this->_enabledModules[$module])) {
            $this->markTestSkipped("The module '$module' is not available.");
        }

        $blocks = array();
        foreach ($this->_getDesignThemes() as $theme) {
            /** @var Mage_Core_Model_Layout_Merge $layoutUpdate */
            $layoutUpdate = Mage::getModel('Mage_Core_Model_Layout_Merge', array('theme' => $theme));
            $blockElements = $layoutUpdate->getFileLayoutUpdatesXml()->xpath($xpath);
            if ($blockElements) {
                foreach ($blockElements as $block) {
                    $blocks[] = (string)$block;
                }
            }
        }
        $blocks = array_unique($blocks);
        $this->assertNotEmpty($blocks, "There are no block declarations found by xpath '{$xpath}' (module {$module})");

        foreach ($blocks as $block) {
            $this->assertNotEmpty(
                Mage::app()->getLayout()->createBlock($block), "Failed to instantiate block '{$block}'"
            );
        }
    }

    /**
     * @return array
     */
    public function rendererDeclarationsDataProvider()
    {
        return array(
            array('Enterprise_Customer', '//action[@method=\'addRenderer\']/renderer_block'),
            array('Enterprise_Rma', '//action[@method=\'addRenderer\']/renderer_block'),
            array('Mage_Adminhtml', '//action[@method=\'addOptionRenderer\']/block'),
            array('Mage_Bundle', '//action[@method=\'addRenderer\']/block'),
            array('Mage_Catalog', '//action[@method=\'addOptionRenderer\']/block'),
        );
    }
}
