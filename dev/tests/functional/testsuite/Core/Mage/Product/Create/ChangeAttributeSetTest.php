<?php
/**
 * Magento
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Product
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Products creation tests with ability to change attribute set during creation and editing products
 */
class Core_Mage_Product_Create_ChangeAttributeSetTest extends Mage_Selenium_TestCase
{
    protected function assertPreConditions()
    {
        $this->loginAdminUser();
        $this->navigate('manage_products');
    }

    /**
     * @test
     *
     * @return array
     */
    public function preconditionsForTests()
    {
        //Data
        $productAttributes = $this->loadDataSet('ProductAttribute', 'product_attributes_for_changing_template');
        $productAttributes['assignedAttribute'] = $this->loadDataSet('ProductAttribute',
            'product_attribute_dropdown_with_options');
        $groupName = $this->generate('string', 5, ':lower:') . '_test_group';
        $attributeSet = $this->loadDataSet('AttributeSet', 'attribute_set');
        $attributeCodes = array();
        //Create attributes
        $this->navigate('manage_attributes');
        foreach ($productAttributes as $name => $attribute) {
            $this->productAttributeHelper()->createAttribute($attribute);
            $this->assertMessagePresent('success', 'success_saved_attribute');
            if ($name == 'assignedAttribute') {
                $attributeSet['associated_attributes']['Product Details'] = $attribute['attribute_code'];
            } else {
                $attributeCodes[] = $attribute['attribute_code'];
            }
        }
        //Create attribute set
        $attributeSet['associated_attributes'][$groupName] = $attributeCodes;
        $this->navigate('manage_attribute_sets');
        $this->attributeSetHelper()->createAttributeSet($attributeSet);
        $this->assertMessagePresent('success', 'success_attribute_set_saved');

        return array(
            'attributeSetName' => $attributeSet['set_name'],
            'assignedAttribute' => $productAttributes['assignedAttribute']['attribute_code'],
            'tabName' => $groupName,
            'attributeCodes' => $attributeCodes,
        );
    }

    /**
     * Change attribute set during product creation from Default to Custom attribute set
     *
     * @param string $productType
     * @param array $attributeSetData
     *
     * @test
     * @dataProvider productTypeDataProvider
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-5881, TL-MAGE-5896, TL-MAGE-5900, TL-MAGE-5901, TL-MAGE-5902
     */
    public function fromDefaultToCustomCreate($productType, $attributeSetData)
    {
        //Data
        $productDataInitial = $this->loadDataSet('Product', $productType . '_product_visible');
        $assignedAttribute = $attributeSetData['assignedAttribute'];
        $newAttributeSet = $attributeSetData['attributeSetName'];
        //Steps
        $this->productHelper()->createProduct($productDataInitial, $productType, false);
        $this->productHelper()->changeAttributeSet($newAttributeSet);
        //Verifying
        $this->addParameter('attributeCodeDropdown', $assignedAttribute);
        $this->assertTrue($this->controlIsVisible('dropdown', 'general_user_attr_dropdown'),
            "There is absent attribute $assignedAttribute, but shouldn't");
        $this->productHelper()->verifyProductInfo($productDataInitial);
        $this->productHelper()->saveProduct();
        $this->assertMessagePresent('success', 'success_saved_product');
    }

    /**
     * Change attribute set during product creating from Custom to Default attribute set
     *
     * @param string $productType
     * @param $attributeSetData
     *
     * @test
     * @dataProvider productTypeDataProvider
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-5903, TL-MAGE-5904, TL-MAGE-5905, TL-MAGE-5906, TL-MAGE-5907
     */
    public function fromCustomToDefaultDuringCreation($productType, $attributeSetData)
    {
        //Data
        $productDataInitial = $this->loadDataSet('Product', $productType . '_product_visible',
            array('product_attribute_set' => $attributeSetData['attributeSetName']));
        $newAttributeSet = 'Default';
        $assignedAttribute = $attributeSetData['assignedAttribute'];
        //Steps
        $this->productHelper()->createProduct($productDataInitial, $productType, false);
        $this->productHelper()->changeAttributeSet($newAttributeSet);
        //Verifying
        $this->addParameter('attributeCodeDropdown', $assignedAttribute);
        $this->assertFalse($this->controlIsVisible('dropdown', 'general_user_attr_dropdown'),
            "There is present $assignedAttribute attribute, but shouldn't");
        $this->productHelper()->verifyProductInfo($productDataInitial);
        $this->productHelper()->saveProduct();
        $this->assertMessagePresent('success', 'success_saved_product');
    }

