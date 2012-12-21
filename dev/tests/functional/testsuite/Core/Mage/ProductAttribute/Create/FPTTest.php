<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_ProductAttribute
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Create new product attribute. Type: Fixed Product Tax
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Core_Mage_ProductAttribute_Create_FPTTest extends Mage_Selenium_TestCase
{
    /**
     * <p>Preconditions:</p>
     * <p>Navigate to System -> Manage Attributes.</p>
     */
    protected function assertPreConditions()
    {
        $this->loginAdminUser();
        $this->navigate('manage_attributes');
    }

    /**
     * @test
     */
    public function navigation()
    {
        $this->assertTrue($this->buttonIsPresent('add_new_attribute'),
            'There is no "Add New Attribute" button on the page');
        $this->clickButton('add_new_attribute');
        $this->assertTrue($this->checkCurrentPage('new_product_attribute'), $this->getParsedMessages());
        $this->assertTrue($this->buttonIsPresent('back'), 'There is no "Back" button on the page');
        $this->assertTrue($this->buttonIsPresent('reset'), 'There is no "Reset" button on the page');
        $this->assertTrue($this->buttonIsPresent('save_attribute'), 'There is no "Save" button on the page');
        $this->assertTrue($this->buttonIsPresent('save_and_continue_edit'),
            'There is no "Save and Continue Edit" button on the page');
    }

    /**
     * <p>Create "Fixed Product Tax" type Product Attribute (required fields only)</p>
     *
     * @return array
     * @test
     * @depends navigation
     * @TestlinkId TL-MAGE-3536
     */
    public function withRequiredFieldsOnly()
    {
        //Data
        $attrData = $this->loadDataSet('ProductAttribute', 'product_attribute_fpt');
        //Steps
        $this->productAttributeHelper()->createAttribute($attrData);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_attribute');

        return $attrData;
    }

    /**
     * <p>Checking of verification for duplicate of Product Attributes with similar code
     * Creation of new attribute with existing code.</p>
     *
     * @param array $attrData
     *
     * @test
     * @depends withRequiredFieldsOnly
     * @TestlinkId TL-MAGE-3527
     */
    public function withAttributeCodeThatAlreadyExists(array $attrData)
    {
        //Steps
        $this->productAttributeHelper()->createAttribute($attrData);
        //Verifying
        $this->assertMessagePresent('error', 'exists_attribute_code');
    }

    /**
     * <p>Checking validation for required fields are EMPTY</p>
     *
     * @param $emptyField
     *
     * @test
     * @dataProvider withRequiredFieldsEmptyDataProvider
     * @depends withRequiredFieldsOnly
     * @TestlinkId TL-MAGE-3535
     */
    public function withRequiredFieldsEmpty($emptyField)
    {
        //Data
        if ($emptyField == 'apply_to') {
            $attrData = $this->loadDataSet('ProductAttribute', 'product_attribute_fpt',
                array($emptyField => 'Selected Product Types'));
        } else {
            $attrData =
                $this->loadDataSet('ProductAttribute', 'product_attribute_fpt', array($emptyField => '%noValue%'));
        }
        //Steps
        $this->productAttributeHelper()->createAttribute($attrData);
        //Verifying
        if ($emptyField != 'apply_to') {
            $fieldXpath = $this->_getControlXpath('field', $emptyField);
        } else {
            $fieldXpath = $this->_getControlXpath('multiselect', 'apply_product_types');
        }
        $this->addParameter('fieldXpath', $fieldXpath);
        $this->assertMessagePresent('validation', 'empty_required_field');
        $this->assertTrue($this->verifyMessagesCount(), $this->getParsedMessages());
    }

    public function withRequiredFieldsEmptyDataProvider()
    {
        return array(
            array('attribute_code'),
            array('admin_title'),
            array('apply_to')
        );
    }

    /**
     * <p>Checking validation for valid data in the 'Attribute Code' field</p>
     *
     * @param $wrongAttributeCode
     * @param $validationMessage
     *
     * @test
     * @dataProvider withInvalidAttributeCodeDataProvider
     * @depends withRequiredFieldsOnly
     * @TestlinkId TL-MAGE-3528
     */
    public function withInvalidAttributeCode($wrongAttributeCode, $validationMessage)
    {
        //Data
        $attrData = $this->loadDataSet('ProductAttribute', 'product_attribute_fpt',
            array('attribute_code' => $wrongAttributeCode));
        //Steps
        $this->productAttributeHelper()->createAttribute($attrData);
        //Verifying
        $this->assertMessagePresent('validation', $validationMessage);
        $this->assertTrue($this->verifyMessagesCount(), $this->getParsedMessages());
    }

    public function withInvalidAttributeCodeDataProvider()
    {
        return array(
            array('11code_wrong', 'invalid_attribute_code'),
            array('CODE_wrong', 'invalid_attribute_code'),
            array('wrong code', 'invalid_attribute_code'),
            array($this->generate('string', 11, ':punct:'), 'invalid_attribute_code'),
            array($this->generate('string', 33, ':lower:'), 'wrong_length_attribute_code')
        );
    }

    /**
     * <p>Checking of correct validate of submitting form by using special</p>
     * <p>characters for all fields exclude 'Attribute Code' field.</p>
     *
     * @test
     * @depends withRequiredFieldsOnly
     * @TestlinkId TL-MAGE-3328
     */
    public function withSpecialCharactersInTitle()
    {
        //Data
        $attrData = $this->loadDataSet('ProductAttribute', 'product_attribute_fpt',
            array('admin_title' => $this->generate('string', 32, ':punct:')));
        $attrData['admin_title'] = preg_replace('/<|>/', '', $attrData['admin_title']);
        $searchData = $this->loadDataSet('ProductAttribute', 'attribute_search_data',
            array('attribute_code' => $attrData['attribute_code']));
        //Steps
        $this->productAttributeHelper()->createAttribute($attrData);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_attribute');
        //Steps
        $this->productAttributeHelper()->openAttribute($searchData);
        //Verifying
        $this->productAttributeHelper()->verifyAttribute($attrData);
    }

    /**
     * <p>Checking of correct work of submitting form by using long values for fields filling</p>
     *
     * @test
     * @depends withRequiredFieldsOnly
     * @TestlinkId TL-MAGE-3534
     */
    public function withLongValues()
    {
        //Data
        $attrData = $this->loadDataSet('ProductAttribute', 'product_attribute_fpt',
            array('attribute_code' => $this->generate('string', 30, ':lower:'),
                  'admin_title'    => $this->generate('string', 255, ':alnum:')));
        $searchData = $this->loadDataSet('ProductAttribute', 'attribute_search_data',
            array('attribute_code'  => $attrData['attribute_code'],
                  'attribute_label' => $attrData['admin_title']));
        //Steps
        $this->productAttributeHelper()->createAttribute($attrData);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_attribute');
        //Steps
        $this->productAttributeHelper()->openAttribute($searchData);
        //Verifying
        $this->productAttributeHelper()->verifyAttribute($attrData);
    }
}