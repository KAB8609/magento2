<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_ImportExport
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Mage_ImportExport_Model_Import_Entity_V2_Eav_Customer_Address
 */
class Mage_ImportExport_Model_Import_Entity_V2_Eav_Customer_AddressTest extends PHPUnit_Framework_TestCase
{
    /**
     * Tested class name
     *
     * @var string
     */
    protected $_testClassName = 'Mage_ImportExport_Model_Import_Entity_V2_Eav_Customer_Address';

    /**
     * Fixture key from fixture
     *
     * @var string
     */
    protected $_fixtureKey = '_fixture/Mage_ImportExport_Customers_Array';

    /**
     * Address entity adapter instance
     *
     * @var Mage_ImportExport_Model_Import_Entity_V2_Eav_Customer_Address
     */
    protected $_entityAdapter;

    /**
     * Init new instance of address entity adapter
     */
    public function setUp()
    {
        parent::setUp();
        $this->_entityAdapter = Mage::getModel($this->_testClassName);
    }

    /**
     * Unset entity adapter
     */
    public function tearDown()
    {
        unset($this->_entityAdapter);
        parent::tearDown();
    }

    /**
     * Test constructor
     *
     * @magentoDataFixture Mage/ImportExport/_files/customer_with_addresses.php
     */
    public function testConstruct()
    {
        if (Magento_Test_Bootstrap::getInstance()->getDbVendorName() != 'mysql') {
            $this->markTestIncomplete('BUG MAGETWO-1953');
        }

        // check entity table
        $this->assertAttributeInternalType('string', '_entityTable', $this->_entityAdapter,
            'Entity table must be a string.');
        $this->assertAttributeNotEmpty('_entityTable', $this->_entityAdapter, 'Entity table must not be empty');

        // check message templates
        $this->assertAttributeInternalType('array', '_messageTemplates', $this->_entityAdapter,
            'Templates must be an array.');
        $this->assertAttributeNotEmpty('_messageTemplates', $this->_entityAdapter, 'Templates must not be empty');

        // check attributes
        $this->assertAttributeInternalType('array', '_attributes', $this->_entityAdapter,
            'Attributes must be an array.');
        $this->assertAttributeNotEmpty('_attributes', $this->_entityAdapter, 'Attributes must not be empty');

        // check addresses
        $this->assertAttributeInternalType('array', '_addresses', $this->_entityAdapter,
            'Addresses must be an array.');
        $this->assertAttributeNotEmpty('_addresses', $this->_entityAdapter, 'Addresses must not be empty');

        // check country regions and regions
        $this->assertAttributeInternalType('array', '_countryRegions', $this->_entityAdapter,
            'Country regions must be an array.');
        $this->assertAttributeNotEmpty('_countryRegions', $this->_entityAdapter, 'Country regions must not be empty');

        $this->assertAttributeInternalType('array', '_regions', $this->_entityAdapter,
            'Regions must be an array.');
        $this->assertAttributeNotEmpty('_regions', $this->_entityAdapter, 'Regions must not be empty');
    }

