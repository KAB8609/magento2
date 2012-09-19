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

/**
 * Helper class
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Core_Mage_Paypal_Helper extends Mage_Selenium_AbstractHelper
{
    public static $monthMap = array('1'  => '01 - January', '2'  => '02 - February', '3'  => '03 - March',
                                    '4'  => '04 - April', '5'  => '05 - May', '6'  => '06 - June', '7'  => '07 - July',
                                    '8'  => '08 - August', '9'  => '09 - September', '10' => '10 - October',
                                    '11' => '11 - November', '12' => '12 - December');

    /**
     * Verify errors after order submitting. Skip tests if error from Paypal
     */
    public function verifyMagentoPayPalErrors()
    {
        $paypalErrors = array('PayPal gateway rejected the request', 'PayPal gateway has rejected request',
                              'Unable to communicate with the PayPal gateway.',
                              'Please verify the card with the issuer bank before placing the order.',
                              'There was an error processing your order. Please contact us or try again later.');
        $submitErrors = $this->getMessagesOnPage('error,validation,verification');
        foreach ($submitErrors as $error) {
            foreach ($paypalErrors as $paypalError) {
                if (strpos($error, $paypalError) !== false) {
                    $this->skipTestWithScreenshot(self::messagesToString($this->getMessagesOnPage()));
                }
            }
        }
    }

    ################################################################################
    #                                                                              #
    #                                   PayPal Developer                           #
    #                                                                              #
    ################################################################################
    /**
     * Validate paypal Page
     *
     * @param string $page
     */
    public function validatePage($page = '')
    {
        if ($page) {
            $this->assertTrue($this->checkCurrentPage($page), $this->getMessagesOnPage());
        } else {
            $page = $this->_findCurrentPageFromUrl();
        }
        //$expectedTitle = $this->getUimapPage($this->getConfigHelper()->getArea(), $page)->getTitle($this->_paramsHelper);
        //$this->assertSame($expectedTitle, $this->getTitle(), 'Title is unexpected for "' . $page . '" page');
        $this->setCurrentPage($page);
    }

    public function waitForNewPage()
    {
        try {
            parent::waitForNewPage();
        } catch (Exception $e) {
            $this->skipTestWithScreenshot($e->getMessage());
        }
    }

    /**
     * Open paypal tab
     *
     * @param string $tabName
     */
    public function openPaypalTab($tabName = '')
    {
        $page = $this->getUimapPage('paypal_developer', 'paypal_developer_logged_in');
        $this->getElement($this->_getControlXpath('tab', $tabName, $page))->click();
        $this->waitForNewPage();
        $result = $this->errorMessage();
        $this->assertFalse($result['success'], $this->getMessagesOnPage());
        $this->validatePage();
    }

    /**
     * Log into Paypal developer's site
     */
    public function paypalDeveloperLogin()
    {
        try {
            $this->goToArea('paypal_developer', 'paypal_developer_home', false);
        } catch (Exception $e) {
            $this->skipTestWithScreenshot($e->getMessage());
        }
        $loginData = array('login_email'     => $this->getConfigHelper()->getDefaultLogin(),
                           'login_password'  => $this->getConfigHelper()->getDefaultPassword());
        $this->validatePage();
        if ($this->controlIsPresent('button', 'button_login')) {
            $this->fillForm($loginData);
            $this->clickButton('button_login', false);
            $this->waitForNewPage();
            $this->waitForElement($this->_getControlXpath('pageelement', 'navigation_menu'));
            $this->validatePage();
        }
        $result = $this->errorMessage();
        $this->assertFalse($result['success'], $this->getMessagesOnPage());
    }

    /**
     * Creates preconfigured Paypal Sandbox account
     *
     * @param string|array $parameters
     *
     * @return array
     */
    public function createPreconfiguredAccount($parameters)
    {
        if (is_string($parameters)) {
            $parameters = $this->loadDataSet('Paypal', $parameters);
        }
        $this->openPaypalTab('test_accounts');
        $this->clickControl('link', 'create_preconfigured_account', false);
        $this->waitForNewPage();
        $this->validatePage();
        $this->fillForm($parameters);
        $this->clickButton('create_account', false);
        $this->waitForNewPage();
        //If get error message after account creation
        $error = $this->errorMessage('failed_account_creation');
        $error1 = $this->successMessage('success_created_account_without_card');
        if ($error['success'] || $error1['success']) {
            $delete = $this->getPaypalSandboxAccountInfo($parameters);
            $this->deleteAccount($delete['email']);
            return $this->createPreconfiguredAccount($parameters);
        }
        $error = $this->errorMessage('incorrect_information');
        if ($error['success']) {
            return $this->createPreconfiguredAccount($parameters);
        }
        $this->assertMessagePresent('success', 'success_created_account');
        $this->validatePage('developer_created_test_account_us');

        return $this->getPaypalSandboxAccountInfo($parameters);
    }

    /**
     * Gets the email for newly created sandbox account
     *
     * @param array $parameters
     *
     * @return array
     */
    public function getPaypalSandboxAccountInfo(array $parameters)
    {
        $this->addParameter('accountEmail', $parameters['login_email']);
        $this->getElement($this->_getControlXpath('link', 'view_details'))->click();
        $elements = $this->getElements($this->_getControlXpath('pageelement', 'account_details_line'), false);
        /**
         * @var PHPUnit_Extensions_Selenium2TestCase_Element $element
         */
        foreach ($elements as $element) {
            $key = $element->element($this->using('xpath')->value('td[1]'))->text();
            $key = preg_replace('/ /', '_', strtolower(trim($key, ':')));
            $value = $element->element($this->using('xpath')->value('td[3]'))->text();
            if ($key == 'credit_card') {
                $cardData = explode(':', $value);
                $number = preg_replace('/\D/', '', $cardData[0]);
                list($expMonth, $expYear) = explode('/', $cardData[1]);
                $data[$key] = array('card_type'        => $parameters['add_credit_card'], 'card_number' => $number,
                                    'expiration_month' => self::$monthMap[trim($expMonth)],
                                    'expiration_year'  => $expYear);
            } else {
                $data[$key] = $value;
            }
        }
        $data['email'] = $this->getControlAttribute('pageelement', 'email_account', 'text');
        return $data;
    }

    /**
     * Gets API Credentials for account
     *
     * @param string $email
     *
     * @return array
     */
    public function getApiCredentials($email)
    {
        $this->addParameter('accountEmail', $email);
        $this->openPaypalTab('api_credentials');
        $apiCredentials = array();
        $elements = $this->getElements($this->_getControlXpath('pageelement', 'account_api_credentials_line'), false);
        /**
         * @var PHPUnit_Extensions_Selenium2TestCase_Element $element
         */
        foreach ($elements as $element) {
            $key = $element->element($this->using('xpath')->value('td[1]'))->text();
            $key = preg_replace('/ /', '_', strtolower(trim($key, ':')));
            $value = $element->element($this->using('xpath')->value('td[2]'))->text();
            if ($key == 'test_account') {
                $apiCredentials['email_associated_with_paypal_merchant_account'] = trim($value);
            } elseif ($key == 'signature') {
                $apiCredentials['api_signature'] = trim($value);
            } else {
                $apiCredentials[$key] = trim($value);
            }
        }
        return $apiCredentials;
    }

    /**
     * Deletes all accounts at PayPal sandbox
     */
    public function deleteAllAccounts()
    {
        $this->openPaypalTab('test_accounts');
        while ($this->controlIsPresent('button', 'delete_account')) {
            $this->clickButtonAndConfirm('delete_account', 'confirmation_to_delete_account', false);
            $this->waitForNewPage();
        }
    }

    /**
     * Deletes account at PayPal sandbox
     *
     * @param string $email
     */
    public function deleteAccount($email)
    {
        $this->addParameter('accountEmail', $email);
        $this->openPaypalTab('test_accounts');
        if ($this->controlIsPresent('checkbox', 'select_account')) {
            $this->fillCheckbox('select_account', 'Yes');
            $this->clickButtonAndConfirm('delete_account', 'confirmation_to_delete_account', false);
            $this->waitForNewPage();
        }
    }

    /**
     * Create Buyers Accounts on PayPal sandbox
     *
     * @param array|string $cards mastercard, visa, discover, amex
     *
     * @return array $accounts
     * @test
     */
    public function createBuyerAccounts($cards)
    {
        if (is_string($cards)) {
            $cards = explode(',', $cards);
            $cards = array_map('trim', $cards);
        }
        $accounts = array();
        foreach ($cards as $card) {
            $info = $this->loadDataSet('Paypal', 'paypal_sandbox_new_buyer_account_' . $card);
            $accounts[$card] = $this->createPreconfiguredAccount($info);
            if ($card != 'amex') {
                $accounts[$card]['credit_card']['card_verification_number'] = '111';
            } else {
                $accounts[$card]['credit_card']['card_verification_number'] = '1234';
            }
        }
        return $accounts;
    }

    ################################################################################
    #                                                                              #
    #                 PayPal Sandbox(@TODO check and rewrite)                      #
    #                                                                              #
    ################################################################################
    /**
     * Login using sandbox account
     * Function has not been verified and is not used right now
     * @TODO check and rewrite
     *
     * @param $parameters
     */
    public function paypalSandboxLogin($parameters)
    {
        if (is_string($parameters)) {
            $elements = explode('/', $parameters);
            $fileName = (count($elements) > 1) ? array_shift($elements) : '';
            $parameters = $this->loadDataSet($fileName, implode('/', $elements));
        }
        if ($this->controlIsPresent('button', 'button_login')) {
            $this->addParameter('elementTitle', $parameters['page_title']);
            $this->validatePage();
            $this->fillForm($parameters['credentials']);
            $this->clickControl('button', 'button_login');
        }
    }

    /**
     * Configure sandbox account
     * Function has not been verified and is not used right now
     * @TODO check and rewrite
     *
     * @param $parameters
     */
    public function paypalSandboxConfigure($parameters)
    {
        if (is_string($parameters)) {
            $elements = explode('/', $parameters);
            $fileName = (count($elements) > 1) ? array_shift($elements) : '';
            $parameters = $this->loadDataSet($fileName, implode('/', $elements));
        }
        $this->addParameter('elementTitle', $parameters['page_title']);
        $this->validatePage();
        $this->fillForm($parameters['credentials']);
        $this->clickControl('button', 'button_login');
        $this->clickControl('button', 'button_agree');
    }

    /**
     * Pays the order using paypal sandbox account
     * Function has not been verified and is not used right now
     * @TODO check and rewrite
     *
     * @param $parameters
     */
    public function paypalPayOrder($parameters)
    {
        if (is_string($parameters)) {
            $elements = explode('/', $parameters);
            $fileName = (count($elements) > 1) ? array_shift($elements) : '';
            $parameters = $this->loadDataSet($fileName, implode('/', $elements));
        }
        if (!$this->controlIsPresent('button', 'button_login')) {
            $this->addParameter('elementTitle', $parameters['page_title_pay_with']);
            $this->validatePage();
            $this->addParameter('elementTitle', $parameters['page_title']);
            $this->clickControl('link', 'have_paypal_account');
        } else {
            $this->addParameter('elementTitle', $parameters['page_title']);
            $this->validatePage();
        }
        $this->fillForm($parameters['credentials']);
        $this->addParameter('elementTitle', $parameters['page_title_review_info']);
        $this->clickControl('button', 'button_login');
        $this->clickControl('button', 'button_continue');
    }
}