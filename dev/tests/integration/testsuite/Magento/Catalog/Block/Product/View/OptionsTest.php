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
 * Test class for Magento_Catalog_Block_Product_View_Options.
 *
 * @magentoDataFixture Magento/Catalog/_files/product_simple.php
 */
class Magento_Catalog_Block_Product_View_OptionsTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Catalog_Block_Product_View_Options
     */
    protected $_block;

    /**
     * @var Magento_Catalog_Model_Product
     */
    protected $_product;

    protected function setUp()
    {
        $this->_product = Mage::getModel('Magento_Catalog_Model_Product');
        $this->_product->load(1);
        Mage::unregister('current_product');
        Mage::register('current_product', $this->_product);
        $this->_block = Mage::app()->getLayout()->createBlock('Magento_Catalog_Block_Product_View_Options');
    }

    public function testSetGetProduct()
    {
        $this->assertSame($this->_product, $this->_block->getProduct());

        $product = Mage::getModel('Magento_Catalog_Model_Product');
        $this->_block->setProduct($product);
        $this->assertSame($product, $this->_block->getProduct());
    }

    public function testGetGroupOfOption()
    {
        $this->assertEquals('default', $this->_block->getGroupOfOption('test'));
    }

    public function testGetOptions()
    {
        $options = $this->_block->getOptions();
        $this->assertNotEmpty($options);
        foreach ($options as $option) {
            $this->assertInstanceOf('Magento_Catalog_Model_Product_Option', $option);
        }
    }

    public function testHasOptions()
    {
        $this->assertTrue($this->_block->hasOptions());
    }

    public function testGetJsonConfig()
    {
        $config = json_decode($this->_block->getJsonConfig());
        $this->assertNotNull($config);
        $this->assertNotEmpty($config);
    }
}
