<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Catalog
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Mage_Catalog_Block_Product_List_Related.
 *
 * @magentoDataFixture Mage/Catalog/_files/products_related.php
 * @magentoDataFixture Mage/Core/_files/frontend_default_theme.php
 */
class Mage_Catalog_Block_Product_List_RelatedTest extends PHPUnit_Framework_TestCase
{
    public function testAll()
    {
        $product = Mage::getModel('Mage_Catalog_Model_Product');
        $product->load(2);
        Mage::register('product', $product);
        /** @var $block Mage_Catalog_Block_Product_List_Related */
        $block = Mage::app()->getLayout()->createBlock('Mage_Catalog_Block_Product_List_Related');
        $block->setLayout(Mage::getModel('Mage_Core_Model_Layout'));
        $block->setTemplate('product/list/related.phtml');

        $html = $block->toHtml();
        $this->assertNotEmpty($html);
        $this->assertContains('Simple Related Product', $html); /* name */
        $this->assertContains('product/1/', $html);  /* part of url */
        $this->assertInstanceOf('Mage_Catalog_Model_Resource_Product_Link_Product_Collection', $block->getItems());
    }
}