    /**
     * Test _initAddresses
     *
     * @magentoDataFixture Mage/ImportExport/_files/customer_with_addresses.php
     * @covers Mage_ImportExport_Model_Import_Entity_V2_Eav_Customer_Address::_initAddresses
     */
    public function testInitAddresses()
    {
        if (Magento_Test_Bootstrap::getInstance()->getDbVendorName() != 'mysql') {
            $this->markTestIncomplete('BUG MAGETWO-1953');
        }

        // get addressed from fixture
        $customers = Mage::registry($this->_fixtureKey);
        $correctAddresses = array();
        /** @var $customer Mage_Customer_Model_Customer */
        foreach ($customers as $customer) {
            $correctAddresses[$customer->getId()] = array();
            /** @var $address Mage_Customer_Model_Address */
            foreach ($customer->getAddressesCollection() as $address) {
                $correctAddresses[$customer->getId()][] = $address->getId();
            }
        }

        // invoke _initAddresses
        $initAddresses = new ReflectionMethod($this->_testClassName, '_initAddresses');
        $initAddresses->setAccessible(true);
        $initAddresses->invoke($this->_entityAdapter);

        // check addresses
        $this->assertAttributeInternalType('array', '_addresses', $this->_entityAdapter,
            'Addresses must be an array.');
        $this->assertAttributeNotEmpty('_addresses', $this->_entityAdapter, 'Addresses must not be empty');

        $addressesReflection = new ReflectionProperty($this->_testClassName, '_addresses');
        $addressesReflection->setAccessible(true);
        $testAddresses = $addressesReflection->getValue($this->_entityAdapter);

        $correctCustomerIds = array_keys($correctAddresses);
        $testCustomerIds = array_keys($testAddresses);
        sort($correctCustomerIds);
        sort($testCustomerIds);
        $this->assertEquals($correctCustomerIds, $testCustomerIds, 'Incorrect customer IDs in addresses array.');

        foreach ($correctCustomerIds as $customerId) {
            $this->assertInternalType('array', $correctAddresses[$customerId], 'Addresses must be an array.');
            $correctAddressIds = $correctAddresses[$customerId];
            $testAddressIds = $testAddresses[$customerId];
            sort($correctAddressIds);
            sort($testAddressIds);
            $this->assertEquals($correctAddressIds, $testAddressIds, 'Incorrect addresses IDs.');
        }
    }

    /**
     * Test _saveAddressEntity
     *
     * @magentoDataFixture Mage/ImportExport/_files/customer_with_addresses.php
     * @covers Mage_ImportExport_Model_Import_Entity_V2_Eav_Customer_Address::_saveAddressEntities
     */
    public function testSaveAddressEntities()
    {
        if (Magento_Test_Bootstrap::getInstance()->getDbVendorName() != 'mysql') {
            $this->markTestIncomplete('BUG MAGETWO-1953');
        }

        // invoke _saveAddressEntities
        list($customerId, $addressId) = $this->_addTestAddress($this->_entityAdapter);

        // check DB
        $testAddress = Mage::getModel('Mage_Customer_Model_Address');
        $testAddress->load($addressId);
        $this->assertEquals($addressId, $testAddress->getId(), 'Incorrect address ID.');
        $this->assertEquals($customerId, $testAddress->getParentId(), 'Incorrect address customer ID.');
    }

    /**
     * Add new test address for existing customer
     *
     * @param Mage_ImportExport_Model_Import_Entity_V2_Eav_Customer_Address $entityAdapter
     * @return array (customerID, addressID)
     */
    protected function _addTestAddress(Mage_ImportExport_Model_Import_Entity_V2_Eav_Customer_Address $entityAdapter)
    {
        $customers = Mage::registry($this->_fixtureKey);
        /** @var $customer Mage_Customer_Model_Customer */
        $customer = reset($customers);
        $customerId = $customer->getId();

        /** @var $addressModel Mage_Customer_Model_Address */
        $addressModel = Mage::getModel('Mage_Customer_Model_Address');
        $tableName    = $addressModel->getResource()->getEntityTable();
        $addressId    = Mage::getResourceHelper('Mage_ImportExport')->getNextAutoincrement($tableName);

        $entityData = array(
            'entity_id'      => $addressId,
            'entity_type_id' => $addressModel->getEntityTypeId(),
            'parent_id'      => $customerId,
            'created_at'     => now(),
            'updated_at'     => now()
        );

        // invoke _saveAddressEntities
        $saveAddressEntities = new ReflectionMethod($this->_testClassName, '_saveAddressEntities');
        $saveAddressEntities->setAccessible(true);
        $saveAddressEntities->invoke($entityAdapter, $entityData);

        return array($customerId, $addressId);
    }

