<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Enterprise_Mage_Pbridge_Payment_ProfileTest extends Mage_Selenium_TestCase
{
    /**
     * <p>Create customer and set necessary system configuration options</p>
     *
     * @return array
     * @test
     */
    public function preconditionsForTests()
    {
        $this->loginAdminUser();
        $this->navigate('manage_customers');

        $userData = $this->loadDataSet('Customers', 'generic_customer_account');
        $this->customerHelper()->createCustomer($userData);
        $this->assertMessagePresent('success', 'success_saved_customer');

        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->useHttps('frontend');

        $this->systemConfigurationHelper()->configure('PaymentMethod/authorizenet_pb_enable');
        $this->systemConfigurationHelper()->configure('PaymentMethod/payment_bridge_disable');

        return $userData;
    }

    /**
     * @test
     * @author azavadsky
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-6461
     *
     * @param array $userData
     */
    public function isProfilePageSecure(array $userData)
    {
        $this->customerHelper()->frontLoginCustomer(array('email'    => $userData['email'],
                                                          'password' => $userData['password']));
        $page = 'my_credit_cards';
        $pageUrl = $this->getPageUrl('frontend', $page);
        if (substr($pageUrl, 0, 5) === 'https') {
            $pageUrl = str_replace('https://', 'http://', $pageUrl);
        }
        $this->url($pageUrl);
        $this->validatePage($page);
        $this->assertTrue($this->controlIsPresent('pageelement', 'account_title'));
        $this->assertStringStartsWith('https://', $this->url(), 'Url must be secure');
    }

    /**
     * @test
     * @depends preconditionsForTests
     */
    public function disableConfiguration()
    {
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->useHttps('frontend', 'No');
        $this->systemConfigurationHelper()->configure('PaymentMethod/authorizenet_pb_disable');
        $this->systemConfigurationHelper()->configure('PaymentMethod/payment_bridge_disable');
    }
}