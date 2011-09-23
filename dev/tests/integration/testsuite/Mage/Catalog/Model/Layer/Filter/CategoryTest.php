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
 * Test class for Mage_Catalog_Model_Layer_Filter_Category.
 *
 * @group module:Mage_Catalog
 * @magentoDataFixture Mage/Catalog/_files/categories.php
 */
class Mage_Catalog_Model_Layer_Filter_CategoryTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Catalog_Model_Layer_Filter_Category
     */
    protected $_model;

    /**
     * @var Mage_Catalog_Model_Category
     */
    protected $_category;

    protected function setUp()
    {
        $this->_category = new Mage_Catalog_Model_Category;
        $this->_category->load(5);
        $this->_model = new Mage_Catalog_Model_Layer_Filter_Category();
        $this->_model->setData(array(
            'layer' => new Mage_Catalog_Model_Layer(array(
                'current_category' => $this->_category,
            )),
        ));
    }

    public function testGetResetValue()
    {
        $this->assertNull($this->_model->getResetValue());
    }

    public function testApplyNothing()
    {
        $this->_model->apply(new Magento_Test_Request(), new Mage_Core_Block_Text());

        $this->assertNull(Mage::registry('current_category_filter'));
    }

    public function testApply()
    {
        $request = new Magento_Test_Request();
        $request->setParam('cat', 3);
        $this->_model->apply($request, new Mage_Core_Block_Text());

        /** @var $category Mage_Catalog_Model_Category */
        $category = Mage::registry('current_category_filter');
        $this->assertInstanceOf('Mage_Catalog_Model_Category', $category);
        $this->assertEquals(3, $category->getId());

        return $this->_model;
    }

    /**
     * @depends testApply
     */
    public function testGetResetValueApplied(Mage_Catalog_Model_Layer_Filter_Category $modelApplied)
    {
        $this->assertEquals(2, $modelApplied->getResetValue());
    }

    public function testGetName()
    {
        $this->assertEquals('Category', $this->_model->getName());
    }

    public function testGetCategory()
    {
        $this->assertSame($this->_category, $this->_model->getCategory());
    }

    /**
     * @depends testApply
     */
    public function testGetCategoryApplied(Mage_Catalog_Model_Layer_Filter_Category $modelApplied)
    {
        $category = $modelApplied->getCategory();
        $this->assertInstanceOf('Mage_Catalog_Model_Category', $category);
        $this->assertEquals(3, $category->getId());
    }

    /**
     * @depends testApply
     */
    public function testGetItems(Mage_Catalog_Model_Layer_Filter_Category $modelApplied)
    {
        $items = $modelApplied->getItems();

        $this->assertInternalType('array', $items);
        $this->assertEquals(1, count($items));

        /** @var $item Mage_Catalog_Model_Layer_Filter_Item */
        $item = $items[0];

        $this->assertInstanceOf('Mage_Catalog_Model_Layer_Filter_Item', $item);
        $this->assertSame($modelApplied, $item->getFilter());
        $this->assertEquals('Category 1.1', $item->getLabel());
        $this->assertEquals(4, $item->getValue());
        $this->assertEquals(1, $item->getCount());
    }
}
