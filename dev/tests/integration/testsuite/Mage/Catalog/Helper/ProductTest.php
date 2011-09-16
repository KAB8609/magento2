<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @group module:Mage_Catalog
 */
class Mage_Catalog_Helper_ProductTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Catalog_Helper_Product
     */
    protected $_helper;

    protected function setUp()
    {
        $this->_helper = new Mage_Catalog_Helper_Product;
    }

    /**
     * @magentoDataFixture Mage/Catalog/_files/products.php
     */
    public function testGetProductUrl()
    {
        // product as object
        $product = new Mage_Catalog_Model_Product;
        $this->assertStringEndsWith('/catalog/product/view/', $this->_helper->getProductUrl($product));

        // product as ID
        $this->assertStringEndsWith('/catalog/product/view/id/1/s/simple-product/', $this->_helper->getProductUrl(1));
    }

    public function testGetPrice()
    {
        $product = new Mage_Catalog_Model_Product;
        $product->setPrice(49.95);
        $this->assertEquals(49.95, $this->_helper->getPrice($product));
    }

    public function testGetFinalPrice()
    {
        $product = new Mage_Catalog_Model_Product;
        $product->setFinalPrice(49.95);
        $this->assertEquals(49.95, $this->_helper->getFinalPrice($product));
    }

    public function testGetImageUrl()
    {
        $product = new Mage_Catalog_Model_Product;
        $this->assertStringEndsWith('images/no_image.jpg', $this->_helper->getImageUrl($product));

        $product->setImage('test_image.png');
        $this->assertStringEndsWith('/test_image.png', $this->_helper->getImageUrl($product));
    }

    public function testGetSmallImageUrl()
    {
        $product = new Mage_Catalog_Model_Product;
        $this->assertStringEndsWith('images/no_image.jpg', $this->_helper->getSmallImageUrl($product));

        $product->setSmallImage('test_image.png');
        $this->assertStringEndsWith('/test_image.png', $this->_helper->getSmallImageUrl($product));
    }

    public function testGetThumbnailUrl()
    {
        $this->assertEmpty($this->_helper->getThumbnailUrl(new Mage_Catalog_Model_Product));
    }

    public function testGetEmailToFriendUrl()
    {
        $product = new Mage_Catalog_Model_Product;
        $product->setId(100);
        $category = new Mage_Catalog_Model_Category;
        $category->setId(10);
        Mage::register('current_category', $category);

        try {
            $this->assertStringEndsWith(
                'sendfriend/product/send/id/100/cat_id/10/', $this->_helper->getEmailToFriendUrl($product)
            );
            Mage::unregister('current_category');
        } catch (Exception $e) {
            Mage::unregister('current_category');
            throw $e;
        }
    }

    public function testGetStatuses()
    {
        $this->assertEquals(array(), $this->_helper->getStatuses());
    }

    /**
     * @magentoDataFixture Mage/Catalog/_files/products.php
     */
    public function testCanShow()
    {
        // non-visible or disabled
        $product = new Mage_Catalog_Model_Product;
        $this->assertFalse($this->_helper->canShow($product));

        // enabled and visible
        $product->setId(1);
        $product->setStatus(Mage_Catalog_Model_Product_Status::STATUS_ENABLED);
        $product->setVisibility(Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH);
        $this->assertTrue($this->_helper->canShow($product));

        $this->assertTrue($this->_helper->canShow(1));
    }

    public function testGetProductUrlSuffix()
    {
        $this->assertEquals('.html', $this->_helper->getProductUrlSuffix());
    }

    public function testCanUseCanonicalTagDefault()
    {
        $this->assertEquals('0', $this->_helper->canUseCanonicalTag());
    }

    /**
     * @magentoConfigFixture current_store catalog/seo/product_canonical_tag 1
     */
    public function testCanUseCanonicalTag()
    {
        $this->assertEquals(1, $this->_helper->canUseCanonicalTag());
    }

    public function testGetAttributeInputTypes()
    {
        $types = $this->_helper->getAttributeInputTypes();
        $this->assertArrayHasKey('multiselect', $types);
        $this->assertArrayHasKey('boolean', $types);
        foreach ($types as $type) {
            $this->assertInternalType('array', $type);
            $this->assertNotEmpty($type);
        }

        $this->assertNotEmpty($this->_helper->getAttributeInputTypes('multiselect'));
        $this->assertNotEmpty($this->_helper->getAttributeInputTypes('boolean'));
    }

    public function testGetAttributeBackendModelByInputType()
    {
        $this->assertNotEmpty($this->_helper->getAttributeBackendModelByInputType('multiselect'));
        $this->assertNull($this->_helper->getAttributeBackendModelByInputType('boolean'));
    }

    public function testGetAttributeSourceModelByInputType()
    {
        $this->assertNotEmpty($this->_helper->getAttributeSourceModelByInputType('boolean'));
        $this->assertNull($this->_helper->getAttributeSourceModelByInputType('multiselect'));
    }

    /**
     * @magentoDataFixture Mage/Catalog/_files/categories.php
     * @magentoAppIsolation enabled
     */
    public function testInitProduct()
    {
        Mage::getSingleton('catalog/session')->setLastVisitedCategoryId(2);
        $this->_helper->initProduct(1, 'view');
        $this->assertInstanceOf('Mage_Catalog_Model_Product', Mage::registry('current_product'));
        $this->assertInstanceOf('Mage_Catalog_Model_Category', Mage::registry('current_category'));
    }

    public function testPrepareProductOptions()
    {
        $product = new Mage_Catalog_Model_Product;
        $buyRequest = new Varien_Object(array('qty' => 100, 'options' => array('option' => 'value')));
        $this->_helper->prepareProductOptions($product, $buyRequest);
        $result = $product->getPreconfiguredValues();
        $this->assertInstanceOf('Varien_Object', $result);
        $this->assertEquals(100, $result->getQty());
        $this->assertEquals(array('option' => 'value'), $result->getOptions());
    }
}
