<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_ValidationVatNumber
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Community2_Mage_ValidationVatNumber_AdminOrderCreation_NewCustomerTest extends Mage_Selenium_TestCase
{
    public function setUpBeforeTests()
    {
        //Data
        $storeInfo = $this->loadDataSet('VatID', 'store_information_data');
        //Filling "Store Information" data and Validation VAT Number
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure($storeInfo);
        $xpath = $this->_getControlXpath('link','store_information_link');
        if (!$this->isElementPresent($xpath . "[@class='open']")) {
            $this->clickControl('link','store_information_link', false);
        }
        $this->clickControl('button', 'validate_vat_number', false);
        $this->pleaseWait();
        //Verification
        $this->assertTrue($this->controlIsPresent('button', 'vat_number_is_valid'), 'VAT Number is not valid');
    }

    /**
     * @return string
     * @test
     */
    public function preconditionsForTests()
    {
        //Data
        $names = array(
            'group_valid_vat_domestic'   => 'Valid VAT Domestic_%randomize%',
            'group_valid_vat_intraunion' => 'Valid VAT IntraUnion_%randomize%',
            'group_invalid_vat'          => 'Invalid VAT_%randomize%',
            'group_default'              => 'Default Group_%randomize%');
        $processedGroupNames = array();
        //Creating three Customer Groups
        $this->loginAdminUser();
        $this->navigate('manage_customer_groups');
        foreach ($names as $groupKey => $groupName){
            $customerGroup = $this->loadDataSet('CustomerGroup', 'new_customer_group',
                array('group_name' => $groupName));
            $this->customerGroupsHelper()->createCustomerGroup($customerGroup);
        //Verifying
            $this->assertMessagePresent('success', 'success_saved_customer_group');
            $processedGroupNames[$groupKey] = $customerGroup['group_name'];
        }
        //Configuring "Create New Account Options" tab
        $this->navigate('system_configuration');
        $accountOptions = $this->loadDataSet('VatID', 'create_new_account_options', $processedGroupNames);
        $this->systemConfigurationHelper()->configure($accountOptions);
        //Data for creating product
        $simple = $this->loadDataSet('Product', 'simple_product_visible');
        //Steps
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($simple);
        //Verification
        $this->assertMessagePresent('success', 'success_saved_product');

        return array('sku'            => $simple['general_name'],
                     'customerGroups' => $processedGroupNames);
    }

    protected function tearDownAfterTestClass()
    {
        $accountOptions = $this->loadDataSet('VatID', 'create_new_account_options_disable');
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure($accountOptions);
    }

    /**
     * <p>Creating order from back-end with different VAT Numbers for new customers.</p>
     * <p>Steps:</p>
     * <p>1. Navigate to "Manage Orders" page</p>
     * <p>2. Create order for new customer</p>
     * <p>3. Fill in all required fields</p>
     * (add products, add payment method information, choose shipping method, etc)</p>
     * <p>4. Click button "Validate VAT Number" and confirm popup</p>
     * <p>5. Customer group should be automatically changed, corresponding to settings</p>
     * <p>6. Click "Save" button</p>
     * <p>Expected result:</p>
     * <p>Order is created, no error messages appear</p>
     *
     * @param array $customerAddressData
     * @param string $messageType
     * @param array $testData
     *
     * @test
     * @depends preconditionsForTests
     * @dataProvider creatingOrderForExistingCustomerDataProvider
     *
     * @TestlinkId	TL-MAGE-4873, TL-MAGE-4903, TL-MAGE-4904
     * @author andrey.vergeles
     */
    public function creatingOrderForNewCustomer($customerAddressData, $messageType, $testData)
    {
        //Data
        $orderData = $this->loadDataSet('SalesOrder', 'order_physical',
            array('filter_sku'     => $testData['sku'],
                  'customer_group' => $testData['customerGroups']['group_default'],
                  'customer_email' => $this->generate('email', 32, 'valid')));
        $userAddressData = $orderData['billing_addr_data'] = $this->loadDataSet('SalesOrder',
            'billing_address_' . $customerAddressData);
        //Steps
        $this->navigate('manage_sales_orders');
        $this->validationVatNumberHelper()->createOrder($orderData, $testData, $userAddressData, $messageType);
        //Verification
        $this->assertMessagePresent('success', 'success_created_order');
    }

    public function creatingOrderForExistingCustomerDataProvider()
    {
        return array(
            array('vat_valid_intraunion', 'validIntraunionMessage'),
            array('vat_valid_domestic', 'validDomesticMessage'),
            array('vat_invalid', 'invalidMessage'),
        );
    }
}