    /**
     * Change attribute set during product editing from Default to Custom attribute set
     *
     * @param string $productType
     * @param $attributeSetData
     *
     * @test
     * @dataProvider productTypeDataProvider
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-5884, TL-MAGE-5908, TL-MAGE-5909, TL-MAGE-5910, TL-MAGE-5911
     */
    public function fromDefaultToCustomDuringEditing($productType, $attributeSetData)
    {
        //Data
        $productDataInitial = $this->loadDataSet('Product', $productType . '_product_visible');
        $assignedAttribute = $attributeSetData['assignedAttribute'];
        $newAttributeSet = $attributeSetData['attributeSetName'];
        //Steps
        $this->productHelper()->createProduct($productDataInitial, $productType);
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->productHelper()->openProduct(array('product_sku' => $productDataInitial['general_sku']));
        $this->productHelper()->changeAttributeSet($newAttributeSet);
        //Verifying
        $this->addParameter('attributeCodeDropdown', $assignedAttribute);
        $this->assertTrue($this->controlIsVisible('dropdown', 'general_user_attr_dropdown'),
            "There is absent attribute $assignedAttribute, but shouldn't");
        $this->productHelper()->verifyProductInfo($productDataInitial);
        $this->productHelper()->saveProduct();
        $this->assertMessagePresent('success', 'success_saved_product');
    }

    /**
     * Change attribute set during product editing from Custom to Default attribute set
     *
     * @param string $productType
     * @param $attributeSetData
     *
     * @test
     * @dataProvider productTypeDataProvider
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-5912, TL-MAGE-5913, TL-MAGE-5914, TL-MAGE-5915, TL-MAGE-5916
     */
    public function fromCustomToDefaultDuringEditing($productType, $attributeSetData)
    {
        //Data
        $productDataInitial = $this->loadDataSet('Product', $productType . '_product_visible',
            array('product_attribute_set' => $attributeSetData['attributeSetName']));
        $newAttributeSet = 'Default';
        $assignedAttribute = $attributeSetData['assignedAttribute'];
        //Steps
        $this->productHelper()->createProduct($productDataInitial, $productType);
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->productHelper()->openProduct(array('product_sku' => $productDataInitial['general_sku']));
        $this->productHelper()->changeAttributeSet($newAttributeSet);
        //Verifying
        $this->addParameter('attributeCodeDropdown', $assignedAttribute);
        $this->assertFalse($this->controlIsVisible('dropdown', 'general_user_attr_dropdown'),
            "There is present $assignedAttribute attribute, but shouldn't");
        $this->productHelper()->verifyProductInfo($productDataInitial);
        $this->productHelper()->saveProduct();
        $this->assertMessagePresent('success', 'success_saved_product');
    }

    /**
     * @return array
     */
    public function productTypeDataProvider()
    {
        return array(
            array('simple'),
            array('virtual'),
            array('downloadable'),
            array('grouped'),
            array('bundle')
        );
    }

    /**
     * Change attribute set for Configurable product during creation
     *
     * @test
     * @testlinkId TL-MAGE-6471
     */
    public function forConfigurableDuringCreation()
    {
        //Steps
        $this->productHelper()->selectTypeProduct('configurable');
        //Verifying
        $this->assertTrue($this->controlIsVisible('button', 'attribute_set_toggle_disabled'));
    }

