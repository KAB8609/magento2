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

class Mage_Adminhtml_Block_Widget_Form_ContainerTest extends PHPUnit_Framework_TestCase
{
    public function testSetDataObject()
    {
        $form = new Varien_Object;
        $dataObject = new Varien_Object;

        // _prepateLayout() is blocked, because it is used by block to instantly add 'form' child
        $block = $this->getMock('Mage_Adminhtml_Block_Widget_Form_Container', array('getChildBlock'), array(), '',
            false);
        $block->expects($this->once())
            ->method('getChildBlock')
            ->with('form')
            ->will($this->returnValue($form));

        $block->setDataObject($dataObject);
        $this->assertSame($dataObject, $block->getDataObject());
        $this->assertSame($dataObject, $form->getDataObject());
    }
}
