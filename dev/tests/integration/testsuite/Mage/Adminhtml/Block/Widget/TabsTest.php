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

class Mage_Adminhtml_Block_Widget_TabsTest extends PHPUnit_Framework_TestCase
{
    /**
     * @magentoAppIsolation enabled
     */
    public function testAddTab()
    {
        $this->markTestIncomplete('Need to fix DI dependencies + block');

        /** @var $widgetInstance Mage_Widget_Model_Widget_Instance */
        $widgetInstance = Mage::getModel('Mage_Widget_Model_Widget_Instance');
        Mage::register('current_widget_instance', $widgetInstance);

        /** @var $layout Mage_Core_Model_Layout */
        $layout = Mage::getModel('Mage_Core_Model_Layout');
        /** @var $block Mage_Adminhtml_Block_Widget_Tabs */
        $block = $layout->createBlock('Mage_Adminhtml_Block_Widget_Tabs', 'block');
        $layout->addBlock('Mage_Widget_Block_Adminhtml_Widget_Instance_Edit_Tab_Main', 'child_tab', 'block');
        $block->addTab('tab_id', 'child_tab');

        $this->assertEquals(array('tab_id'), $block->getTabsIds());
    }
}
