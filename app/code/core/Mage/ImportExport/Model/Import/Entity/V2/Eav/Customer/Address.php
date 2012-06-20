<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_ImportExport
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Import entity customer address model
 *
 * @category    Mage
 * @package     Mage_ImportExport
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_ImportExport_Model_Import_Entity_V2_Eav_Customer_Address
    extends Mage_ImportExport_Model_Import_Entity_V2_Eav_Customer_Abstract
{
    /**#@+
     * Permanent column names
     *
     * Names that begins with underscore is not an attribute.
     * This name convention is for to avoid interference with same attribute name.
     */
    const COLUMN_EMAIL      = '_email';
    const COLUMN_ADDRESS_ID = '_entity_id';
    /**#@-*/

    /**#@+
     * Particular columns that contains of customer default addresses
     */
    const COLUMN_DEFAULT_BILLING  = '_address_default_billing_';
    const COLUMN_DEFAULT_SHIPPING = '_address_default_shipping_';
    /**#@-*/

    /**#@+
     * Error codes
     */
    const ERROR_ADDRESS_ID_IS_EMPTY = 'addressIdIsEmpty';
    const ERROR_CUSTOMER_NOT_FOUND  = 'customerNotFound';
    const ERROR_INVALID_REGION      = 'invalidRegion';
    /**#@-*/

    /**
     * Default addresses column names to appropriate customer attribute code
     *
     * @var array
     */
    protected static $_defaultAddressAttributeMapping = array(
        self::COLUMN_DEFAULT_BILLING  => 'default_billing',
        self::COLUMN_DEFAULT_SHIPPING => 'default_shipping'
    );

    /**
     * Permanent entity columns
     *
     * @var array
     */
    protected $_permanentAttributes = array(self::COLUMN_WEBSITE, self::COLUMN_EMAIL, self::COLUMN_ADDRESS_ID);

    /**
     * Existing addresses
     *
     * [customer ID] => array(
     *     address ID 1,
     *     address ID 2,
     *     ...
     *     address ID N
     * )
     *
     * @var array
     */
    protected $_addresses = array();

    /**
     * Attributes with index (not label) value
     *
     * @var array
     */
    protected $_indexValueAttributes = array('country_id');

    /**
     * Customer entity DB table name
     *
     * @var string
     */
    protected $_entityTable;

    /**
     * Countries and regions
     *
     * array(
     *   [country_id_lowercased_1] => array(
     *     [region_code_lowercased_1]         => region_id_1,
     *     [region_default_name_lowercased_1] => region_id_1,
     *     ...,
     *     [region_code_lowercased_n]         => region_id_n,
     *     [region_default_name_lowercased_n] => region_id_n
     *   ),
     *   ...
     * )
     *
     * @var array
     */
    protected $_countryRegions = array();

    /**
     * Region ID to region default name pairs
     *
     * @var array
     */
    protected $_regions = array();

    /**
     * Column names that holds values with particular meaning
     *
     * @var array
     */
    protected $_particularAttributes = array(
        self::COLUMN_WEBSITE,
        self::COLUMN_EMAIL,
        self::COLUMN_ADDRESS_ID,
        self::COLUMN_DEFAULT_BILLING,
        self::COLUMN_DEFAULT_SHIPPING
    );

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();

        $this->_entityTable = Mage::getModel('Mage_Customer_Model_Address')->getResource()->getEntityTable();

        /** @var $helper Mage_ImportExport_Helper_Data */
        $helper = Mage::helper('Mage_ImportExport_Helper_Data');
        $this->addMessageTemplate(self::ERROR_ADDRESS_ID_IS_EMPTY, $helper->__('Customer address id is not specified'));
        $this->addMessageTemplate(self::ERROR_CUSTOMER_NOT_FOUND,
            $helper->__("Customer with such email and website code doesn't exist")
        );
        $this->addMessageTemplate(self::ERROR_INVALID_REGION, $helper->__('Region is invalid'));

        $this->_initAttributes();
        $this->_initAddresses()
            ->_initCountryRegions();
    }

    /**
     * Initialize existent addresses data
     *
     * @return Mage_ImportExport_Model_Import_Entity_V2_Eav_Customer_Address
     */
    protected function _initAddresses()
    {
        /** @var $address Mage_Customer_Model_Address */
        foreach (Mage::getResourceModel('Mage_Customer_Model_Resource_Address_Collection') as $address) {
            $customerId = $address->getParentId();
            if (!isset($this->_addresses[$customerId])) {
                $this->_addresses[$customerId] = array();
            }
            $addressId = $address->getId();
            if (!in_array($addressId, $this->_addresses[$customerId])) {
                $this->_addresses[$customerId][] = $addressId;
            }
        }

        return $this;
    }

    /**
     * Get region collection
     *
     * @return Mage_Customer_Model_Resource_Customer_Collection
     */
    protected function _getRegionCollection()
    {
        /** @var $collection Mage_Directory_Model_Resource_Region_Collection */
        $collection = Mage::getResourceModel('Mage_Directory_Model_Resource_Region_Collection');
        return $collection;
    }

    /**
     * Initialize country regions hash for clever recognition
     *
     * @return Mage_ImportExport_Model_Import_Entity_V2_Eav_Customer_Address
     */
    protected function _initCountryRegions()
    {
        $collection = $this->_getRegionCollection();
        /** @var $region Mage_Directory_Model_Region */
        foreach ($collection->getItems() as $region) {
            $countryNormalized = strtolower($region->getCountryId());
            $regionCode = strtolower($region->getCode());
            $regionName = strtolower($region->getDefaultName());
            $this->_countryRegions[$countryNormalized][$regionCode] = $region->getId();
            $this->_countryRegions[$countryNormalized][$regionName] = $region->getId();
            $this->_regions[$region->getId()] = $region->getDefaultName();
        }
        return $this;
    }

    /**
     * Import data rows
     *
     * @abstract
     * @return boolean
     */
    protected function _importData()
    {
        /** @var $customer Mage_Customer_Model_Customer */
        $customer       = Mage::getModel('Mage_Customer_Model_Customer');
        $dateTimeFormat = Varien_Date::convertZendToStrftime(Varien_Date::DATETIME_INTERNAL_FORMAT, true, true);
        $resource       = Mage::getModel('Mage_Customer_Model_Address');
        $table          = $resource->getResource()->getEntityTable();
        $nextEntityId   = Mage::getResourceHelper('Mage_ImportExport')->getNextAutoincrement($table);

        $regionColName  = 'region';
        $countryColName = 'country_id';
        /** @var $regionConfig Mage_Eav_Model_Config */
        $regionConfig   = Mage::getSingleton('Mage_Eav_Model_Config');
        /** @var $regionIdAttr Mage_Customer_Model_Attribute */
        $regionIdAttr   = $regionConfig->getAttribute($this->getEntityTypeCode(), 'region_id');
        $regionIdTable  = $regionIdAttr->getBackend()->getTable();
        $regionIdAttrId = $regionIdAttr->getId();

        while ($bunch = $this->_dataSourceModel->getNextBunch()) {
            $entityRows = array();
            $attributes = array();
            $defaults   = array(); // customer default addresses (billing/shipping) data

            foreach ($bunch as $rowNum => $rowData) {
                // check row data
                if (!$this->validateRow($rowData, $rowNum)) {
                    continue;
                }

                $email = strtolower($rowData[self::COLUMN_EMAIL]);
                $websiteId = $this->_websiteCodeToId[$rowData[self::COLUMN_WEBSITE]];
                $customerId = $this->_customers[$email][$websiteId];

                // get address attributes
                $addressAttributes = array();
                foreach ($this->_attributes as $attributeAlias => $attributeParams) {
                    if (isset($rowData[$attributeAlias]) && strlen($rowData[$attributeAlias])) {
                        if ('select' == $attributeParams['type']) {
                            $value = $attributeParams['options'][strtolower($rowData[$attributeAlias])];
                        } elseif ('datetime' == $attributeParams['type']) {
                            $value = gmstrftime($dateTimeFormat, strtotime($rowData[$attributeAlias]));
                        } else {
                            $value = $rowData[$attributeAlias];
                        }
                        $addressAttributes[$attributeParams['id']] = $value;
                    }
                }

                // get address id
                if (isset($this->_addresses[$customerId])
                    && in_array($rowData[self::COLUMN_ADDRESS_ID], $this->_addresses[$customerId])
                ) {
                    $addressId = $rowData[self::COLUMN_ADDRESS_ID];
                } else {
                    $addressId = $nextEntityId++;
                }

                // entity table data
                $entityRows[] = array(
                    'entity_id'      => $addressId,
                    'entity_type_id' => $this->_entityTypeId,
                    'parent_id'      => $customerId,
                    'created_at'     => now(),
                    'updated_at'     => now()
                );

                // attribute values
                foreach ($this->_attributes as $attributeParams) {
                    if (isset($addressAttributes[$attributeParams['id']])) {
                        $attributes[$attributeParams['table']][$addressId][$attributeParams['id']]
                            = $addressAttributes[$attributeParams['id']];
                    }
                }

                // customer default addresses
                foreach (self::getDefaultAddressAttributeMapping() as $colName => $customerAttrCode) {
                    if (!empty($rowData[$colName])) {
                        /** @var $attribute Mage_Eav_Model_Entity_Attribute_Abstract */
                        $attribute = $customer->getAttribute($customerAttrCode);
                        $defaults[$attribute->getBackend()->getTable()][$customerId][$attribute->getId()] = $addressId;
                    }
                }

                // let's try to find region ID
                if (!empty($rowData[$regionColName])) {
                    $countryNormalized = strtolower($rowData[$countryColName]);
                    $regionNormalized  = strtolower($rowData[$regionColName]);

                    if (isset($this->_countryRegions[$countryNormalized][$regionNormalized])) {
                        $regionId = $this->_countryRegions[$countryNormalized][$regionNormalized];
                        $attributes[$regionIdTable][$addressId][$regionIdAttrId] = $regionId;
                        $tbl = $this->_attributes[$regionColName]['table'];
                        $regionColNameId = $this->_attributes[$regionColName]['id'];
                        $attributes[$tbl][$addressId][$regionColNameId] = $this->_regions[$regionId];
                    }
                }
            }

            $this->_saveAddressEntities($entityRows)
                ->_saveAddressAttributes($attributes)
                ->_saveCustomerDefaults($defaults);
        }
        return true;
    }

    /**
     * Update and insert data in entity table
     *
     * @param array $entityRows Rows for insert
     * @return Mage_ImportExport_Model_Import_Entity_V2_Eav_Customer_Address
     */
    protected function _saveAddressEntities(array $entityRows)
    {
        if ($entityRows) {
            $this->_connection->insertOnDuplicate($this->_entityTable, $entityRows, array('updated_at'));
        }
        return $this;
    }

    /**
     * Save customer address attributes
     *
     * @param array $attributesData
     * @return Mage_ImportExport_Model_Import_Entity_V2_Eav_Customer_Address
     */
    protected function _saveAddressAttributes(array $attributesData)
    {
        foreach ($attributesData as $tableName => $data) {
            $tableData = array();
            foreach ($data as $addressId => $attrData) {
                foreach ($attrData as $attributeId => $value) {
                    $tableData[] = array(
                        'entity_id'      => $addressId,
                        'entity_type_id' => $this->_entityTypeId,
                        'attribute_id'   => $attributeId,
                        'value'          => $value
                    );
                }
            }
            $this->_connection->insertOnDuplicate($tableName, $tableData, array('value'));
        }
        return $this;
    }

    /**
     * Save customer default addresses
     *
     * @param array $defaults
     * @return Mage_ImportExport_Model_Import_Entity_V2_Eav_Customer_Address
     */
    protected function _saveCustomerDefaults(array $defaults)
    {
        /** @var $entity Mage_Customer_Model_Customer */
        $entity = Mage::getModel('Mage_Customer_Model_Customer');
        $entityTypeId = $entity->getEntityTypeId();

        foreach ($defaults as $tableName => $data) {
            $tableData = array();
            foreach ($data as $customerId => $attrData) {
                foreach ($attrData as $attributeId => $value) {
                    $tableData[] = array(
                        'entity_id'      => $customerId,
                        'entity_type_id' => $entityTypeId,
                        'attribute_id'   => $attributeId,
                        'value'          => $value
                    );
                }
            }
            $this->_connection->insertOnDuplicate($tableName, $tableData, array('value'));
        }
        return $this;
    }

    /**
     * EAV entity type code getter
     *
     * @abstract
     * @return string
     */
    public function getEntityTypeCode()
    {
        return 'customer_address';
    }

    /**
     * Customer default addresses column name to customer attribute mapping array
     *
     * @static
     * @return array
     */
    public static function getDefaultAddressAttributeMapping()
    {
        return self::$_defaultAddressAttributeMapping;
    }

    /**
     * Validate data row
     *
     * @param array $rowData
     * @param int $rowNumber
     * @return boolean
     */
    public function validateRow(array $rowData, $rowNumber)
    {
        if (isset($this->_validatedRows[$rowNumber])) { // check that row is already validated
            return !isset($this->_invalidRows[$rowNumber]);
        }
        $this->_validatedRows[$rowNumber] = true;
        $this->_processedEntitiesCount++;

        if (empty($rowData[self::COLUMN_ADDRESS_ID])) {
            $this->addRowError(self::ERROR_ADDRESS_ID_IS_EMPTY, $rowNumber);
        } elseif (empty($rowData[self::COLUMN_WEBSITE])) {
            $this->addRowError(self::ERROR_WEBSITE_IS_EMPTY, $rowNumber);
        } elseif (empty($rowData[self::COLUMN_EMAIL])) {
            $this->addRowError(self::ERROR_EMAIL_IS_EMPTY, $rowNumber);
        } else {
            $email   = strtolower($rowData[self::COLUMN_EMAIL]);
            $website = $rowData[self::COLUMN_WEBSITE];

            if (!Zend_Validate::is($email, 'EmailAddress')) {
                $this->addRowError(self::ERROR_INVALID_EMAIL, $rowNumber);
            } elseif (!isset($this->_websiteCodeToId[$website])) {
                $this->addRowError(self::ERROR_INVALID_WEBSITE, $rowNumber);
            } elseif (!$this->_getCustomerId($email, $website)) {
                $this->addRowError(self::ERROR_CUSTOMER_NOT_FOUND, $rowNumber);
            } else {
                // check simple attributes
                foreach ($this->_attributes as $attributeCode => $attributeParams) {
                    if (in_array($attributeCode, $this->_ignoredAttributes)) {
                        continue;
                    }
                    if (isset($rowData[$attributeCode]) && strlen($rowData[$attributeCode])) {
                        $this->isAttributeValid($attributeCode, $attributeParams, $rowData, $rowNumber);
                    } elseif ($attributeParams['is_required']) {
                        $this->addRowError(self::ERROR_VALUE_IS_REQUIRED, $rowNumber, $attributeCode);
                    }
                }

                $countryRegions = isset($this->_countryRegions[strtolower($rowData['country_id'])])
                    ? $this->_countryRegions[strtolower($rowData['country_id'])]
                    : array();

                if (!empty($rowData['region'])
                    && !empty($countryRegions)
                    && !isset($countryRegions[strtolower($rowData['region'])])
                ) {
                    $this->addRowError(self::ERROR_INVALID_REGION, $rowNumber);
                }
            }
        }

        return !isset($this->_invalidRows[$rowNumber]);
    }

    /**
     * Retrieve entity attribute EAV collection
     *
     * @return Mage_Eav_Model_Resource_Attribute_Collection
     */
    protected function _getAttributeCollection()
    {
        /** @var $addressCollection Mage_Customer_Model_Resource_Address_Attribute_Collection */
        $addressCollection = Mage::getResourceModel('Mage_Customer_Model_Resource_Address_Attribute_Collection');
        $addressCollection->addSystemHiddenFilter()
            ->addExcludeHiddenFrontendFilter();
        return $addressCollection;
    }
}
