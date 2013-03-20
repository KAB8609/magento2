<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test for Mage_DesignEditor_Model_Observer
 */
class Mage_DesignEditor_Model_ObserverTest extends PHPUnit_Framework_TestCase
{
    /**
     * @param string $area
     * @param string $designMode
     * @param array $expectedAssets
     *
     * @magentoAppIsolation enabled
     * @dataProvider cleanJsDataProvider
     */
    public function testCleanJs($area, $designMode, $expectedAssets)
    {
        $layout = Mage::app()->getLayout();
        /** @var $headBlock Mage_Page_Block_Html_Head */
        $headBlock = $layout->createBlock('Mage_Page_Block_Html_Head', 'head');
        $headBlock->setData('vde_design_mode', $designMode);

        /** @var $assets Mage_Core_Model_Page_Asset_Collection */
        $objectManager = Mage::getObjectManager();

        $assets = array(
            array('name'   => 'test_css', 'type' => Mage_Core_Model_Design_Package::CONTENT_TYPE_CSS,
                  'params' => array()),
            array('name'   => 'test_css_vde', 'type' => Mage_Core_Model_Design_Package::CONTENT_TYPE_CSS,
                  'params' => array('flag_name' => 'vde_design_mode')),
            array('name'   => 'test_js', 'type' => Mage_Core_Model_Design_Package::CONTENT_TYPE_JS,
                  'params' => array()),
            array('name'   => 'test_js_vde', 'type' => Mage_Core_Model_Design_Package::CONTENT_TYPE_JS,
                  'params' => array('flag_name' => 'vde_design_mode')),
        );

        /** @var $groupAssets Mage_Page_Model_GroupedAssets */
        $groupAssets = $objectManager->get('Mage_Page_Model_GroupedAssets');
        foreach ($assets as $asset) {
            $groupAssets->addAsset($asset['name'],
                $objectManager->create('Mage_Core_Model_Page_Asset_ViewFile', array(
                    'file' => 'some_file',
                    'contentType' => $asset['type'])),
                $asset['params']);
        }

        /** @var $eventManager Mage_Core_Model_Event_Manager */
        $eventManager = $objectManager->get('Mage_Core_Model_Event_Manager')->addEventArea($area);
        $eventManager->dispatch('controller_action_layout_generate_blocks_after', array('layout' => $layout));

        $actualAssets = array_keys($objectManager->get('Mage_Core_Model_Page')->getAssets()->getAll());
        $this->assertEquals($expectedAssets, $actualAssets);
    }

    /**
     * @return array
     */
    public function cleanJsDataProvider()
    {
        return array(
            'vde area - design mode' => array('vde', '1', array('test_css', 'test_css_vde', 'test_js_vde')),
            'vde area - non design mode' => array('vde', '0',
                array('test_css', 'test_css_vde', 'test_js', 'test_js_vde')),
            'default area - design mode' => array('default', '1',
                array('test_css', 'test_css_vde', 'test_js', 'test_js_vde')),
            'default area - non design mode' => array('default', '0',
                array('test_css', 'test_css_vde', 'test_js', 'test_js_vde')),
        );
    }
}
