<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Usa_Block_Adminhtml_Dhl_UnitofmeasureTest extends PHPUnit_Framework_TestCase
{
    /**
     * @magentoAppIsolation enabled
     */
    public function testToHtml()
    {
        /** @var $layout Mage_Core_Model_Layout */
        $layout = Mage::getSingleton('Mage_Core_Model_Layout', array('area' => 'adminhtml'));
        $this->assertEquals('adminhtml', $layout->getArea());
        $this->assertEquals('adminhtml', Mage::app()->getLayout()->getArea());

        /** @var $block Mage_Usa_Block_Adminhtml_Dhl_Unitofmeasure */
        $block = $layout->createBlock('Mage_Usa_Block_Adminhtml_Dhl_Unitofmeasure');
        $this->assertNotEmpty($block->toHtml());
    }
}
