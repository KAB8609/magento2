<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Catalog
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Catalog_Block_Layer_ViewTest extends PHPUnit_Framework_TestCase
{
    public function testGetClearUrl()
    {
        $childBlock = new Varien_Object;

        $block = $this->getMock('Mage_Catalog_Block_Layer_View', array('getChildBlock'));
        $block->expects($this->atLeastOnce())
            ->method('getChildBlock')
            ->with('layer_state')
            ->will($this->returnValue($childBlock));

        $expectedUrl = 'http://example.com/clear_all/12/';
        $this->assertNotEquals($expectedUrl, $block->getClearUrl());
        $childBlock->setClearUrl($expectedUrl);
        $this->assertEquals($expectedUrl, $block->getClearUrl());
    }
}