    /**
     * Test _saveAddressAttributes
     *
     * @magentoDataFixture Mage/ImportExport/_files/customer_with_addresses.php
     * @covers Mage_ImportExport_Model_Import_Entity_V2_Eav_Customer_Address::_saveAddressAttributes
     */
    public function testSaveAddressAttributes()
    {
        if (Magento_Test_Bootstrap::getInstance()->getDbVendorName() != 'mysql') {
            $this->markTestIncomplete('BUG MAGETWO-1953');
        }

        // get attributes list
        $attributesReflection = new ReflectionProperty($this->_testClassName, '_attributes');
        $attributesReflection->setAccessible(true);
        $attributes = $attributesReflection->getValue($this->_entityAdapter);

        // get some attribute
        $attributeName = 'city';
        $this->assertArrayHasKey($attributeName, $attributes, 'Key "' . $attributeName . '" should be an attribute.');
        $attributeParams = $attributes[$attributeName];
        $this->assertArrayHasKey('id', $attributeParams, 'Attribute must have an ID.');
        $this->assertArrayHasKey('table', $attributeParams, 'Attribute must have a table.');

        // create new address with attributes
        $data = $this->_addTestAddress($this->_entityAdapter);
        $addressId = $data[1];
        $attributeId = $attributeParams['id'];
        $attributeTable = $attributeParams['table'];
        $attributeValue = 'Test City';

        $attributeArray = array();
        $attributeArray[$attributeTable][$addressId][$attributeId] = $attributeValue;

        // invoke _saveAddressAttributes
        $saveAttributes = new ReflectionMethod($this->_testClassName, '_saveAddressAttributes');
        $saveAttributes->setAccessible(true);
        $saveAttributes->invoke($this->_entityAdapter, $attributeArray);

        // check DB
        /** @var $testAddress Mage_Customer_Model_Address */
        $testAddress = Mage::getModel('Mage_Customer_Model_Address');
        $testAddress->load($addressId);
        $this->assertEquals($addressId, $testAddress->getId(), 'Incorrect address ID.');
        $this->assertEquals($attributeValue, $testAddress->getData($attributeName), 'There is no attribute value.');
    }

    /**
     * Test _saveCustomerDefaults
     *
     * @magentoDataFixture Mage/ImportExport/_files/customer_with_addresses.php
     * @covers Mage_ImportExport_Model_Import_Entity_V2_Eav_Customer_Address::_saveCustomerDefaults
     */
    public function testSaveCustomerDefaults()
    {
        if (Magento_Test_Bootstrap::getInstance()->getDbVendorName() != 'mysql') {
            $this->markTestIncomplete('BUG MAGETWO-1953');
        }

        // get not default address
        $customers = Mage::registry($this->_fixtureKey);
        /** @var $notDefaultAddress Mage_Customer_Model_Address */
        $notDefaultAddress = null;
        /** @var $addressCustomer Mage_Customer_Model_Customer */
        $addressCustomer = null;
        /** @var $customer Mage_Customer_Model_Customer */
        foreach ($customers as $customer) {
            /** @var $address Mage_Customer_Model_Address */
            foreach ($customer->getAddressesCollection() as $address) {
                if (!$customer->getDefaultBillingAddress() && !$customer->getDefaultShippingAddress()) {
                    $notDefaultAddress = $address;
                    $addressCustomer = $customer;
                    break;
                }
                if ($notDefaultAddress) {
                    break;
                }
            }
        }
        $this->assertNotNull($notDefaultAddress, 'Not default address must exists.');
        $this->assertNotNull($addressCustomer, 'Not default address customer must exists.');

        $addressId  = $notDefaultAddress->getId();
        $customerId = $addressCustomer->getId();

        // set customer defaults
        $defaults = array();
        foreach (Mage_ImportExport_Model_Import_Entity_V2_Eav_Customer_Address::getDefaultAddressAttributeMapping()
            as $attributeCode) {
            /** @var $attribute Mage_Eav_Model_Entity_Attribute_Abstract */
            $attribute = $addressCustomer->getAttribute($attributeCode);
            $attributeTable = $attribute->getBackend()->getTable();
            $attributeId = $attribute->getId();
            $defaults[$attributeTable][$customerId][$attributeId] = $addressId;
        }

        // invoke _saveCustomerDefaults
        $saveDefaults = new ReflectionMethod($this->_testClassName, '_saveCustomerDefaults');
        $saveDefaults->setAccessible(true);
        $saveDefaults->invoke($this->_entityAdapter, $defaults);

        // check DB
        /** @var $testCustomer Mage_Customer_Model_Customer */
        $testCustomer = Mage::getModel('Mage_Customer_Model_Customer');
        $testCustomer->load($customerId);
        $this->assertEquals($customerId, $testCustomer->getId(), 'Customer must exists.');
        $this->assertNotNull($testCustomer->getDefaultBillingAddress(), 'Default billing address must exists.');
        $this->assertNotNull($testCustomer->getDefaultShippingAddress(), 'Default shipping address must exists.');
        $this->assertEquals(
            $addressId,
            $testCustomer->getDefaultBillingAddress()->getId(),
            'Incorrect default billing address.'
        );
        $this->assertEquals(
            $addressId,
            $testCustomer->getDefaultShippingAddress()->getId(),
            'Incorrect default shipping address.'
        );
    }

