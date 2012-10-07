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

class Mage_Sales_Block_Order_Invoice_ItemsTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Core_Model_Layout
     */
    protected $_layout;

    /**
     * @var Mage_Sales_Block_Order_Invoice_Items
     */
    protected $_block;

    /**
     * @var Mage_Sales_Model_Order_Invoice
     */
    protected $_invoice;

    public function setUp()
    {
        $this->_layout = new Mage_Core_Model_Layout;
        $this->_block = new Mage_Sales_Block_Order_Invoice_Items;
        $this->_layout->addBlock($this->_block, 'block');
        $this->_invoice = new Mage_Sales_Model_Order_Invoice;
    }

    protected function tearDown()
    {
        $this->_layout = null;
        $this->_block = null;
        $this->_invoice = null;
    }

    public function testGetInvoiceTotalsHtml()
    {
        $childBlock = $this->_layout->addBlock('Mage_Core_Block_Text', 'invoice_totals', 'block');

        $expectedHtml = '<b>Any html</b>';
        $this->assertEmpty($childBlock->getInvoice());
        $this->assertNotEquals($expectedHtml, $this->_block->getInvoiceTotalsHtml($this->_invoice));

        $childBlock->setText($expectedHtml);
        $actualHtml = $this->_block->getInvoiceTotalsHtml($this->_invoice);
        $this->assertSame($this->_invoice, $childBlock->getInvoice());
        $this->assertEquals($expectedHtml, $actualHtml);
    }

    public function testGetInvoiceCommentsHtml()
    {
        $childBlock = $this->_layout->addBlock('Mage_Core_Block_Text', 'invoice_comments', 'block');

        $expectedHtml = '<b>Any html</b>';
        $this->assertEmpty($childBlock->getEntity());
        $this->assertEmpty($childBlock->getTitle());
        $this->assertNotEquals($expectedHtml, $this->_block->getInvoiceCommentsHtml($this->_invoice));

        $childBlock->setText($expectedHtml);
        $actualHtml = $this->_block->getInvoiceCommentsHtml($this->_invoice);
        $this->assertSame($this->_invoice, $childBlock->getEntity());
        $this->assertNotEmpty($childBlock->getTitle());
        $this->assertEquals($expectedHtml, $actualHtml);
    }
}
