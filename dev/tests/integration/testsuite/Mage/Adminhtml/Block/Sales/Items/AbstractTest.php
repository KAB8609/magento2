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

class Mage_Adminhtml_Block_Sales_Items_AbstractTest extends Mage_Backend_Area_TestCase
{
    public function testGetItemExtraInfoHtml()
    {
        /** @var $layout Mage_Core_Model_Layout */
        $layout = Mage::getModel('Mage_Core_Model_Layout');
        /** @var $block Mage_Adminhtml_Block_Sales_Items_Abstract */
        $block = $layout->createBlock('Mage_Adminhtml_Block_Sales_Items_Abstract', 'block');

        $item = new Varien_Object;

        $this->assertEmpty($block->getItemExtraInfoHtml($item));

        $expectedHtml ='<html><body>some data</body></html>';
        /** @var $childBlock Mage_Core_Block_Text */
        $childBlock = $layout->addBlock('Mage_Core_Block_Text', 'other_block', 'block', 'order_item_extra_info');
        $childBlock->setText($expectedHtml);

        $this->assertEquals($expectedHtml, $block->getItemExtraInfoHtml($item));
        $this->assertSame($item, $childBlock->getItem());
    }
}
