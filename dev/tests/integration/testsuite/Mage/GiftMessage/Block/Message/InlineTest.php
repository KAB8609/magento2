<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_GiftMessage
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_GiftMessage_Block_Message_InlineTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_GiftMessage_Block_Message_Inline
     */
    protected $_block;

    protected function setUp()
    {
        $this->_block = Mage::app()->getLayout()->createBlock('Mage_GiftMessage_Block_Message_Inline');
    }

    /**
     * @magentoDataFixture Magento/Catalog/_files/product_with_image.php
     */
    public function testThumbnail()
    {
        Mage::app()->getArea(Magento_Core_Model_App_Area::AREA_FRONTEND)->load();
        $product = Mage::getModel('Magento_Catalog_Model_Product');
        $product->load(1);

        $size = $this->_block->getThumbnailSize();
        $this->assertGreaterThan(1, $size);
        $this->assertContains('/'.$size, $this->_block->getThumbnailUrl($product));
        $this->assertStringEndsWith('magento_image.jpg', $this->_block->getThumbnailUrl($product));
    }
}