    /**
     * Test attribute collection getter
     *
     * @covers Mage_ImportExport_Model_Import_Entity_V2_Eav_Customer_Address::_getAttributeCollection
     */
    public function testGetAttributeCollection()
    {
        $getCollection = new ReflectionMethod($this->_testClassName, '_getAttributeCollection');
        $getCollection->setAccessible(true);
        $collection = $getCollection->invoke($this->_entityAdapter);
        $this->assertInstanceOf(
            'Mage_Customer_Model_Resource_Address_Attribute_Collection',
            $collection,
            'Incorrect attribute collection class.'
        );
    }

    /**
     * Test import data method with add/update behaviour
     *
     * @magentoDataFixture Mage/ImportExport/_files/customers_for_address_import.php
     * @covers Mage_ImportExport_Model_Import_Entity_V2_Eav_Customer_Address::_importData
     */
    public function testImportDataAddUpdate()
    {
        if (Magento_Test_Bootstrap::getInstance()->getDbVendorName() != 'mysql') {
            $this->markTestIncomplete('BUG MAGETWO-1953');
        }

        // set behaviour
        $parameters = new ReflectionProperty($this->_testClassName, '_parameters');
        $parameters->setAccessible(true);
        $parametersData = $parameters->getValue($this->_entityAdapter);
        $parametersData['behavior'] = Mage_ImportExport_Model_Import::BEHAVIOR_V2_ADD_UPDATE;
        $parameters->setValue($this->_entityAdapter, $parametersData);
        $parameters->setAccessible(false);

        // set fixture CSV file
        $sourceFile = __DIR__ . '/../../_files/address_import_update.csv';
        $result = $this->_entityAdapter
            ->setSource(Mage_ImportExport_Model_Import_Adapter::findAdapterFor($sourceFile))
            ->isDataValid();
        $this->assertFalse($result, 'Validation result must be false.');

        // fixture registry keys
        $fixtureCustomer = '_fixture/Mage_ImportExport_Model_Import_Entity_V2_Eav_Customer_AddressTest_Customer';
        $fixtureCsv      = '_fixture/Mage_ImportExport_Model_Import_Entity_V2_Eav_Customer_AddressTest_Csv_Update';

        // get customer
        /** @var $customer Mage_Customer_Model_Customer */
        $customer = Mage::registry($fixtureCustomer);
        $customerId = $customer->getId();

        // get csv fixture data
        $csvData = Mage::registry($fixtureCsv);

        // import data
        $this->_entityAdapter->importData();

        // form attribute list
        $keyAttribute = 'postcode';
        $requiredAttributes[] = array($keyAttribute);
        foreach (array('update', 'remove') as $action) {
            foreach ($csvData[$action] as $attributes) {
                $requiredAttributes = array_merge($requiredAttributes, array_keys($attributes));
            }
        }

        // get addresses
        /** @var $addressCollection Mage_Customer_Model_Resource_Address_Collection */
        $addressCollection = Mage::getResourceModel('Mage_Customer_Model_Resource_Address_Collection');
        $addressCollection->addAttributeToSelect($requiredAttributes);
        $addresses = array();
        /** @var $address Mage_Customer_Model_Address */
        foreach ($addressCollection as $address) {
            $addresses[$address->getData($keyAttribute)] = $address;
        }

        // is addresses exists
        $this->assertArrayHasKey($csvData['address']['update'], $addresses, 'Address must exist.');
        $this->assertArrayHasKey($csvData['address']['new'], $addresses, 'Address must exist.');
        $this->assertArrayNotHasKey($csvData['address']['no_customer'], $addresses, 'Address must not exist.');
        $this->assertArrayHasKey($csvData['address']['new_no_address_id'], $addresses, 'Address must exist.');

        // is updated address fields have new values
        $updatedAddressId = $csvData['address']['update'];
        /** @var $updatedAddress Mage_Customer_Model_Address */
        $updatedAddress = $addresses[$updatedAddressId];
        $updatedData = $csvData['update'][$updatedAddressId];
        foreach ($updatedData as $fieldName => $fieldValue) {
            $this->assertEquals($fieldValue, $updatedAddress->getData($fieldName));
        }

        // are removed data fields have old values
        $removedData = $csvData['remove'][$updatedAddressId];
        foreach ($removedData as $fieldName => $fieldValue) {
            $this->assertEquals($fieldValue, $updatedAddress->getData($fieldName));
        }

        // are default billing/shipping addresses have new value
        $customer = Mage::getModel('Mage_Customer_Model_Customer');
        $customer->load($customerId);
        $defaultsData = $csvData['default'];
        $this->assertEquals(
            $defaultsData['billing'],
            $customer->getDefaultBillingAddress()->getData($keyAttribute),
            'Incorrect default billing address'
        );
        $this->assertEquals(
            $defaultsData['shipping'],
            $customer->getDefaultShippingAddress()->getData($keyAttribute),
            'Incorrect default shipping address'
        );
    }

