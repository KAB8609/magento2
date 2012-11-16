<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Captcha
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Captcha for Login On Checkout tests
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Core_Mage_Captcha_LoginOnCheckoutTest extends Mage_Selenium_TestCase
{
    public function setUpBeforeTests()
    {
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('Captcha/default_frontend_captcha');
    }

    public function assertPreConditions()
    {
        $this->logoutCustomer();
        $this->loginAdminUser();
    }

    public function tearDownAfterTestClass()
    {
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('Captcha/default_frontend_captcha');
    }

    /**
     * Create customer and product
     * @return array
     * @test
     */
    public function preconditionsForTests()
    {
        //Data
        $simple = $this->loadDataSet('Product', 'simple_product_visible');
        $userData = $this->loadDataSet('Customers', 'generic_customer_account');
        //Steps
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($simple);
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->navigate('manage_customers');
        $this->customerHelper()->createCustomer($userData);
        $this->assertMessagePresent('success', 'success_saved_customer');

        return array('user'   => array('email_address' => $userData['email'], 'password' => $userData['password']),
                     'product'=> $simple['general_name']);
    }

    /**
     * <p>Enable Captcha for Login on Checkout</p>
     * <p>Steps:</p>
     * <p>1.Enable CAPTCHA on frontend option is set to Yes</p>
     * <p>2.Display mode is set to Always</p>
     * <p>3.Forms - "Login" is selected</p>
     * <p>4.Open Frontend</p>
     * <p>5.Add any product to shopping cart</p>
     * <p>6.Proceed to checkout</p>
     * <p>Expected result</p>
     * <p>CAPTCHA image is present</p>
     * <p>"Please type the letters below" field is present</p>
     * <p>Reload Captcha image is present</p>
     *
     * @param array $testData
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-2620
     */
    public function enableCaptcha($testData)
    {
        //Steps
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('Captcha/enable_front_login_captcha');
        $this->frontend();
        $this->productHelper()->frontOpenProduct($testData['product']);
        $this->productHelper()->frontAddProductToCart();
        $this->clickButton('proceed_to_checkout');
        //Verification
        $this->assertTrue($this->controlIsVisible('field', 'captcha_user_login'));
        $this->assertTrue($this->controlIsVisible('pageelement', 'captcha_user_login'));
        $this->assertTrue($this->controlIsVisible('button', 'captcha_reload_user_login'));

        $this->assertFalse($this->controlIsVisible('field', 'captcha_guest_checkout'));
        $this->assertFalse($this->controlIsVisible('pageelement', 'captcha_guest_checkout'));
        $this->assertFalse($this->controlIsVisible('button', 'captcha_reload_guest_checkout'));
        $this->assertFalse($this->controlIsVisible('field', 'captcha_register_during_checkout'));
        $this->assertFalse($this->controlIsVisible('pageelement', 'captcha_register_during_checkout'));
        $this->assertFalse($this->controlIsVisible('button', 'captcha_reload_register_during_checkout'));
    }
    /**
     * <p>Reload Captcha for Login on Checkout</p>
     * <p>Steps:</p>
     * <p>1.Open Frontend</p>
     * <p>2.Add any product to shopping cart</p>
     * <p>3.Proceed to checkout</p>
     * <p>4.Click "Refresh" captcha image</p>
     * <p>Expected result</p>
     * <p>CAPTCHA image is refreshed</p>
     *
     * @param array $testData
     *
     * @test
     * @depends preconditionsForTests
     * @depends enableCaptcha
     * @TestlinkId TL-MAGE-5661
     */
    public function refreshCaptcha($testData)
    {

        //Steps
        $this->frontend();
        $this->productHelper()->frontOpenProduct($testData['product']);
        $this->productHelper()->frontAddProductToCart();
        $this->clickButton('proceed_to_checkout');
        $captchaUrl1 = $this->getControlAttribute('pageelement', 'captcha_user_login', 'src');
        $this->clickControl('button', 'captcha_reload_user_login', false);
        $this->waitForAjax();
        $captchaUrl2 = $this->getControlAttribute('pageelement', 'captcha_user_login', 'src');
        //Verification
        $this->assertNotEquals($captchaUrl1, $captchaUrl2, 'Captcha is not refreshed');
    }

    /**
     * <p>Empty Captcha for Login on Checkout</p>
     * <p>Steps:</p>
     * <p>1.Open Frontend</p>
     * <p>2.Add any product to shopping cart</p>
     * <p>3.Proceed to checkout</p>
     * <p>4.Fill "Login" form and leave empty captcha field</p>
     * <p>6.Click "Login" button </p>
     * <p>Expected result</p>
     * <p>Show validation message "This is a required field."</p>
     *
     * @param array $testData
     *
     * @test
     * @depends preconditionsForTests
     * @depends enableCaptcha
     * @TestlinkId TL-MAGE-5662
     */
    public function emptyCaptcha($testData)
    {
        //Steps
        $this->frontend();
        $this->productHelper()->frontOpenProduct($testData['product']);
        $this->productHelper()->frontAddProductToCart();
        $this->clickButton('proceed_to_checkout');
        $this->fillFieldset($testData['user'],'checkout_method');
        $this->clickButton('login', false);
        //Verification
        $this->assertMessagePresent('validation', 'empty_captcha_user_login');
    }
    /**
     * <p>Wrong Captcha for Login on Checkout</p>
     * <p>Steps:</p>
     * <p>1.Open Frontend</p>
     * <p>2.Add any product to shopping cart</p>
     * <p>3.Proceed to checkout</p>
     * <p>4.Fill Login form with wrong Captcha</p>
     * <p>6.Click "Login" button </p>
     * <p>Expected result</p>
     * <p>Show message "Incorrect CAPTCHA."</p>
     *
     * @param array $testData
     *
     * @test
     * @depends preconditionsForTests
     * @depends enableCaptcha
     * @TestlinkId TL-MAGE-5664
     */
    public function wrongCaptcha($testData)
    {
        //Data
        $testData['user']['captcha_user_login'] = '1234';
        //Steps
        $this->frontend();
        $this->productHelper()->frontOpenProduct($testData['product']);
        $this->productHelper()->frontAddProductToCart();
        $this->clickButton('proceed_to_checkout');
        $this->fillFieldset($testData['user'],'checkout_method');
        $this->clickButton('login');
        //Verification
        $this->assertMessagePresent('error', 'incorrect_captcha');
    }

    /**
     * <p>Correct Captcha for Login on Checkout</p>
     * <p>Steps:</p>
     * <p>1.Open Frontend</p>
     * <p>2.Add any product to shopping cart</p>
     * <p>3.Proceed to checkout</p>
     * <p>4.Fill Login form with Correct Captcha</p>
     * <p>6.Click "Login" button </p>
     * <p>Expected result</p>
     * <p>Billing Information tab is opened</p>
     *
     * @param array $testData
     *
     * @test
     * @depends preconditionsForTests
     * @depends enableCaptcha
     * @TestlinkId TL-MAGE-5663
     */
    public function correctCaptcha($testData)
    {
        //Data
        $testData['user']['captcha_user_login'] = '1111';
        //Steps
        $this->frontend();
        $this->productHelper()->frontOpenProduct($testData['product']);
        $this->productHelper()->frontAddProductToCart();
        $this->clickButton('proceed_to_checkout');
        $this->fillFieldset($testData['user'],'checkout_method');
        $this->clickButton('login');
        //Verification
        $this->checkoutOnePageHelper()->assertOnePageCheckoutTabOpened('billing_information');
    }
}
