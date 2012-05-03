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

class Mage_Widget_Block_Adminhtml_Widget_Instance_Edit_Tab_MainTest extends PHPUnit_Framework_TestCase
{
    public function testPackageThemeElement()
    {
        Mage::register('current_widget_instance', new Varien_Object());
        $block = Mage::app()->getLayout()->createBlock('Mage_Widget_Block_Adminhtml_Widget_Instance_Edit_Tab_Main');
        $block->toHtml();
        $element = $block->getForm()->getElement('package_theme');
        $this->assertInstanceOf('Varien_Data_Form_Element_Text', $element);
        $this->assertTrue($element->getDisabled());
    }
}
