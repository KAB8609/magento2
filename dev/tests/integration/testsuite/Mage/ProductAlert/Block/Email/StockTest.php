<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_ProductAlert
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_ProductAlert_Block_Email_StockTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_ProductAlert_Block_Email_Stock
     */
    protected $_block;

    protected function setUp()
    {
        $this->markTestIncomplete('Need to fix DI dependencies');

        $this->_block = Mage::getModel('Mage_ProductAlert_Block_Email_Stock');
    }

    protected function tearDown()
    {
        $this->_block = null;
    }

    /**
     * magentoDataFixture Mage/Catalog/_files/product_with_image.php
     */
    public function testThumbnail()
    {
        $this->markTestIncomplete('Need to fix DI dependencies + fixture');

        $product = Mage::getModel('Mage_Catalog_Model_Product');
        $product->load(1);

        $size = $this->_block->getThumbnailSize();
        $this->assertGreaterThan(1, $size);
        $this->assertContains('/'.$size, $this->_block->getThumbnailUrl($product));
        $this->assertStringEndsWith('magento_image.jpg', $this->_block->getThumbnailUrl($product));
    }
}
