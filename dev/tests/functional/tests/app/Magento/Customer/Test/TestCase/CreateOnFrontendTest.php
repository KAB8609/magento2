<?php
/**
 * {license_notice}
 *
 * @category    Mtf
 * @package     Mtf
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Test\TestCase;

use Mtf\Factory\Factory;
use Mtf\TestCase\Functional;
use Magento\Customer\Test\Fixture\Customer;

/**
 * Create Customer on frontend and set default billing address
 *
 * @package Magento\Customer\Test\TestCase;
 */
class CreateOnFrontendTest extends Functional
{
    /**
     * Create Customer account on frontend
     *
     * @ZephyrId MAGETWO-12394
     */
    public function testCreateCustomer()
    {
        $this->markTestSkipped('CICD-776');
        //Data
        $customer = Factory::getFixtureFactory()->getMagentoCustomerCustomer();
        $customer->switchData('customer_US_1');

        //Page
        $homePage = Factory::getPageFactory()->getCmsIndexIndex();
        $createPage = Factory::getPageFactory()->getCustomerAccountCreate();
        $accountIndexPage = Factory::getPageFactory()->getCustomerAccountIndex();
        $addressEditPage = Factory::getPageFactory()->getCustomerAddressEdit();

        //Step 1 Create Account
        $homePage->open();
        $topLinks = $homePage->getTopLinks();
        $topLinks->openLink('register');

        $createPage->getCreateForm()->create($customer);

        //Verifying
        $this->assertContains('Thank you for registering', $accountIndexPage->getMessages()->getSuccessMessages());

        //Check that customer redirected to Dashboard after registration
        $this->assertContains('My Dashboard', $accountIndexPage->getTitleBlock()->getTitle());

        //Step 2 Set Billing Address
        $accountIndexPage->getDashboardAddress()->editBillingAddress();
        $addressEditPage->getEditForm()->editCustomerAddress($customer->getAddressData());

        //Verifying
        $accountIndexPage = Factory::getPageFactory()->getCustomerAccountIndex();
        $this->assertContains('The address has been saved', $accountIndexPage->getMessages()->getSuccessMessages());

        //Verify customer address against previously entered data
        $accountIndexPage->open();
        $accountIndexPage->getDashboardAddress()->editBillingAddress();
        $addressEditPage = Factory::getPageFactory()->getCustomerAddressEdit();
        $this->assertTrue($addressEditPage->getEditForm()->verify($customer->getAddressData()));
    }
}