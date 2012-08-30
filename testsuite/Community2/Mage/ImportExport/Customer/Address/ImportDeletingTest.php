<?php
/**
 * Magento
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_ImportExport
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Customer Addresses Tests
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Community2_Mage_ImportExport_Deleting_AddressTest extends Mage_Selenium_TestCase
{
    static protected $_customerData = array();

    /**
     * Precondition:
     * Create new customer
     */
    public function setUpBeforeTests()
    {
        $this->loginAdminUser();
        $this->navigate('manage_customers');
        self::$_customerData = $this->loadDataSet('Customers', 'generic_customer_account');
        $this->customerHelper()->createCustomer(self::$_customerData);
        $this->assertMessagePresent('success', 'success_saved_customer');
    }
    /**
     * Preconditions:
     * Log in to Backend.
     */
    protected function assertPreConditions()
    {
        //logged in once for all tests
        $this->loginAdminUser();
    }
    /**
     * Verify that deleting customer address via import works correctly
     * Preconditions: One customer with three addresses created in the system
     * 5 csv files: 1 - full match to customer address data (positive);
     * 2 - only unique key match (positive); 3 - different email (negative);
     * 4 - different website (negative); 5 - different address id (negative)
     * Steps
     * 1. Go to System -> Import/Export -> Import
     * 2. Select Entity Type: Customer Addresses
     * 3. Select Import Behavior: Delete Entities
     * 5. Select file from precondition
     * 6. Click Check Data button
     * 7. Click Import button
     * 8. Go to Customers -> Manage Customers
     * 9. Open customer, check addresses
     * Expected:
     * After step 6: corresponding validation messages are shown
     * After step 9: no address in positive cases; address present in negative cases
     *
     * @test
     * @dataProvider importDeleteAddress
     * @TestlinkId TL-MAGE-5679, 5680
     */
    public function deleteCustomerAddress($addressData, $addressRow, $shouldBeDeleted, $validation)
    {
        //Add address for customer if not present
        $this->navigate('manage_customers');
        $this->addParameter(
            'customer_first_last_name',
            self::$_customerData['first_name'] . ' ' . self::$_customerData['last_name']
        );
        $this->customerHelper()->openCustomer(array('email' => self::$_customerData['email']));
        $this->openTab('addresses');
        if ($this->customerHelper()->isAddressPresent($addressData) == 0) {
            $this->customerHelper()->addAddress($addressData);
            $this->customerHelper()->saveForm('save_customer');
            $this->customerHelper()->openCustomer(array('email' => self::$_customerData['email']));
            $this->openTab('addresses');
        };
        $addressId = $this->customerHelper()->isAddressPresent($addressData);

        //Step 1
        $this->navigate('import');
        //Steps 2-3
        $this->importExportHelper()->chooseImportOptions('Customer Addresses', 'Delete Entities');
        //Steps 4-7
        if ($addressRow[0]['_email'] == '<realEmail>') {
            $addressRow[0]['_email'] = self::$_customerData['email'];
        }
        if ($addressRow[0]['_entity_id'] == '<realAddressId>') {
            $addressRow[0]['_entity_id'] = $addressId;
        }
        $report = $this->importExportHelper()->import($addressRow);
        //Verify import
        $this->assertEquals($validation, $report, 'Import has been finished with issues');
        //Steps 8-9
        $this->navigate('manage_customers');
        $this->customerHelper()->openCustomer(array('email' => self::$_customerData['email']));
        $this->openTab('addresses');
        //Verifying that address was deleted/not deleted by import
        if ($shouldBeDeleted) {
            $this->assertEquals(0, $this->customerHelper()->isAddressPresent($addressData),
                'Address wasn\'t deleted by import');
        } else {
            $this->assertNotEquals(0, $this->customerHelper()->isAddressPresent($addressData),
                'Address was deleted');
        }
    }

    public function importDeleteAddress()
    {
        $basicAddressData = $this->loadDataSet('Customers', 'generic_address', array('first_name' => 'William',
            'middle_name' => 'E.', 'last_name' => 'Holler', 'company' => 'Team Electronics',
            'street_address_line_1' => '3186 Lincoln Street', 'street_address_line_2' => '',
            'city' => 'Camden', 'state' => 'New Jersey', 'zip_code' => '08102', 'telephone' => '609-504-6350',
            'fax' => '609-504-6350',));
        $basicAddressRow = $this->loadDataSet('ImportExport', 'generic_address_csv', array(
            'city' => 'Camden', 'company' => 'Team Electronics', 'fax' => '609-504-6350', 'firstname' => 'William',
            'lastname' => 'Holler', 'middlename' => 'E.', 'postcode' => '08102', 'prefix' => '',
            'region' => 'New Jersey', 'street' => '3186 Lincoln Street', 'telephone' => '609-504-6350',));
        $addressData = array();
        $addressRows = array();
        $streets = array('4040 Hickory Ridge Drive', '3129 Parkway Street', '746 Goodwin Avenue');
        for ($i=0; $i<6; $i++) {
            $addressData[$i] = $basicAddressData;
            $addressRows[$i] = $basicAddressRow;
            $addressRows[$i]['_email'] = '<realEmail>';
            $addressRows[$i]['_entity_id'] = '<realAddressId>';
            if ($i<3) {
                $addressData[$i]['street_address_line_1'] = $streets[$i];
                $addressRows[$i]['street'] = $streets[$i];
            } else {
                $addressData[$i]['street_address_line_1'] = $streets[2];
                $addressRows[$i]['street'] = $streets[2];
            }
        }
        //row 1 matches customer address data
        //row 2: only unique key match
        $addressRows[1]['city'] = 'Volga';
        $addressRows[1]['firstname'] = 'Nicole';
        $addressRows[1]['lastname'] = 'Forrest';
        $addressRows[1]['postcode'] = '57071';
        $addressRows[1]['street'] = '1181 Ryan Road';
        $addressRows[1]['telephone'] = '605-627-7815';
        //row 3: different email
        $addressRows[2]['_email'] = 'fakeemail.test@test.com';
        //row 4: different website
        $addressRows[3]['_website'] = 'admin';
        //row 5: different address id
        $addressRows[4]['_entity_id'] = '10000';
        //row 6: empty address id
        $addressRows[5]['_entity_id'] = '';

        //validation messages
        $successfulImport = array('validation' => array(
                'validation' => array("Checked rows: 1, checked entities: 1, invalid rows: 0, total errors: 0"),
                'success' => array(
                    "File is valid! To start import process press \"Import\" button  Import"
                )),
            'import' => array(
                'success' => array('Import successfully done.')
            ));
        $customerNotFound = array('validation' => array(
                'error' => array("Customer with such email and website code doesn't exist in rows: 1"),
                'validation' => array(
                    "File is totally invalid. Please fix errors and re-upload file",
                    "Checked rows: 1, checked entities: 1, invalid rows: 1, total errors: 1",
                )));
        $addressNotFound = array('validation' => array(
                'error' => array("Customer address for such customer doesn't exist in rows: 1"),
                'validation' => array(
                    "File is totally invalid. Please fix errors and re-upload file",
                    "Checked rows: 1, checked entities: 1, invalid rows: 1, total errors: 1",
                )));
        $emptyEntityId = array('validation' => array(
            'error' => array("Customer address id column is not specified in rows: 1"),
            'validation' => array(
                "File is totally invalid. Please fix errors and re-upload file",
                "Checked rows: 1, checked entities: 1, invalid rows: 1, total errors: 1",
            )));

        return array(
            array($addressData[0], array($addressRows[0]), true, $successfulImport),
            array($addressData[1], array($addressRows[1]), true, $successfulImport),
            array($addressData[2], array($addressRows[2]), false, $customerNotFound),
            array($addressData[3], array($addressRows[3]), false, $customerNotFound),
            array($addressData[4], array($addressRows[4]), false, $addressNotFound),
            array($addressData[5], array($addressRows[5]), false, $emptyEntityId),
            );
    }
}