<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Sales
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Sales_Block_Order_Creditmemo_ItemsTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Core_Model_Layout
     */
    protected $_layout;

    /**
     * @var Mage_Sales_Block_Order_Creditmemo_Items
     */
    protected $_block;

    /**
     * @var Mage_Sales_Model_Order_Creditmemo
     */
    protected $_creditmemo;

    public function setUp()
    {
        $this->_layout = Mage::getModel('Mage_Core_Model_Layout');
        $this->_block = $this->_layout->createBlock('Mage_Sales_Block_Order_Creditmemo_Items', 'block');
        $this->_creditmemo = Mage::getModel('Mage_Sales_Model_Order_Creditmemo');
    }

    protected function tearDown()
    {
        $this->_layout = null;
        $this->_block = null;
        $this->_creditmemo = null;
    }

    public function testGetTotalsHtml()
    {
        $childBlock = $this->_layout->addBlock('Mage_Core_Block_Text', 'creditmemo_totals', 'block');

        $expectedHtml = '<b>Any html</b>';
        $this->assertEmpty($childBlock->getCreditmemo());
        $this->assertNotEquals($expectedHtml, $this->_block->getTotalsHtml($this->_creditmemo));

        $childBlock->setText($expectedHtml);
        $actualHtml = $this->_block->getTotalsHtml($this->_creditmemo);
        $this->assertSame($this->_creditmemo, $childBlock->getCreditmemo());
        $this->assertEquals($expectedHtml, $actualHtml);
    }

    public function testGetCommentsHtml()
    {
        $childBlock = $this->_layout->addBlock('Mage_Core_Block_Text', 'creditmemo_comments', 'block');

        $expectedHtml = '<b>Any html</b>';
        $this->assertEmpty($childBlock->getEntity());
        $this->assertEmpty($childBlock->getTitle());
        $this->assertNotEquals($expectedHtml, $this->_block->getCommentsHtml($this->_creditmemo));

        $childBlock->setText($expectedHtml);
        $actualHtml = $this->_block->getCommentsHtml($this->_creditmemo);
        $this->assertSame($this->_creditmemo, $childBlock->getEntity());
        $this->assertNotEmpty($childBlock->getTitle());
        $this->assertEquals($expectedHtml, $actualHtml);
    }
}
