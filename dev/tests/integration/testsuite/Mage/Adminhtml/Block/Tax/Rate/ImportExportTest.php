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
class Mage_Adminhtml_Block_Tax_Rate_ImportExportTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Adminhtml_Block_Catalog_Product_Attribute_Edit_Tab_Main
     */
    protected $_block = null;

    protected function setUp()
    {
        $this->_block = Mage::app()->getLayout()
            ->createBlock('Mage_Adminhtml_Block_Tax_Rate_ImportExport')
            ->setArea('adminhtml');
    }

    protected function tearDown()
    {
        $this->_block = null;
    }

    public function testCreateBlock()
    {
        $this->assertInstanceOf('Mage_Adminhtml_Block_Tax_Rate_ImportExport', $this->_block);
    }

    public function testFormExists()
    {
        $html = $this->_block->toHtml();

        $this->assertContains(
            '<form id="import-form"', $html
        );

        $this->assertContains(
            '<form id="export_form"', $html
        );
    }
}
