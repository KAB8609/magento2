<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Checkout
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @magentoDataFixture Mage/Catalog/_files/product_with_image.php
 * @magentoDataFixture Mage/Core/_files/frontend_default_theme.php
 */
class Mage_Checkout_Block_Cart_Item_RendererTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Checkout_Block_Cart_Item_Renderer
     */
    protected $_block;

    protected function setUp()
    {
        $this->_block = Mage::app()->getLayout()->createBlock('Mage_Checkout_Block_Cart_Item_Renderer');
        /** @var $item Mage_Sales_Model_Quote_Item */
        $item = Mage::getModel('Mage_Sales_Model_Quote_Item');
        $product = Mage::getModel('Mage_Catalog_Model_Product');
        $product->load(1);
        $item->setProduct($product);
        $this->_block->setItem($item);
    }

    protected function tearDown()
    {
        $this->_block = null;
    }

    public function testThumbnail()
    {
        $size = $this->_block->getThumbnailSize();
        $sidebarSize = $this->_block->getThumbnailSidebarSize();
        $this->assertGreaterThan(1, $size);
        $this->assertGreaterThan(1, $sidebarSize);
        $this->assertContains('/'.$size, $this->_block->getProductThumbnailUrl());
        $this->assertContains('/'.$sidebarSize, $this->_block->getProductThumbnailSidebarUrl());
        $this->assertStringEndsWith('magento_image.jpg', $this->_block->getProductThumbnailUrl());
        $this->assertStringEndsWith('magento_image.jpg', $this->_block->getProductThumbnailSidebarUrl());
    }
}
