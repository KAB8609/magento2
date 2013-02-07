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
        $this->_block = Mage::app()->getLayout()->createBlock('Mage_ProductAlert_Block_Email_Stock');
    }

    protected function tearDown()
    {
        $this->_block = null;
    }

    /**
     * @magentoDataFixture Mage/Core/_files/frontend_default_theme.php
     * @magentoDataFixture Mage/Catalog/_files/product_with_image.php
     */
    public function testThumbnail()
    {
        $product = Mage::getModel('Mage_Catalog_Model_Product');
        $product->load(1);

        $size = $this->_block->getThumbnailSize();
        $this->assertGreaterThan(1, $size);
        $this->assertContains('/'.$size, $this->_block->getThumbnailUrl($product));
        $this->assertStringEndsWith('magento_image.jpg', $this->_block->getThumbnailUrl($product));
    }
}
