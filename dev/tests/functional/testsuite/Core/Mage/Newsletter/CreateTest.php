<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Newsletter
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Newsletter Subscription validation
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Core_Mage_Newsletter_CreateTest extends Mage_Selenium_TestCase
{
    protected function assertPreConditions()
    {
        $this->logoutCustomer();
    }

    /**
     * <p>Preconditions</p>
     * <p>Creates Category to use during tests</p>
     *
     * @return string $category
     * @test
     */
    public function preconditionsForTests()
    {
        //Data
        $category = $this->loadDataSet('Category', 'sub_category_required');
        //Steps
        $this->loginAdminUser();
        $this->navigate('manage_categories', false);
        $this->categoryHelper()->checkCategoriesPage();
        $this->categoryHelper()->createCategory($category);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_category');
        $this->reindexInvalidedData();
        $this->flushCache();
        return $category['name'];
    }

    /**
     * <p>With valid email</p>
     *
     * <p> Steps:</p>
     * <p>1. Navigate to Frontend</p>
     * <p>2. Open created category</p>
     * <p>3. Enter a valid email to subscribe</p>
     * <p>4. Click 'Subscribe' button</p>
     * <p>Expected result: Success message is displayed</p>
     * <p>5. Login to backend</p>
     * <p>6. Go to Newsletter -> Newsletter Subscribers</p>
     * <p>7. Verify the email in subscribers list</p>
     * <p>Expected result: The email is present in the subscribers list</p>
     *
     * @param string $category
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-3250
     */
    public function guestUseValidNotExistCustomerEmail($category)
    {
        $search = $this->loadDataSet('Newsletter', 'search_newsletter_subscribers',
                                     array('filter_email' => $this->generate('email', 15, 'valid')));
        //Steps
        $this->categoryHelper()->frontOpenCategory($category);
        $this->newsletterHelper()->frontSubscribe($search['filter_email']);
        //Verifying
        $this->assertMessagePresent('success', 'newsletter_success_subscription');
        //Steps
        $this->loginAdminUser();
        $this->navigate('newsletter_subscribers');
        //Verifying
        $this->assertTrue($this->newsletterHelper()->checkStatus('subscribed', $search),
                          'Incorrect status for ' . $search['filter_email'] . ' email');
    }

    /**
     * <p>With valid email that used for registered customer</p>
     *
     * <p> Steps:</p>
     * <p>1. Navigate to Frontend</p>
     * <p>2. Open created category</p>
     * <p>3. Enter an email to subscribe</p>
     * <p>4. Click 'Subscribe' button</p>
     * <p>Expected result:</p>
     * <p>Error message is displayed</p>
     *
     * @param string $category
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-3239
     */
    public function guestUseValidExistCustomerEmail($category)
    {
        $customer = $this->loadDataSet('Customers', 'generic_customer_account');
        $search = $this->loadDataSet('Newsletter', 'search_newsletter_subscribers',
                                     array('filter_email' => $customer['email']));
        //Steps
        $this->loginAdminUser();
        $this->navigate('manage_customers');
        $this->customerHelper()->createCustomer($customer);
        $this->assertMessagePresent('success', 'success_saved_customer');
        $this->frontend();
        $this->categoryHelper()->frontOpenCategory($category);
        $this->newsletterHelper()->frontSubscribe($search['filter_email']);
        //Verifying
        $this->assertMessagePresent('error', 'newsletter_email_used');
    }

    /**
     * <p>With invalid email</p>
     *
     * <p> Steps:</p>
     * <p>1. Navigate to Frontend</p>
     * <p>2. Open created category</p>
     * <p>3. Enter invalid email to subscribe</p>
     * <p>4. Click 'Subscribe' button</p>
     * <p>Expected result:</p>
     * <p>Error message is displayed</p>
     *
     * @param string $category
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-3249
     */
    public function guestInvalidEmail($category)
    {
        $search = $this->loadDataSet('Newsletter', 'search_newsletter_subscribers',
                                     array('filter_email' => $this->generate('email', 15, 'invalid')));
        //Steps
        $this->categoryHelper()->frontOpenCategory($category);
        $this->newsletterHelper()->frontSubscribe($search['filter_email']);
        //Verifying
        $this->assertMessagePresent('error', 'newsletter_invalid_email');
    }

    /**
     * <p>With empty email field</p>
     *
     * <p> Steps:</p>
     * <p>1. Navigate to Frontend</p>
     * <p>2. Open created category</p>
     * <p>3. Leave email field empty</p>
     * <p>4. Click 'Subscribe' button</p>
     * <p>Expected result:</p>
     * <p>Validation message is displayed</p>
     *
     * @param string $category
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-3248
     */
    public function guestEmptyEmail($category)
    {
        //Steps
        $this->categoryHelper()->frontOpenCategory($category);
        $this->newsletterHelper()->frontSubscribe('');
        //Verifying
        $this->assertMessagePresent('validation', 'newsletter_required_field');
        $this->assertTrue($this->verifyMessagesCount(), $this->getParsedMessages());
    }

    /**
     * <p>With long valid email</p>
     *
     * <p> Steps:</p>
     * <p>1. Navigate to Frontend</p>
     * <p>2. Open created category</p>
     * <p>3. Enter long valid email to subscribe</p>
     * <p>4. Click 'Subscribe' button</p>
     * <p>Expected result:</p>
     * <p>Error message is displayed</p>
     *
     * @param string $category
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-3251
     */
    public function guestLongValidEmail($category)
    {
        //Steps
        $newSubscriberEmail = $this->generate('email', 250, 'valid');
        $this->categoryHelper()->frontOpenCategory($category);
        $this->newsletterHelper()->frontSubscribe($newSubscriberEmail);
        //Verify
        $this->assertMessagePresent('error', 'newsletter_long_email');
    }

    /**
     * subscribe registered customer email.
     *
     * <p> Steps:</p>
     * <p>1. Navigate to Frontend</p>
     * <p>2. Register customer</p>
     * <p>3. Open created category</p>
     * <p>4. Enter customer email to subscribe</p>
     * <p>5. Click 'Subscribe' button</p>
     * <p>Expected result: Success message is displayed</p>
     * <p>6. Login to backend</p>
     * <p>7. Go to Newsletter -> Newsletter Subscribers</p>
     * <p>8. Verify the email in subscribers list</p>
     * <p>Expected result: The email is present in the subscribers list</p>
     *
     * @param string $category
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-5399
     */
    public function customerUseOwnEmail($category)
    {
        $customer = $this->loadDataSet('Customers', 'generic_customer_account');
        $search = $this->loadDataSet('Newsletter', 'search_newsletter_subscribers',
                                     array('filter_email' => $customer['email']));
        //Steps
        $this->loginAdminUser();
        $this->navigate('manage_customers');
        $this->customerHelper()->createCustomer($customer);
        $this->assertMessagePresent('success', 'success_saved_customer');
        $this->customerHelper()->frontLoginCustomer(array('email'    => $customer['email'],
                                                          'password' => $customer['password']));
        $this->categoryHelper()->frontOpenCategory($category);
        $this->newsletterHelper()->frontSubscribe($search['filter_email']);
        //Verifying
        $this->assertMessagePresent('success', 'newsletter_success_subscription');
        //Steps
        $this->loginAdminUser();
        $this->navigate('newsletter_subscribers');
        //Verifying
        $this->assertTrue($this->newsletterHelper()->checkStatus('subscribed', $search),
                          'Incorrect status for ' . $search['filter_email'] . ' email');
    }

    /**
     * <p> Delete Subscriber</p>
     *
     * <p> Steps:</p>
     * <p>1. Navigate to Frontend</p>
     * <p>2. Open created category</p>
     * <p>3. Enter email to subscribe</p>
     * <p>4. Check message</p>
     * <p>5. Login to backend</p>
     * <p>6. Goto Newsletter -> Newsletter Subscribers</p>
     * <p>7. Verify email in subscribers list</p>
     * <p>8. Select subscriber`s email from the list</p>
     * <p>9. Choose "Delete" option in actions</p>
     * <p>10. Click "Submit" button</p>
     * <p>11. Check confirmation message</p>
     * <p>Expected result: Subscriber`s has been removed from the list</p>
     *
     * @param string $category
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-3247
     */
    public function deleteSubscriber($category)
    {
        $search = $this->loadDataSet('Newsletter', 'search_newsletter_subscribers',
                                     array('filter_email' => $this->generate('email', 15, 'valid')));
        //Steps
        $this->categoryHelper()->frontOpenCategory($category);
        $this->newsletterHelper()->frontSubscribe($search['filter_email']);
        //Verifying
        $this->assertMessagePresent('success', 'newsletter_success_subscription');
        //Steps
        $this->loginAdminUser();
        $this->navigate('newsletter_subscribers');
        //Verifying
        $this->assertTrue($this->newsletterHelper()->checkStatus('subscribed', $search),
                          'Incorrect status for ' . $search['filter_email'] . ' email');
        //Steps
        $this->newsletterHelper()->massAction('delete', array($search));
        //Verifying
        $this->assertMessagePresent('success', 'success_delete');
        $this->assertNull($this->search($search), 'Subscriber is not deleted');
    }

    /**
     * <p> Unsubscribe Subscriber</p>
     *
     * <p> Steps:</p>
     * <p>1. Navigate to Frontend</p>
     * <p>2. Open created category</p>
     * <p>3. Enter email to subscribe</p>
     * <p>4. Check message</p>
     * <p>5. Login to backend</p>
     * <p>6. Goto Newsletter -> Newsletter Subscribers</p>
     * <p>7. Verify email in subscribers list</p>
     * <p>8. Select subscriber`s email from the list</p>
     * <p>9. Choose "Unsubscribe" option in actions</p>
     * <p>10. Click "Submit" button</p>
     * <p>11. Check confirmation message</p>
     * <p>Expected result: Subscriber`s email status has changed</p>
     *
     * @param string $category
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-3252
     */
    public function subscriberUnsubscribe($category)
    {
        $search = $this->loadDataSet('Newsletter', 'search_newsletter_subscribers',
                                     array('filter_email' => $this->generate('email', 15, 'valid')));
        //Steps
        $this->categoryHelper()->frontOpenCategory($category);
        $this->newsletterHelper()->frontSubscribe($search['filter_email']);
        //Verifying
        $this->assertMessagePresent('success', 'newsletter_success_subscription');
        //Steps
        $this->loginAdminUser();
        $this->navigate('newsletter_subscribers');
        //Verifying
        $this->assertTrue($this->newsletterHelper()->checkStatus('subscribed', $search),
                          'Incorrect status for ' . $search['filter_email'] . ' email');
        //Steps
        $this->newsletterHelper()->massAction('unsubscribe', array($search));
        //Verifying
        $this->assertMessagePresent('success', 'success_update');
        $this->assertTrue($this->newsletterHelper()->checkStatus('unsubscribed', $search),
                          $this->getParsedMessages());
    }
}