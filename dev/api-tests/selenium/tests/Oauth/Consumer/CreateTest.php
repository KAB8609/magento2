<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    tests
 * @package     selenium
 * @subpackage  tests
 * @author      Magento Core Team <core@magentocommerce.com>
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Test creation new customer from Backend
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Oauth_Consumer_CreateTest extends Mage_Selenium_TestCase
{
    /**
     * Consumer name
     * 
     * @var string
     */
    protected $_consumerToBeDeleted;
    
    /**
     * <p>Log in to Backend.</p>
     */
    public function setUpBeforeTests()
    {
        $this->loginAdminUser();
    }

//    /**
//     * TBD after https://jira.magento.com/browse/APIA-199 is fixed.
//     */
//     protected function tearDown()
//    {
//        if ($this->_consumerToBeDeleted) {
//            $this->navigate('oauth_consumers');
//            $this->oauthHelper()->deleteConsumerByName($this->_consumerToBeDeleted);
//            $this->_consumerToBeDeleted = null;
//        }
//    }
    /**
     * <p>Preconditions:</p>
     * <p>Navigate to  System -> oAuth -> Consumers.</p>
     */
    protected function assertPreConditions()
    {
        $this->navigate('oauth_consumers');
    }

    /**
     * <p>Test navigation.</p>
     * <p>Steps:</p>
     * <p>1. Verify that 'Add New' button is present and click her.</p>
     * <p>2. Verify that the create customer page is opened.</p>
     * <p>3. Verify that 'Back' button is present.</p>
     * <p>4. Verify that 'Save' button is present.</p>
     * <p>5. Verify that 'Save and Continue Edit' button is present.</p>
     * <p>6. Verify that 'Reset' button is present.</p>
     *
     * @test
     */
    public function navigation()
    {
        $this->assertTrue($this->buttonIsPresent('add_new_consumer'), 'There is no "Add New" button on the page');
        $this->clickButton('add_new_consumer');
        $this->assertTrue($this->checkCurrentPage('new_consumer'), $this->getParsedMessages());
        $this->assertTrue($this->buttonIsPresent('back'), 'There is no "Back" button on the page');
        $this->assertTrue($this->buttonIsPresent('save_consumer'), 'There is no "Save" button on the page');
        $this->assertTrue($this->buttonIsPresent('save_and_continue_consumer'), 
            'There is no "Save and Continue Edit" button on the page');
        $this->assertTrue($this->buttonIsPresent('reset'), 'There is no "Reset" button on the page');
    }

    /**
     * <p>Create consumer by filling all fields with valid data</p>
     * <p>Steps:</p>
     * <p>1. Click Add New button.</p>
     * <p>2. Fill in fields with walid data.</p>
     * <p>3. Click 'Save' button.</p>
     * <p>Expected result:</p>
     * <p>Consumer is created.</p>
     * <p>Success Message is displayed</p>
     *
     * @depends navigation
     * @test
     */

    // Failed because https://jira.magento.com/browse/APIA-234
    
    public function withAllValidData()
    {
        //Data
        $consumerData = $this->loadData('generic_consumer');
        //Steps
        $this->oauthHelper()->createConsumer($consumerData);
        //Saving consumer name for tearDown
        $this->_consumerToBeDeleted = $consumerData['consumer_name'];
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_consumer');
        $this->assertTrue($this->checkCurrentPage('oauth_consumers'), $this->getParsedMessages());
    }
    
    /**
     * <p>Ceate consumer with one empty reqired field</p>
     * <p>Steps:</p>
     * <p>1. Click 'Add New' button.</p>
     * <p>2. Fill in fields except Name.</p>
     * <p>3. Click 'Save' button.</p>
     * <p>Expected result:</p>
     * <p>Consumer is not created.</p>
     * <p>Error Message is displayed.</p>
     * 
     * @test
     */
    public function withRequiredFieldEmpty()
    {
        //Steps
        $consumerData = $this->loadData('generic_consumer');
        //Saving consumer name for tearDown
        $this->_consumerToBeDeleted = $consumerData['consumer_name'];
        $consumerData['consumer_name'] = '';
        $this->oauthHelper()->createConsumer($consumerData);
        //Verifying
        $xpath = $this->oauthHelper()->getUIMapFieldXpath('new_consumer', 'consumer_name');
        $this->addParameter('fieldXpath', $xpath);
        $this->assertMessagePresent('error', 'empty_required_field');
        $this->assertTrue($this->checkCurrentPage('new_consumer'), $this->getParsedMessages());
    }
    
    /**
     * <p> Check read-only fields (Key+Secret).</p>
     * <p>Steps:</p>
     * <p>1. Click 'Add New' button.</p>
     * <p>Expected result:</p>
     * <p>Key field was found and this field is disabled.</p>
     * <p>Secret field was found and this field is disabled.</p>
     * 
     * @test
     */
    public function withReadOnlyFields()
    {
       $this->clickButton('add_new_consumer');
       
       $keyXpath = $this->oauthHelper()->getUIMapFieldXpath('new_consumer', 'key');
       $this->assertTrue($this->isElementPresent($keyXpath), 'Key field was not found or this field is not disabled.');
       $keyValue= $this->oauthHelper()->getFieldValue('key');
       $this->assertNotEmpty($keyValue, 'Key field is empty');
       
       $secretXpath = $this->oauthHelper()->getUIMapFieldXpath('new_consumer', 'secret');
       $this->assertTrue($this->isElementPresent($secretXpath), 
       'Secret field was not found or this field is not disabled.');
       $secretValue= $this->oauthHelper()->getFieldValue('secret');
       $this->assertNotEmpty($secretValue, 'Secret field is empty');
    }

    /**
     * <p>Create consumer with invalid value for 'Callback Url' field</p>
     * <p>Steps:</p>
     * <p>1. Click Add New button..</p>
     * <p>2. Fill Name field with "Name" text.</p>
     * <p>3. Fill Callback URL with "invalid url" text.</p>
     * <p>4. Fill Rejected URL with valid text.
     * <p>5. Click 'Save' button.</p>
     * <p>Expected result:</p>
     * <p> Message "Invalid Callback URL 'invalid url'." appears in the top of the page. New Consumer page is opened.</p>
     *
     * @depends withAllValidData
     * @dataProvider withInvalidUrlDataProvider
     * @test
     */
    
    // Failed because https://jira.magento.com/browse/APIA-205
    
    public function withInvalidCallbackURL($wrongUrl)
    {
        //Data
        $consumerData = $this->loadData('generic_consumer', array('callback_url' => $wrongUrl));
        //Steps
        $this->oauthHelper()->createConsumer($consumerData);
        $this->addParameter('callbackUrl', $wrongUrl);
        //Saving consumer name for tearDown
        $this->_consumerToBeDeleted = $consumerData['consumer_name'];
        //Verifying
        $this->assertMessagePresent('error', 'invalid_callback_url');
    }

     /**
     * <p>Create consumer with invalid value for 'Rejected Url' field</p>
     * <p>Steps:</p>
     * <p>1. Click Add New button..</p>
     * <p>2. Fill Name field with "Name" text.</p>
     * <p>3. Fill Rejected URL with "invalid url" text.</p>
     * <p>4. Fill Callback URL with valid text.
     * <p>5. Click 'Save' button.</p>
     * <p>Expected result:</p>
     * <p> Message "Invalid Rejected URL 'invalid url'." appears in the top of the page. New Consumer page is opened.</p>
     *
     * @depends withAllValidData
     * @dataProvider withInvalidUrlDataProvider
     * @test
     */

    // Failed because https://jira.magento.com/browse/APIA-202
    
    public function withInvalidRejectedURL($wrongUrl)
    {
        //Data
        $consumerData = $this->loadData('generic_consumer', array('rejected_callback_url' => $wrongUrl));
        //Steps
        $this->oauthHelper()->createConsumer($consumerData);
        $this->addParameter('rejectedCallbackUrl', $wrongUrl);
        //Saving consumer name for tearDown
        $this->_consumerToBeDeleted = $consumerData['consumer_name'];
        //Verifying
        $this->assertMessagePresent('error', 'invalid_rejected_callback_url');
    }

    public function withInvalidUrlDataProvider()
    {
        return array(
            array('invalid'),
            array('www.localhost.com'),
            array(' ')
        );
    }
}