    /**
     * Change attribute set to new one with all type of user attribute several times
     *
     * @param array $attributeSetData
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-6886, TL-MAGE-6887, TL-MAGE-6888, TL-MAGE-6889, TL-MAGE-6891, TL-MAGE-6893, TL-MAGE-6894,
     *             TL-MAGE-6895, TL-MAGE-6902
     */
    public function changeAttributeSetSeveralTimes(array $attributeSetData)
    {
        //Data
        $productDefaultSet = $this->loadDataSet('Product', 'simple_product_visible');
        $search = $this->loadDataSet('Product', 'product_search',
            array('product_sku' => $productDefaultSet['general_sku']));
        //Steps
        $this->productHelper()->createProduct($productDefaultSet, 'simple', false);
        for ($i = 0; $i < 3; $i++) {
            $this->productHelper()->changeAttributeSet($attributeSetData['attributeSetName']);
            $this->addParameter('productTabName', $attributeSetData['tabName']);
            $this->productHelper()->openProductTab('user_tab');
            //Verifying
            foreach ($attributeSetData['attributeCodes'] as $code) {
                $this->addParameter('elementId', strstr($code, '_fpt_') ? 'attribute-' . $code . '-container' : $code);
                $this->assertTrue($this->controlIsVisible('pageelement', 'element_by_id'));
            }
            $this->productHelper()->verifyProductInfo($productDefaultSet);
            $this->productHelper()->changeAttributeSet($productDefaultSet['product_attribute_set']);
        }
        $this->productHelper()->changeAttributeSet($attributeSetData['attributeSetName']);
        $this->productHelper()->saveProduct();
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->assertEquals($attributeSetData['attributeSetName'],
            $this->productHelper()->getProductDataFromGrid($search, 'Attrib. Set Name'),
            'Product has been saved with incorrect attribute set.'
        );
    }

    /**
     * Change attribute set with required field
     *
     * @param array $attributeSetData
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-6900
     */
    public function withRequiredAttribute(array $attributeSetData)
    {
        //Data
        $product = $this->loadDataSet('Product', 'simple_product_required',
            array('product_attribute_set' => $attributeSetData['attributeSetName']));
        $attribute = array('values_required' => 'Yes');
        //Preconditions
        $this->navigate('manage_attributes');
        $this->productAttributeHelper()->editAttribute($attributeSetData['assignedAttribute'], $attribute);
        //Steps
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($product, 'simple', false);
        $this->productHelper()->openProductTab('general');
        //Verifying
        $this->addParameter('attributeCodeDropdown', $attributeSetData['assignedAttribute']);
        $this->assertTrue($this->controlIsVisible('dropdown', 'general_user_attr_dropdown'),
            'Attribute with code ' . $attributeSetData['assignedAttribute'] . ' is absent');
        $this->productHelper()->changeAttributeSet('Default');
        $this->productHelper()->saveProduct();
        $this->assertMessagePresent('success', 'success_saved_product');
    }

    /**
     * Change attribute set with product related attributes
     *
     * @param array $attributeSetData
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-6901
     */
    public function withProductRelatedAttribute(array $attributeSetData)
    {
        //Data
        $product = $this->loadDataSet('Product', 'virtual_product_required',
            array('product_attribute_set' => $attributeSetData['attributeSetName']));
        $attribute = array('apply_to' => 'Selected Product Types', 'apply_product_types' => 'Virtual Product');
        //Preconditions
        $this->navigate('manage_attributes');
        $this->productAttributeHelper()->editAttribute($attributeSetData['assignedAttribute'], $attribute);
        //Steps
        $this->navigate('manage_products');
        $this->productHelper()->selectTypeProduct('simple');
        $this->productHelper()->changeAttributeSet($product['product_attribute_set']);
        //Verifying
        $this->addParameter('attributeCodeDropdown', $attributeSetData['assignedAttribute']);
        $this->assertFalse($this->controlIsVisible('dropdown', 'general_user_attr_dropdown'),
            'Attribute with code ' . $attributeSetData['assignedAttribute'] . ' is present');
        $this->fillCheckbox('general_weight_and_type_switcher', 'Yes');
        $this->assertTrue($this->controlIsVisible('dropdown', 'general_user_attr_dropdown'),
            'Attribute with code ' . $attributeSetData['assignedAttribute'] . ' is absent');
        $this->productHelper()->changeAttributeSet('Default');
        $this->assertFalse($this->controlIsVisible('dropdown', 'general_user_attr_dropdown'),
            'Attribute with code ' . $attributeSetData['assignedAttribute'] . ' is present');
        $this->productHelper()->fillProductInfo($product);
        $this->productHelper()->saveProduct();
        $this->assertMessagePresent('success', 'success_saved_product');
    }
}
