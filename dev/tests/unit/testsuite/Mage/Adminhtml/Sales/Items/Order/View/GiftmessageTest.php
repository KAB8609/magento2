<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Adminhtml
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Adminhtml_Block_Sales_Order_View_GiftmessageTest extends PHPUnit_Framework_TestCase
{
    public function testGetSaveButtonHtml()
    {
        $item = new Magento_Object;
        $expectedHtml = 'some_value';

        $block = $this->getMock('Mage_Adminhtml_Block_Sales_Order_View_Giftmessage',
            array('getChildBlock', 'getChildHtml'), array(), '', false);
        $block->setEntity(new Magento_Object);
        $block->expects($this->once())
            ->method('getChildBlock')
            ->with('save_button')
            ->will($this->returnValue($item));
        $block->expects($this->once())
            ->method('getChildHtml')
            ->with('save_button')
            ->will($this->returnValue($expectedHtml));

        $this->assertEquals($expectedHtml, $block->getSaveButtonHtml());
        $this->assertNotEmpty($item->getOnclick());
    }
}
