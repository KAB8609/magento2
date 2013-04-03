<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_WebsiteRestrictions
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Enterprise_Mage_WebsiteRestrictions_WebsitesRestrictionsTest extends Mage_Selenium_TestCase
{
    /**
     * <p>Preconditions:</p>
     * <p>Log in to Backend.</p>
     * <p>Navigate to System -> Configuration </p>
     */
    protected function assertPreConditions()
    {
        $this->loginAdminUser();
        $this->navigate('system_configuration');
    }

    protected function tearDownAfterTestClass()
    {
        //Data
        $config = $this->loadDataSet('WebsiteRestrictions', 'disable_website_restrictions');
        //Steps
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure($config);
        $this->clearInvalidedCache();
        $this->frontend();
    }
    /**
     * <p>Check Configuration Fields</p>
     *
     * @test
     * @TestlinkId TL-MAGE-5519
     */

    public function navigationTest()
    {
        $this->openTab('general_general');
        $this->assertTrue($this->controlIsPresent('dropdown', 'access_restriction'),
            'There is no "access_restriction" dropdown on the page');
        $this->assertTrue($this->controlIsPresent('dropdown', 'restriction_mode'),
            'There is no "restriction_mode" dropdown on the page');
        $this->assertTrue($this->controlIsPresent('dropdown', 'startup_page'),
            'There is no "startup_page" dropdown on the page');
        $this->assertTrue($this->controlIsPresent('dropdown', 'landing_page_restriction'),
            'There is no "landing_page" dropdown on the page');
        $this->assertTrue($this->controlIsPresent('dropdown', 'http_response'),
            'There is no "http_response" dropdown on the page');
    }

    /**
     *
     * <p>Website Closed HTTP Response 200 OK</p>
     *
     * @test
     * @depends navigationTest
     * @TestlinkId TL-MAGE-5520
     */

    public function websiteClosedHttpResponse200()
    {
        //Data
        $config = $this->loadDataSet('WebsiteRestrictions', 'website_closed_response_200');
        //Preconditions
        $this->systemConfigurationHelper()->configure($config);
        $this->clearInvalidedCache();
        //Steps
        $this->frontend('home_page', false);
        $this->websiteRestrictionsHelper()->validateFrontendHttpCode('home_page', '200');
        $this->assertEquals('503 Service Unavailable', $this->title(), "Open wrong page");
    }

    /**
     *
     * <p>Website Closed HTTP Response 503</p>
     *
     * @test
     * @depends navigationTest
     * @TestlinkId TL-MAGE-5521
     */

    public function websiteClosedHttpResponse503()
    {
        //Data
        $config = $this->loadDataSet('WebsiteRestrictions', 'website_closed_response_503');
        //Preconditions
        $this->systemConfigurationHelper()->configure($config);
        $this->clearInvalidedCache();
        //Steps
        $this->frontend('home_page', false);
        $this->websiteRestrictionsHelper()->validateFrontendHttpCode('home_page', '503');
        $this->assertEquals('503 Service Unavailable', $this->title(), "Open wrong page");
    }

    /**
     * <p>Redirect to login form in "Login Only" Mode</p>
     *
     *
     * @test
     * @depends navigationTest
     * @TestlinkId TL-MAGE-5525
     */
    public function redirectToLoginFormInLoginOnlyMode()
    {
        //Data
        $config = $this->loadDataSet('WebsiteRestrictions', 'login_only_to_login_form');
        //Steps
        $this->systemConfigurationHelper()->configure($config);
        $this->clearInvalidedCache();
        $this->frontend('home_page', false);
        //Verification
        $this->validatePage('customer_login');
    }

    /**
     * <p>Redirect to landing page in "Login Only" Mode</p>
     *
     *
     * @test
     * @depends navigationTest
     * @TestlinkId TL-MAGE-5526
     */
    public function redirectToLandingPageInLoginOnlyMode()
    {
        //Data
        $config = $this->loadDataSet('WebsiteRestrictions', 'login_only_to_landing_page');
        //Steps
        $this->systemConfigurationHelper()->configure($config);
        $this->clearInvalidedCache();
        $this->frontend('home_page', false);
        //Verification
        $this->assertEquals('About Us', $this->title(), "Open wrong page ");
    }

    /**
     * <p>Verify that "Forgot Your Password" page is enable in "Login Only" Mode</p>
     *
     *
     * @test
     * @depends navigationTest
     * @TestlinkId TL-MAGE-5527
     */
    public function forgotYourPasswordInLoginOnlyMode()
    {
        //Data
        $config = $this->loadDataSet('WebsiteRestrictions', 'login_only_to_login_form');
        //Steps
        $this->systemConfigurationHelper()->configure($config);
        $this->clearInvalidedCache();
        $this->frontend('home_page', false);
        $this->validatePage('customer_login');
        $this->clickControl('link', 'forgot_password');
        //Verification
        $this->validatePage('forgot_customer_password');
    }

    /**
     * <p>Checkout in "Login Only" Mode</p>
     *
     *
     * @test
     * @depends navigationTest
     * @TestlinkId TL-MAGE-5522
     */
    public function checkoutInRestrictedMode()
    {
        //Data
        $simple = $this->loadDataSet('Product', 'simple_product_visible');
        $userData = $this->loadDataSet('Customers', 'generic_customer_account');
        $config = $this->loadDataSet('WebsiteRestrictions', 'login_only_to_login_form');
        $user = array('email'    => $userData['email'], 'password' => $userData['password']);
        $checkoutData = $this->loadDataSet('OnePageCheckout', 'exist_flatrate_checkmoney_usa',
            array('general_name'  => $simple['general_name'], 'email_address'  => $user['email']));
        //Preconditions
        $this->systemConfigurationHelper()->configure($config);
        $this->clearInvalidedCache();
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($simple);
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->navigate('manage_customers');
        $this->customerHelper()->createCustomer($userData);
        $this->assertMessagePresent('success', 'success_saved_customer');
        //Steps
        $this->frontend('home_page', false);
        $this->validatePage('customer_login');
        $this->fillFieldset($user, 'log_in_customer');
        $this->clickButton('login');
        $this->checkoutOnePageHelper()->frontCreateCheckout($checkoutData);
        //Verification
        $this->assertMessagePresent('success', 'success_checkout');
        //Postcondition
        $this->clickControl('link', 'log_out');

    }

    /**
     * <p>Register customer in "Login and Register" mode </p>
     *
     * @test
     * @depends navigationTest
     * @TestlinkId TL-MAGE-5523
     */
    public function registerCustomerInLoginAndRegisterMode()
    {
        //Data
        $user = $this->loadDataSet('Customers', 'customer_account_register');
        $config = $this->loadDataSet('WebsiteRestrictions', 'login_and_register_to_login_form');
        //Steps
        $this->systemConfigurationHelper()->configure($config);
        $this->clearInvalidedCache();
        $this->frontend('home_page', false);
        $this->validatePage('customer_login');
        $this->customerHelper()->registerCustomer($user);
        //Verifying
        $this->assertMessagePresent('success', 'success_registration');
        $this->validatePage('customer_account');
        //Postcondition
        $this->clickControl('link', 'log_out');

    }

    /**
     * <p>Register customer in "Login Only" mode </p>
     *
     * @test
     * @depends navigationTest
     * @TestlinkId TL-MAGE-5524
     */
    public function registerCustomerInLoginOnlyMode()
    {

        //Data
        $config = $this->loadDataSet('WebsiteRestrictions', 'login_only_to_login_form');
        //Precondition
        $this->systemConfigurationHelper()->configure($config);
        $this->clearInvalidedCache();
        //Steps
        $this->frontend('register_account', false);
        //Verifying
        $this->validatePage('customer_login');
        $this->assertFalse($this->controlIsPresent('button', 'create_account'));
    }
}


