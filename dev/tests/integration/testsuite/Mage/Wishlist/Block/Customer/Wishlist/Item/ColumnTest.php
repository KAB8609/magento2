<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Wishlist
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Wishlist_Block_Customer_Wishlist_Item_ColumnTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Core_Model_Layout
     */
    protected $_layout = null;

    /**
     * @var Mage_Wishlist_Block_Customer_Wishlist_Item_Column
     */
    protected $_block = null;

    protected function setUp()
    {
        $this->_layout = Mage::getModel('Mage_Core_Model_Layout');
        $this->_block = $this->_layout->addBlock('Mage_Wishlist_Block_Customer_Wishlist_Item_Column', 'test');
        $this->_layout->addBlock('Mage_Core_Block_Text', 'child', 'test');
    }

    public function testToHtml()
    {
        $item = new StdClass;
        $this->_block->setItem($item);
        $this->_block->toHtml();
        $this->assertSame($item, $this->_layout->getBlock('child')->getItem());
    }

    public function testGetJs()
    {
        $expected = uniqid();
        $this->_layout->getBlock('child')->setJs($expected);
        $this->assertEquals($expected, $this->_block->getJs());
    }
}
