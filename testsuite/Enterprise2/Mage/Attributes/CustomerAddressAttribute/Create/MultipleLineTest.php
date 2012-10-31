<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Attributes
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Create new customer address attribute. Type: Multiple Line
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Enterprise2_Mage_Attributes_CustomerAddressAttribute_MultipleLineTest extends Mage_Selenium_TestCase
{
    /**
     * <p>Preconditions:</p>
     * <p>Navigate to Customer -> Attributes -> Manage Customer Address Attributes</p>
     */
    protected function assertPreConditions()
    {
        $this->loginAdminUser();
        $this->navigate('manage_customer_address_attributes');
    }

    /**
     * @test
     */
    public function navigation()
    {
        $this->assertTrue($this->buttonIsPresent('add_new_attribute'),
            'There is no "Add New Attribute" button on the page');
        $this->clickButton('add_new_attribute');
        $this->assertTrue($this->checkCurrentPage('new_customer_address_attribute'), $this->getParsedMessages());
        $this->assertTrue($this->buttonIsPresent('back'), 'There is no "Back" button on the page');
        $this->assertTrue($this->buttonIsPresent('reset'), 'There is no "Reset" button on the page');
        $this->assertTrue($this->buttonIsPresent('save_attribute'), 'There is no "Save" button on the page');
        $this->assertTrue($this->buttonIsPresent('save_and_continue_edit'),
            'There is no "Save and Continue Edit" button on the page');
    }

    /**
     * <p>Create "Multiple Line" type Customer Address Attributes (required fields only)</p>
     * <p>Steps:</p>
     * <p>1.Click on "Add New Attribute" button</p>
     * <p>2.Choose " Multiple Line" in 'Input Type' dropdown</p>
     * <p>3.Fill all required fields</p>
     * <p>4.Click on "Save Attribute" button</p>
     * <p>Expected result:</p>
     * <p>New attribute [" Multiple Line" type] successfully created.</p>
     * <p>Success message: 'The customer attribute has been saved.' is displayed.</p>
     *
     * @return array
     * @test
     * @depends navigation
     * @TestlinkId TL-MAGE-5557
     */
    public function withRequiredFieldsOnly()
    {
        //Data
        $attrData = $this->loadDataSet('CustomerAddressAttribute', 'customer_address_attribute_multipleline');
        //Steps
        $this->attributesHelper()->createAttribute($attrData);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_attribute');

        return $attrData;
    }

    /**
     * <p>Checking of verification for duplicate of Customer Address Attributes with similar code
     * Creation of new attribute with existing code.</p>
     * <p>Steps:</p>
     * <p>1.Click on "Add New Attribute" button</p>
     * <p>2.Choose " Multiple Line" in 'Input Type' dropdown</p>
     * <p>3.Fill 'Attribute Code' field by code used in test before.</p>
     * <p>4.Fill other required fields by regular data.</p>
     * <p>5.Click on "Save Attribute" button</p>
     * <p>Expected result:</p>
     * <p>New attribute ["Multiple Line" type] should not be created.</p>
     * <p>Error message: 'Attribute with the same code already exists' is displayed.</p>
     *
     * @param array $attrData
     *
     * @test
     * @depends withRequiredFieldsOnly
     * @TestlinkId TL-MAGE-5558
     */
    public function withAttributeCodeThatAlreadyExists(array $attrData)
    {
        //Steps
        $this->attributesHelper()->createAttribute($attrData);
        //Verifying
        $this->assertMessagePresent('error', 'exists_attribute_code');
    }

    /**
     * <p>Checking validation for required fields are EMPTY</p>
     * <p>Steps:</p>
     * <p>1.Click on "Add New Attribute" button</p>
     * <p>2.Choose " Multiple Line" in 'Input Type' dropdown</p>
     * <p>3.Skip filling of one field required and fill other required fields.</p>
     * <p>4.Click on "Save Attribute" button</p>
     * <p>Expected result:</p>
     * <p>New attribute ["Multiple Line" type] should not be created.</p>
     * <p>Error JS message: 'This is a required field.' is displayed.</p>
     *
     * @param $emptyField
     * @param $messageCount
     *
     * @test
     * @dataProvider withRequiredFieldsEmptyDataProvider
     * @TestlinkId TL-MAGE-5559
     */
    public function withRequiredFieldsEmpty($emptyField, $messageCount)
    {
        //Data
        $attrData = $this->loadDataSet('CustomerAddressAttribute', 'customer_address_attribute_multipleline',
            array($emptyField => '%noValue%'));
        //Steps
        $this->attributesHelper()->createAttribute($attrData);
        //Verifying
        $xpath = $this->_getControlXpath('field', $emptyField);
        $this->addParameter('fieldXpath', $xpath);
        $this->assertMessagePresent('validation', 'empty_required_field');
        $this->assertTrue($this->verifyMessagesCount($messageCount), $this->getParsedMessages());
    }

    public function withRequiredFieldsEmptyDataProvider()
    {
        return array(
            array('attribute_code', 1),
            array('lines_count', 1),
            array('sort_order', 1),
            array('admin_title', 1)
        );
    }

    /**
     * <p>Checking validation for valid data in the 'Attribute Code' field</p>
     * <p>Steps:</p>
     * <p>1.Click on "Add New Attribute" button</p>
     * <p>2.Choose " Multiple Line" in 'Input Type' dropdown</p>
     * <p>3.Fill 'Attribute Code' field by invalid data [Examples: '0xxx'/'_xxx'/'111x']</p>
     * <p>4.Fill other required fields by regular data.</p>
     * <p>5.Click on "Save Attribute" button</p>
     * <p>Expected result:</p>
     * <p>New attribute ["Multiple Line" type] should not be created.</p>
     * <p>Error JS message: 'Please use only letters (a-z), numbers (0-9) or underscore(_) in
     * this field, first character should be a letter.' is displayed.</p>
     *
     * @param $wrongAttributeCode
     * @param $validationMessage
     *
     * @test
     * @dataProvider withInvalidAttributeCodeDataProvider
     * @depends withRequiredFieldsOnly
     * @TestlinkId TL-MAGE-5560
     */
    public function withInvalidAttributeCode($wrongAttributeCode, $validationMessage)
    {
        //Data
        $attrData = $this->loadDataSet('CustomerAddressAttribute', 'customer_address_attribute_multipleline',
            array('attribute_code' => $wrongAttributeCode));
        //Steps
        $this->attributesHelper()->createAttribute($attrData);
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
     * <p>Checking validation for field  Lines Count</p>
     * <p>Steps:</p>
     * <p>1.Click on "Add New Attribute" button</p>
     * <p>2.Choose "Multiple Line" in 'Input Type' dropdown</p>
     * <p>3.Fill 'Lines Count' field by invalid data [Examples: '0xxx'/'_xxx'/'111x']</p>
     * <p>4.Fill other required fields by regular data.</p>
     * <p>5.Click on "Save Attribute" button</p>
     * <p>Expected result:</p>
     * <p>New attribute ["Multiple Line" type] should not be created.</p>
     * <p>Error JS message: 'The value is not within the specified range'</p>
     *
     * @param $wrongAttributeCode
     * @param $validationMessage
     *
     * @test
     * @dataProvider withInvalidLineCountDataProvider
     * @TestlinkId TL-MAGE-5561
     */
    public function withInvalidLineCount($wrongAttributeCode, $validationMessage)
    {
        //Data
        $attrData = $this->loadDataSet('CustomerAddressAttribute', 'customer_address_attribute_multipleline',
            array('lines_count' => $wrongAttributeCode));
        //Steps
        $this->attributesHelper()->createAttribute($attrData);
        //Verifying
        $this->assertMessagePresent('validation', $validationMessage);
        $this->assertTrue($this->verifyMessagesCount(), $this->getParsedMessages());
    }

    public function withInvalidLineCountDataProvider()
    {
        return array(
            array('1', 'invalid_lines_range'),
            array('line_value', 'invalid_lines_range'),
            array($this->generate('string', 11, ':punct:'), 'invalid_lines_range'),
            array($this->generate('string', 33, ':lower:'), 'invalid_lines_range')
        );
    }

    /**
     * <p>Checking of correct validate of submitting form by using special
     * characters for all fields exclude 'Attribute Code' field.</p>
     * <p>Steps:</p>
     * <p>1.Click on "Add New Attribute" button</p>
     * <p>2.Choose "Multiple Line" in 'Input Type' dropdown</p>
     * <p>3.Fill 'Attribute Code' field by regular data.</p>
     * <p>4.Fill other required fields by special characters.</p>
     * <p>5.Click on "Save Attribute" button</p>
     * <p>Expected result:</p>
     * <p>New attribute ["Multiple Line" type] successfully created.</p>
     * <p>Success message: 'The customer attribute has been saved.' is displayed.</p>
     *
     * @test
     * @depends withRequiredFieldsOnly
     * @TestlinkId TL-MAGE-5562
     */
    public function withSpecialCharactersInTitle()
    {
        //Data
        $attrData = $this->loadDataSet('CustomerAddressAttribute', 'customer_address_attribute_multipleline',
            array('admin_title' => $this->generate('string', 32, ':punct:')));
        $attrData['manage_labels_options']['admin_title'] = preg_replace('/<|>/', '',
            $attrData['manage_labels_options']['admin_title']);
        $searchData = $this->loadDataSet('CustomerAddressAttribute', 'attribute_search_data',
            array('attribute_code' => $attrData['properties']['attribute_code']));
        //Steps
        $this->attributesHelper()->createAttribute($attrData);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_attribute');
        //Steps
        $this->addParameter('elementTitle', $attrData['manage_labels_options']['admin_title']);
        $this->attributesHelper()->openAttribute($searchData);
        //Verifying
        $this->productAttributeHelper()->verifyAttribute($attrData);
    }

    /**
     * <p>Checking of correct work of submitting form by using long values for fields filling</p>
     * <p>Steps:</p>
     * <p>1.Click on "Add New Attribute" button</p>
     * <p>2.Choose "Multiple Line" in 'Input Type' dropdown</p>
     * <p>3.Fill all required fields by long value alpha-numeric data.</p>
     * <p>4.Click on "Save Attribute" button</p>
     * <p>Expected result:</p>
     * <p>New attribute ["Multiple Line" type] successfully created.<p>
     * <p>Success message: 'The customer attribute has been saved.' is displayed.</p>
     *
     * @test
     * @depends withRequiredFieldsOnly
     * @TestlinkId TL-MAGE-5563
     */
    public function withLongValues()
    {
        //Data
        $attrData = $this->loadDataSet('CustomerAddressAttribute', 'customer_address_attribute_multipleline',
            array('attribute_code' => $this->generate('string', 21, ':lower:'),
            'admin_title'    => $this->generate('string', 255, ':alnum:')));
        $searchData = $this->loadDataSet('CustomerAddressAttribute', 'attribute_search_data',
            array('attribute_code'  => $attrData['properties']['attribute_code'],
                  'attribute_label' => $attrData['manage_labels_options']['admin_title']));
        //Steps
        $this->attributesHelper()->createAttribute($attrData);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_attribute');
        //Steps
        $this->addParameter('elementTitle', $attrData['manage_labels_options']['admin_title']);
        $this->attributesHelper()->openAttribute($searchData);
        //Verifying
        $this->productAttributeHelper()->verifyAttribute($attrData);
    }
}