<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Test\Integrity\Theme;

class RenderersTest extends \Magento\TestFramework\TestCase\AbstractIntegrity
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
            /** @var \Magento\View\Layout\Processor $layoutUpdate */
            $layoutUpdate = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\View\Layout\Processor', array('theme' => $theme));
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
                \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\View\Layout')
                    ->createBlock($block),
                "Failed to instantiate block '{$block}'"
            );
        }
    }

    /**
     * @return array
     */
    public function rendererDeclarationsDataProvider()
    {
        return array(
            array(
                'Magento_CustomerCustomAttributes',
                '//action[@method=\'addRenderer\']/argument[@name="renderer_block"]'
            ),
            array(
                'Magento_Rma',
                '//action[@method=\'addRenderer\']/argument[@name="renderer_block"]'
            ),
            array(
                'Magento_Bundle',
                '//action[@method=\'addRenderer\']/argument[@name="block"]'
            ),
        );
    }
}
