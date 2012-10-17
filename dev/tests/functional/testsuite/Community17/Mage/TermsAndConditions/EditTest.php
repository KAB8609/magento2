<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_TermsAndConditions
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Delete Terms And Conditions in Backend
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Community17_Mage_TermsAndConditions_EditTest extends Mage_Selenium_TestCase
{

    /**
     * <p>Preconditions:</p>
     * <p>Navigate to Manage Checkout Terms and Conditions</p>
     */
    protected function assertPreConditions() {
        $this->loginAdminUser();
        $this->navigate('manage_checkout_terms_and_conditions');
    }

    /** Standard T&C page
     * 
     * @test
     */
    public function navigationNewTermsAndConditions() {
        $this->assertTrue($this->buttonIsPresent('create_new_terms_and_conditions'), 'There is no "Add New Condition" button on the page');
        $this->clickButton('create_new_terms_and_conditions');
        $this->assertTrue($this->checkCurrentPage('create_condition'), $this->getParsedMessages());
        $this->assertTrue($this->buttonIsPresent('back'), 'There is no "Back" button on the page');
        $this->assertTrue($this->buttonIsPresent('reset'), 'There is no "Reset" button on the page');
        $this->assertTrue($this->buttonIsPresent('save_condition'), 'There is no "Save Condition" button on the page');
    }

    /** Create a T&C example
     * 
     * @test
     */
    public function preconditionsForTestTermsAndConditions() {
        //Data
        $simpleData = $this->loadDataSet('TermsAndConditions', 'generic_terms_default');
        //Steps
        $this->navigate('manage_checkout_terms_and_conditions');
        $this->termsAndConditionsHelper()->createTermsAndConditions($simpleData);
        //Verification
        $this->assertMessagePresent('success', 'condition_saved');

        return $simpleData['condition_name'];
        
    }
            
    /**  
     * @depends preconditionsForTestTermsAndConditions
     * 
     * <p>Steps:</p>
     * <p>1. Open just created T&C;</p>
     * <p>2. Navigate "Condition Name" field;</p>
     * <p>3. Retype "Condition Name" field;</p>
     * <p>4. Press "Save Condition" button;</p>
     * <p>Expected Result: T&C is saved, changes are applied;</p>
     * 
     * @test
     * @TestLinkId	TL-MAGE-2314
     */
    
    public function EditSingleTermsAndConditions($editData) {
        $searchData = $this->loadData('search_terms', array('filter_condition_name' => $editData));
        //Steps
        $this->navigate('manage_checkout_terms_and_conditions');
        $this->searchAndOpen($searchData, true, 'sales_checkout_terms_and_conditions_grid');
        //Steps
        $this->termsAndConditionsHelper()->editTermsAndCondtions($searchData);
        //Verification
        $this->assertMessagePresent('success', 'condition_saved');
    }
    
    /**
     * @depends EditSingleTermsAndConditions
     * 
     * @test
     * @TestLinkId	TL-MAGE-2320
     */
    public function ResetEditSingleTermsAndConditions($resetData) {
        $searchData = $this->loadData('search_terms', array('filter_condition_name' => $resetData));
        //Steps
        $this->navigate('manage_checkout_terms_and_conditions');
        $this->searchAndOpen($searchData, true, 'sales_checkout_terms_and_conditions_grid');
        //Steps
        $this->fillForm('generic_terms_default');
        $this->clickButton('reset');
        $this->clickControl('button', 'save_condition');
        //Verification
        $this->assertMessagePresent('success', 'condition_saved');
    }
    
    
    /**
     *@depends ResetEditSingleTermsAndConditions
     * 
     * @test
     * @TestLinkId	TL-MAGE-2321
     */
    public function BackFromSingleTermsAndConditions($backData) {
        $searchData = $this->loadData('search_terms', array('filter_condition_name' => $backData));
        //Steps
        $this->navigate('manage_checkout_terms_and_conditions');
        $this->searchAndOpen($searchData, true, 'sales_checkout_terms_and_conditions_grid');
        //Steps
        $this->fillForm('generic_terms_default');
        $this->clickButton('back');
        //Verification
        $this->navigate('manage_checkout_terms_and_conditions');
    }
    
    
    
    /** Preconditions for ResetFilterAfterSearch
     * 
     * @test
     */
    public function preconditionsForResetFilterAfterSearch() {
        //Data
        $simpleData = $this->loadDataSet('TermsAndConditions', 'generic_terms_default');
        //Steps
        $this->navigate('manage_checkout_terms_and_conditions');
        $this->termsAndConditionsHelper()->createTermsAndConditions($simpleData);
        //Verification
        $this->assertMessagePresent('success', 'condition_saved');

        return $simpleData['condition_name'];
        
    }
    
    
    
      /**
     * @depends preconditionsForResetFilterAfterSearch
     * 
     * @test
     * 
     */
    public function ResetFilterAfterSearch($backData) {
        $searchData = $this->loadData('search_terms', array('filter_condition_name' => $backData));
        //Steps
        $this->navigate('manage_checkout_terms_and_conditions');
        $this->search($searchData, 'sales_checkout_terms_and_conditions_grid');
        $this->clickButton('search');
        //Steps
        $this->clickButton('reset_filter');
        //Verification
        $this->navigate('manage_checkout_terms_and_conditions');
    }
    
    
    
}
