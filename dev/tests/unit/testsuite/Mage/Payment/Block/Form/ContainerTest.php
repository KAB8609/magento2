<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Payment
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Mage_Payment_Block_Form_ContainerAbstract
 */
class Mage_Payment_Block_Form_ContainerAbstractTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers Mage_Payment_Block_Form_ContainerAbstract::getChildBlock
     */
    public function testSetMethodFormTemplate()
    {
        $objectManagerHelper = new Magento_Test_Helper_ObjectManager($this);
        $childBlockA = $objectManagerHelper->getObject('Mage_Core_Block_Template');
        $childBlockB = $objectManagerHelper->getObject('Mage_Core_Block_Template');

        $func = function ($blockName) use ($childBlockA, $childBlockB) {
            switch ($blockName) {
                case 'payment.method.a':
                    return $childBlockA;
                case 'payment.method.b':
                    return $childBlockB;
            }
            return null;
        };
        $block = $this->getMock('Mage_Payment_Block_Form_Container', array('getChildBlock'),
            array(), '', false);
        $block->expects($this->atLeastOnce())
            ->method('getChildBlock')
            ->will($this->returnCallback($func));

        $template = 'any_template.phtml';
        $this->assertNotEquals($template, $childBlockA->getTemplate());
        $this->assertNotEquals($template, $childBlockB->getTemplate());

        $block->setMethodFormTemplate('a', $template);
        $this->assertEquals($template, $childBlockA->getTemplate()); // Template is set to the block
        $this->assertNotEquals($template, $childBlockB->getTemplate()); // Template is not propagated to other blocks
    }
}
