<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Usa_Block_Adminhtml_Dhl_UnitofmeasureTest extends Mage_Backend_Area_TestCase
{
    /**
     * @magentoAppIsolation enabled
     */
    public function testToHtml()
    {
        Mage::getObjectManager()->configure(array(
            'Mage_Core_Model_Layout' => array(
                'parameters' => array('area' => 'adminhtml')
            )
        ));
        /** @var $layout Mage_Core_Model_Layout */
        $layout = Mage::getSingleton('Mage_Core_Model_Layout');
        /** @var $block Mage_Usa_Block_Adminhtml_Dhl_Unitofmeasure */
        $block = $layout->createBlock('Mage_Usa_Block_Adminhtml_Dhl_Unitofmeasure');
        $this->assertNotEmpty($block->toHtml());
    }
}
