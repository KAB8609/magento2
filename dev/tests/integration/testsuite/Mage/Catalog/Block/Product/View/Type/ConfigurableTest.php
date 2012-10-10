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
 * Test class for Mage_Catalog_Block_Product_View_Type_Configurable.
 *
 * magentoDataFixture Mage/Catalog/_files/product_configurable.php
 */
class Mage_Catalog_Block_Product_View_Type_ConfigurableTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Catalog_Block_Product_View_Type_Configurable
     */
    protected $_block;

    protected $_product;

    protected function setUp()
    {
        $this->markTestIncomplete('Need to fix DI dependencies + fixture');

        $this->_product = Mage::getModel('Mage_Catalog_Model_Product');
        $this->_product->load(1);
        $this->_block = Mage::app()->getLayout()->createBlock('Mage_Catalog_Block_Product_View_Type_Configurable');
        $this->_block->setProduct($this->_product);
    }

    public function testGetAllowAttributes()
    {
        $attributes = $this->_block->getAllowAttributes();
        $this->assertInstanceOf(
            'Mage_Catalog_Model_Resource_Product_Type_Configurable_Attribute_Collection',
            $attributes
        );
        $this->assertGreaterThanOrEqual(1, $attributes->getSize());
    }

    public function testHasOptions()
    {
        $this->assertTrue($this->_block->hasOptions());
    }

    public function testGetAllowProducts()
    {
        $products = $this->_block->getAllowProducts();
        $this->assertGreaterThanOrEqual(2, count($products));
        foreach ($products as $products) {
            $this->assertInstanceOf('Mage_Catalog_Model_Product', $products);
        }
    }

    public function testGetJsonConfig()
    {
        $config = (array) json_decode($this->_block->getJsonConfig());
        $this->assertNotEmpty($config);
        $this->assertArrayHasKey('attributes', $config);
        $this->assertArrayHasKey('template', $config);
        $this->assertArrayHasKey('basePrice', $config);
        $this->assertArrayHasKey('productId', $config);
        $this->assertEquals(1, $config['productId']);
    }
}
