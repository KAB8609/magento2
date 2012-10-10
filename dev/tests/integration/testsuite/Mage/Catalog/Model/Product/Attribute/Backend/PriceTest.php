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
 * Test class for Mage_Catalog_Model_Product_Attribute_Backend_Price.
 *
 * magentoDataFixture Mage/Catalog/_files/product_simple.php
 */
class Mage_Catalog_Model_Product_Attribute_Backend_PriceTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Catalog_Model_Product_Attribute_Backend_Price
     */
    protected $_model;

    protected function setUp()
    {
        $this->markTestIncomplete('Need to fix DI dependencies + fixture');

        $this->_model = Mage::getModel('Mage_Catalog_Model_Product_Attribute_Backend_Price');
        $this->_model->setAttribute(
            Mage::getSingleton('Mage_Eav_Model_Config')->getAttribute('catalog_product', 'price')
        );
    }

    protected function tearDown()
    {
        $this->_model = null;
    }

    public function testSetScopeDefault()
    {
        /* validate result of setAttribute */
        $this->assertEquals(
            Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
            $this->_model->getAttribute()->getIsGlobal()
        );
        $this->_model->setScope($this->_model->getAttribute());
        $this->assertEquals(
            Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
            $this->_model->getAttribute()->getIsGlobal()
        );
    }

    /**
     * @magentoConfigFixture current_store catalog/price/scope 1
     */
    public function testSetScope()
    {
        $this->_model->setScope($this->_model->getAttribute());
        $this->assertEquals(
            Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
            $this->_model->getAttribute()->getIsGlobal()
        );
    }

    /**
     * @magentoConfigFixture current_store catalog/price/scope 1
     * @magentoConfigFixture current_store currency/options/base GBP
     */
    public function testAfterSave()
    {
        $product = Mage::getModel('Mage_Catalog_Model_Product');
        $product->load(1);
        $product->setOrigData();
        $product->setPrice(9.99);
        $product->setStoreId(0);

        $this->_model->setScope($this->_model->getAttribute());
        $this->_model->afterSave($product);

        $this->assertEquals(
            '9.99',
            $product->getResource()->getAttributeRawValue(
                $product->getId(),
                $this->_model->getAttribute()->getId(),
                Mage::app()->getStore()->getId()
            )
        );
    }
}
