<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Wishlist
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Wishlist_Block_AbstractTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Wishlist_Block_Abstract
     */
    protected $_block;

    protected function setUp()
    {
        $this->_block = $this->getMockForAbstractClass(
            'Mage_Wishlist_Block_Abstract',
            array(Mage::getSingleton('Mage_Core_Block_Template_Context'))
        );
    }

    /**
     * @magentoAppIsolation enabled
     * @magentoDataFixture Mage/Catalog/_files/product_with_image.php
     * @magentoDataFixture Mage/Core/_files/frontend_default_theme.php
     */
    public function testImage()
    {
        Mage::getDesign()->setArea(Mage_Core_Model_App_Area::AREA_FRONTEND)->setDefaultDesignTheme();
        $product = Mage::getModel('Mage_Catalog_Model_Product');
        $product->load(1);

        $size = $this->_block->getImageSize();
        $this->assertGreaterThan(1, $size);
        $this->assertContains('/'.$size, $this->_block->getImageUrl($product));
        $this->assertStringEndsWith('magento_image.jpg', $this->_block->getImageUrl($product));
    }
}
