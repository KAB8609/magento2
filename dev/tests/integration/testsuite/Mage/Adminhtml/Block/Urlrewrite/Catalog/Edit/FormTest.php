<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Adminhtml
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Adminhtml_Block_Urlrewrite_Catalog_Edit_FormTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Varien_Data_Form
     */
    protected $_form = null;

    /**
     * Initialize form
     *
     * @param array $args
     */
    protected function _initForm($args = array())
    {
        $layout = new Mage_Core_Model_Layout();
        /** @var $block Mage_Adminhtml_Block_Urlrewrite_Catalog_Edit_Form */
        $block = $layout->createBlock('Mage_Adminhtml_Block_Urlrewrite_Catalog_Edit_Form', 'block', $args);
        $block->toHtml();
        $this->_form = $block->getForm();
    }

    /**
     * Unset block
     */
    protected function tearDown()
    {
        unset($this->_form);
        parent::tearDown();
    }

    /**
     * Check _formPostInit set expected fields values
     *
     * @covers Mage_Adminhtml_Block_Urlrewrite_Catalog_Edit_Form::_formPostInit
     *
     * @dataProvider formPostInitDataProvider
     *
     * @param array $productData
     * @param array $categoryData
     * @param string $action
     * @param string $idPath
     * @param string $requestPath
     * @param string $targetPath
     */
    public function testFormPostInitNew($productData, $categoryData, $action, $idPath, $requestPath, $targetPath)
    {
        $args = array();
        if ($productData) {
            $args['product'] = new Varien_Object($productData);
        }
        if ($categoryData) {
            $args['category'] = new Varien_Object($categoryData);
        }
        $this->_initForm($args);
        $this->assertContains($action, $this->_form->getAction());

        $this->assertEquals($idPath, $this->_form->getElement('id_path')->getValue());
        $this->assertEquals($requestPath, $this->_form->getElement('request_path')->getValue());
        $this->assertEquals($targetPath, $this->_form->getElement('target_path')->getValue());

        $this->assertTrue($this->_form->getElement('id_path')->getData('disabled'));
        $this->assertTrue($this->_form->getElement('target_path')->getData('disabled'));
    }

    /**
     * Test entity stores
     *
     * @dataProvider getEntityStoresDataProvider
     * @magentoAppIsolation enabled
     * @magentoDataFixture Mage/Core/_files/store.php
     *
     * @param array $productData
     * @param array $categoryData
     * @param array $expectedStores
     */
    public function testGetEntityStores($productData, $categoryData, $expectedStores)
    {
        $args = array();
        if ($productData) {
            $args['product'] = new Varien_Object($productData);
        }
        if ($categoryData) {
            $args['category'] = new Varien_Object($categoryData);
        }
        $this->_initForm($args);
        $this->assertEquals($expectedStores, $this->_form->getElement('store_id')->getValues());
    }

    /**
     * Check exception is thrown when product does not associated with stores
     *
     * @magentoAppIsolation enabled
     * @magentoDataFixture Mage/Core/_files/store.php
     *
     * @expectedException Mage_Core_Model_Store_Exception
     * @expectedExceptionMessage Chosen product does not associated with any website, so URL rewrite is not possible.
     */
    public function testGetEntityStoresProductStoresException()
    {
        $args = array(
            'product' => new Varien_Object(array('id' => 1))
        );
        $this->_initForm($args);
    }

    /**
     * Check exception is thrown when product stores in intersection with category stores is empty
     *
     * @magentoAppIsolation enabled
     * @magentoDataFixture Mage/Core/_files/store.php
     *
     * @expectedException Mage_Core_Model_Store_Exception
     * @expectedExceptionMessage Chosen product does not associated with any website, so URL rewrite is not possible.
     */
    public function testGetEntityStoresProductCategoryStoresException()
    {
        $args = array(
            'product' => new Varien_Object(array('id' => 1, 'store_ids' => array(1))),
            'category' => new Varien_Object(array('id' => 1, 'store_ids' => array(3)))
        );
        $this->_initForm($args);
    }

    /**
     * Check exception is thrown when category does not associated with stores
     *
     * @magentoAppIsolation enabled
     * @magentoDataFixture Mage/Core/_files/store.php
     *
     * @expectedException Mage_Core_Model_Store_Exception
     * @expectedExceptionMessage Chosen category does not associated with any website, so URL rewrite is not possible.
     */
    public function testGetEntityStoresCategoryStoresException()
    {
        $args = array(
            'category' => new Varien_Object(array('id' => 1))
        );
        $this->_initForm($args);
    }

    /**
     * Data provider for testing formPostInit
     * 1) Category selected
     * 2) Product selected
     * 3) Product with category selected
     *
     * @static
     * @return array
     */
    public static function formPostInitDataProvider()
    {
        return array(
            array(
                null, array('id' => 3, 'level' => 2, 'url_key' => 'category'),
                'category/3', 'category/3', 'category.html', 'catalog/category/view/id/3'
            ),
            array(
                array('id' => 2, 'url_key' => 'product'), null,
                'product/2', 'product/2', 'product.html', 'catalog/product/view/id/2'
            ),
            array(
                array('id' => 2, 'name' => 'product'), array('id' => 3, 'level' => 2, 'url_key' => 'category'),
                'product/2/category/3', 'product/2/3', 'category/product.html', 'catalog/product/view/id/2/category/3'
            )
        );
    }

    /**
     * Entity stores data provider
     * 1) Category assigned to 1 store
     * 2) Product assigned to 1 store
     * 3) Product and category are assigned to same store
     *
     * @static
     * @return array
     */
    public static function getEntityStoresDataProvider()
    {
        return array(
            array(
                null, array('id' => 3, 'store_ids' => array(1)),
                array(
                    array(
                        'label' => 'Main Website',
                        'value' => array()
                    ),
                    array(
                        'label' => '    Main Website Store',
                        'value' => array(
                            array(
                                'label' => '    Default Store View',
                                'value' => 1
                            )
                        )
                    )
                )
            ),
            array(
                array('id' => 2, 'store_ids' => array(1)), null,
                array(
                    array(
                        'label' => 'Main Website',
                        'value' => array()
                    ),
                    array(
                        'label' => '    Main Website Store',
                        'value' => array(
                            array(
                                'label' => '    Default Store View',
                                'value' => 1
                            )
                        )
                    )
                )
            ),
            array(
                array('id' => 2, 'store_ids' => array(1)), array('id' => 3, 'store_ids' => array(1)),
                array(
                    array(
                        'label' => 'Main Website',
                        'value' => array()
                    ),
                    array(
                        'label' => '    Main Website Store',
                        'value' => array(
                            array(
                                'label' => '    Default Store View',
                                'value' => 1
                            )
                        )
                    )
                )
            ),
        );
    }
}
