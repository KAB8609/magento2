<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Block\Adminhtml\Order\View;

class GiftmessageTest extends \PHPUnit_Framework_TestCase
{
    public function testGetSaveButtonHtml()
    {
        $item = new \Magento\Object;
        $expectedHtml = 'some_value';

        /** @var $block \Magento\Sales\Block\Adminhtml\Order\View\Giftmessage */
        $block = $this->getMock('Magento\Sales\Block\Adminhtml\Order\View\Giftmessage',
            array('getChildBlock', 'getChildHtml'), array(), '', false);
        $block->setEntity(new \Magento\Object);
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