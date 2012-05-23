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

class Mage_Adminhtml_Block_Widget_GridTest extends PHPUnit_Framework_TestCase
{
    public function testGetMassactionBlock()
    {
        $layout = new Mage_Core_Model_Layout;
        $block = $layout->createBlock('Mage_Adminhtml_Block_Widget_Grid', 'block');
        $child = $layout->addBlock('Mage_Core_Block_Template', 'massaction', 'block');
        $this->assertSame($child, $block->getMassactionBlock());
    }
}