    /**
     * Test import data method with delete behaviour
     *
     * @magentoDataFixture Mage/ImportExport/_files/customers_for_address_import.php
     * @covers Mage_ImportExport_Model_Import_Entity_V2_Eav_Customer_Address::_importData
     */
    public function testImportDataDelete()
    {
        if (Magento_Test_Bootstrap::getInstance()->getDbVendorName() != 'mysql') {
            $this->markTestIncomplete('BUG MAGETWO-1953');
        }

        // set behaviour
        $parameters = new ReflectionProperty($this->_testClassName, '_parameters');
        $parameters->setAccessible(true);
        $parametersData = $parameters->getValue($this->_entityAdapter);
        $parametersData['behavior'] = Mage_ImportExport_Model_Import::BEHAVIOR_V2_DELETE;
        $parameters->setValue($this->_entityAdapter, $parametersData);
        $parameters->setAccessible(false);

        // set fixture CSV file
        $sourceFile = __DIR__ . '/../../_files/address_import_delete.csv';
        $result = $this->_entityAdapter
            ->setSource(Mage_ImportExport_Model_Import_Adapter::findAdapterFor($sourceFile))
            ->isDataValid();
        $this->assertTrue($result, 'Validation result must be true.');

        // fixture data
        $fixtureCsv = '_fixture/Mage_ImportExport_Model_Import_Entity_V2_Eav_Customer_AddressTest_Csv_Delete';

        // get csv fixture data
        $csvData = Mage::registry($fixtureCsv);

        // import data
        $this->_entityAdapter->importData();

        // key attribute
        $keyAttribute = 'postcode';

        // get addresses
        /** @var $addressCollection Mage_Customer_Model_Resource_Address_Collection */
        $addressCollection = Mage::getResourceModel('Mage_Customer_Model_Resource_Address_Collection');
        $addressCollection->addAttributeToSelect($keyAttribute);
        $addresses = array();
        /** @var $address Mage_Customer_Model_Address */
        foreach ($addressCollection as $address) {
            $addresses[$address->getData($keyAttribute)] = $address;
        }

        // is addresses exists
        $this->assertArrayNotHasKey($csvData['delete'], $addresses, 'Address must not exist.');
        $this->assertArrayHasKey($csvData['not_delete'], $addresses, 'Address must exist.');
    }
}
