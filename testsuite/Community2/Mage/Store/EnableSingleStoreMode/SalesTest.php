<?php
/**
 * Magento
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Store_EnableSingleStoreMode
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Community2_Mage_Store_EnableSingleStoreMode_SalesTest extends Mage_Selenium_TestCase
{
    protected function assertPreConditions()
    {
        $this->loginAdminUser();
    }

    protected function tearDownAfterTestClass()
    {
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('SingleStoreMode/disable_single_store_mode');
    }

    /**
     * Create customer
     * @return array
     * @test
     */
    public function preconditionsForTests()
    {
        //Data
        $userData = $this->loadDataSet('Customers', 'generic_customer_account');
        //Steps
        $this->loginAdminUser();
        $this->admin('manage_stores');
        $this->storeHelper()->deleteStoreViewsExceptSpecified(array('Default Store View'));
        $this->navigate('manage_customers');
        $this->customerHelper()->createCustomer($userData);
        $this->assertMessagePresent('success', 'success_saved_customer');
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('SingleStoreMode/enable_single_store_mode');

        return $userData;
    }

    /**
     * <p>"Please Select a Store" step is present during New Order Creation</p>
     * <p>Steps:</p>
     * <p>1. Login to Backend.</p>
     * <p>2. Navigate to System - Manage Stores.</p>
     * <p>3. If there more one Store View - delete except Default Store View.</p>
     * <p>4. Navigate System Configuration - Single Store Mode.</p>
     * <p>5. Configure Enable Single-Store Mode - Yes.</p>
     * <p>6. Navigate to Orders page.</p>
     * <p>7. Click "Create New Order" button.</p>
     * <p>8. Choose any customer.</p>
     * <p>Expected result:</p>
     * <p>There is no "Please Select a Store" field set on the page</p>
     *
     * @param array $userData
     *
     * @depends preconditionsForTests
     * @test
     * @TestlinkId TL-MAGE-6271
     * @author Nataliya_Kolenko
     */
    public function verificationSelectStoreDuringOrderCreation($userData)
    {
        //Data
        $param = $userData['first_name'] . ' ' . $userData['last_name'];
        $this->addParameter('customer_first_last_name', $param);
        $this->navigate('manage_sales_orders');
        $this->clickButton('create_new_order');
        $this->orderHelper()->searchAndOpen(array('email' => $userData['email']), false, 'order_customer_grid');
        $this->waitForAjax();
        $this->assertFalse($this->controlIsVisible('fieldset', 'order_store_selector'),
            'There is "Please Select a Store" field set on the page');
    }

    /**
     * <p>"Store" column is not displayed on the Recurring Profiles(beta) page</p>
     * <p>Steps:</p>
     * <p>1. Login to Backend.</p>
     * <p>2. Navigate to System - Manage Stores.</p>
     * <p>3. If there more one Store View - delete except Default Store View.</p>
     * <p>4. Navigate System Configuration - Single Store Mode.</p>
     * <p>5. Configure Enable Single-Store Mode - Yes.</p>
     * <p>6. Navigate to Recurring Profiles(beta) page.</p>
     * <p>Expected result:</p>
     * <p>There is no "Store" column the page</p>
     *
     * @test
     * @TestlinkId TL-MAGE-6272
     * @author Nataliya_Kolenko
     */
    public function verificationRecurringProfiles()
    {
        $this->navigate('manage_sales_recurring_profile');
        $this->assertFalse($this->controlIsPresent('dropdown', 'filter_store'),
            'There is "Store" column on the page');
    }

    /**
     * <p>All references to Website-Store-Store View are not displayed in the Terms and Conditions area.</p>
     * <p>Steps:</p>
     * <p>1. Login to Backend.</p>
     * <p>2. Navigate to System - Manage Stores.</p>
     * <p>3. If there more one Store View - delete except Default Store View.</p>
     * <p>4. Navigate System Configuration - Single Store Mode.</p>
     * <p>5. Configure Enable Single-Store Mode - Yes.</p></p>
     * <p>6. Navigate to "Manage Terms and Conditions" page.</p>
     * <p>7. Click "Add New Condition" button".</p>
     * <p>Expected result:</p>
     * <p>There is no "Store View" column on the page.</p>
     * <p>There is no "Store View" multi selector on the page.</p>
     *
     *
     * @test
     * @TestlinkId TL-MAGE-6273
     * @author Nataliya_Kolenko
     */
    public function verificationTermsAndConditions()
    {
        $this->navigate('manage_checkout_terms_and_conditions');
        $this->assertFalse($this->controlIsPresent('dropdown', 'filter_store_view'),
            'There is no "Store View" column on the page');
        $this->assertTrue($this->controlIsPresent('button', 'create_new_terms_and_conditions'),
            'There is no "Add New Condition" button on the page');
        $this->clickButton('create_new_terms_and_conditions');
        $this->assertFalse($this->controlIsPresent('multiselect', 'store_view'),
            'There is "Store View" multi selector on the page');
    }
}