<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test for Magento_Adminhtml_Block_Urlrewrite_Catalog_Edit_FormTest
 * @magentoAppArea adminhtml
 */
class Magento_Adminhtml_Block_Urlrewrite_Catalog_Edit_FormTest extends PHPUnit_Framework_TestCase
{
    /**
     * Get form instance
     *
     * @param array $args
     * @return Magento_Data_Form
     */
    protected function _getFormInstance($args = array())
    {
        /** @var $layout Magento_Core_Model_Layout */
        $layout = Mage::getModel('Magento_Core_Model_Layout');
        /** @var $block Magento_Adminhtml_Block_Urlrewrite_Catalog_Edit_Form */
        $block = $layout->createBlock(
            'Magento_Adminhtml_Block_Urlrewrite_Catalog_Edit_Form', 'block', array('data' => $args)
        );
        $block->setTemplate(null);
        $block->toHtml();
        return $block->getForm();
    }

    /**
     * Check _formPostInit set expected fields values
     *
     * @covers Magento_Adminhtml_Block_Urlrewrite_Catalog_Edit_Form::_formPostInit
     *
     * @dataProvider formPostInitDataProvider
     *
     * @param array $productData
     * @param array $categoryData
     * @param string $action
     * @param string $idPath
     * @param string $requestPath
     * @param string $targetPath
     * @magentoConfigFixture current_store general/single_store_mode/enabled 1
     */
    public function testFormPostInitNew($productData, $categoryData, $action, $idPath, $requestPath, $targetPath)
    {
        $args = array();
        if ($productData) {
            $args['product'] = new Magento_Object($productData);
        }
        if ($categoryData) {
            $args['category'] = new Magento_Object($categoryData);
        }
        $form = $this->_getFormInstance($args);
        $this->assertContains($action, $form->getAction());

        $this->assertEquals($idPath, $form->getElement('id_path')->getValue());
        $this->assertEquals($requestPath, $form->getElement('request_path')->getValue());
        $this->assertEquals($targetPath, $form->getElement('target_path')->getValue());

        $this->assertTrue($form->getElement('id_path')->getData('disabled'));
        $this->assertTrue($form->getElement('target_path')->getData('disabled'));
    }

    /**
     * Test entity stores
     *
     * @dataProvider getEntityStoresDataProvider
     * @magentoAppIsolation enabled
     * @magentoDataFixture Magento/Core/_files/store.php
     *
     * @param array $productData
     * @param array $categoryData
     * @param array $expectedStores
     */
    public function testGetEntityStores($productData, $categoryData, $expectedStores)
    {
        $args = array();
        if ($productData) {
            $args['product'] = new Magento_Object($productData);
        }
        if ($categoryData) {
            $args['category'] = new Magento_Object($categoryData);
        }
        $form = $this->_getFormInstance($args);
        $this->assertEquals($expectedStores, $form->getElement('store_id')->getValues());
    }

    /**
     * Check exception is thrown when product does not associated with stores
     *
     * @magentoAppIsolation enabled
     * @magentoDataFixture Magento/Core/_files/store.php
     *
     * @expectedException Magento_Core_Model_Store_Exception
     * @expectedExceptionMessage We can't set up a URL rewrite because the product you chose is not associated with
     */
    public function testGetEntityStoresProductStoresException()
    {
        $args = array(
            'product' => new Magento_Object(array('id' => 1))
        );
        $this->_getFormInstance($args);
    }

    /**
     * Check exception is thrown when product stores in intersection with category stores is empty
     *
     * @magentoAppIsolation enabled
     * @magentoDataFixture Magento/Core/_files/store.php
     *
     * @expectedException Magento_Core_Model_Store_Exception
     * @expectedExceptionMessage We can't set up a URL rewrite because the product you chose is not associated with
     */
    public function testGetEntityStoresProductCategoryStoresException()
    {
        $args = array(
            'product' => new Magento_Object(array('id' => 1, 'store_ids' => array(1))),
            'category' => new Magento_Object(array('id' => 1, 'store_ids' => array(3)))
        );
        $this->_getFormInstance($args);
    }

    /**
     * Check exception is thrown when category does not associated with stores
     *
     * @magentoAppIsolation enabled
     * @magentoDataFixture Magento/Core/_files/store.php
     *
     * @expectedException Magento_Core_Model_Store_Exception
     * @expectedExceptionMessage We can't set up a URL rewrite because the category your chose is not associated with
     */
    public function testGetEntityStoresCategoryStoresException()
    {
        $args = array(
            'category' => new Magento_Object(array('id' => 1))
        );
        $this->_getFormInstance($args);
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