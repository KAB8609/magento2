<?php
/**
 * Integrity test for template setters in Mage_Checkout_Block_CartTest
 *
 * {license_notice}
 *
 * @category Mage
 * @package Mage_Checkout
 * @subpackage integration_tests
 * @copyright  {copyright}
 * @license    {license_link}
 */

class Integrity_Mage_Checkout_Block_CartTest extends PHPUnit_Framework_TestCase
{
    /**
     * @param string $layoutFile
     * @dataProvider layoutFilesDataProvider
     */
    public function testCustomTemplateSetters($layoutFile)
    {
        $params = array();
        if (preg_match('/app\/design\/frontend\/(.+?)\/(.+?)\//', $layoutFile, $matches)) {
            $params = array('_package' => $matches[1], '_theme' => $matches[2]);
        }

        $xml = simplexml_load_file($layoutFile);
        $nodes = $xml->xpath('//block/action[@method="setCartTemplate" or @method="setEmptyTemplate"]') ?: array();
        /** @var $node SimpleXMLElement */
        foreach ($nodes as $node) {
            $template = (array)$node->children();
            $template = array_shift($template);
            foreach ($node->xpath('..') as $blockNode) {
                preg_match('/^(.+?_.+?)_/', $blockNode['type'], $matches);
                $params['_module'] = $matches[1];
                break;
            }
            $this->assertFileExists(Mage::getDesign()->getTemplateFilename($template, $params));
        }
    }

    /**
     * @return array
     */
    public function layoutFilesDataProvider()
    {
        return Utility_Files::init()->getLayoutFiles(array('area' => 'frontend'));
    }
}
