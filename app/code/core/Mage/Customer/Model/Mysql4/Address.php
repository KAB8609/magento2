<?php
/**
 * Customer address model
 *
 * @package    Ecom
 * @subpackage Customer
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Customer_Model_Mysql4_Address extends Mage_Customer_Model_Address
{
    static protected $_addressTable;
    static protected $_typeTable;
    static protected $_typeLinkTable;

    /**
     * DB read object
     *
     * @var Zend_Db_Adapter_Abstract
     */
    static protected $_read;
    
    /**
     * DB write object
     *
     * @var Zend_Db_Adapter_Abstract
     */
    static protected $_write;
    
    public function __construct($data=array()) 
    {
        parent::__construct($data);
        
        self::$_addressTable    = Mage::registry('resources')->getTableName('customer', 'address');
        self::$_typeTable       = Mage::registry('resources')->getTableName('customer', 'address_type');
        self::$_typeLinkTable   = Mage::registry('resources')->getTableName('customer', 'address_type_link');
        self::$_read    = Mage::registry('resources')->getConnection('customer_read');
        self::$_write   = Mage::registry('resources')->getConnection('customer_write');
    }
    
    /**
     * Get row from database table
     *
     * @param   int $rowId
     * @return  Mage_Customer_Model_Address
     */
    public function load($addressId)
    {
        $select = self::$_read->select()->from(self::$_addressTable)
            ->where(self::$_read->quoteInto('address_id=?', $addressId));
        
        $this->setData(self::$_read->fetchRow($select));
        $this->setType($this->getTypesByAddressId($this->getAddressId()));
        return $this;
    }
    
    public function save($useTransaction=true)
    {
        if ($useTransaction) {
            self::$_write->beginTransaction();
        }        
        
        $this->_prepareSaveData();
        try {
            if ($this->getAddressId()) {
                $condition = self::$_write->quoteInto('address_id=?', $this->getAddressId());
                $result = self::$_write->update(self::$_addressTable, $this->getData(), $condition);
                if ($result) {
                    $this->updateTypes();
                }
            } else {
                self::$_write->insert(self::$_addressTable, $this->getData());
                $this->setAddressId(self::$_write->lastInsertId());
                $this->insertTypes();
            }
            
            if ($useTransaction) {
                self::$_write->commit();
            }
        }
        catch (Exeption $e) {
            if ($useTransaction) {
                self::$_write->rollBack();
            }
            throw $e;
        }
        
        return $this;
    }
    
    protected function _prepareSaveData()
    {
        $data= $this->__toArray(array('customer_id', 'firstname', 'lastname', 'postcode', 'street', 'city', 
            'region', 'region_id', 'country_id', 'company', 'telephone', 'fax'));
        
        if (empty($data['customer_id'])) {
            throw Mage::exception('Mage_Customer')->addMessage(Mage::getModel('customer_model', 'message')->error('CSTE004'));
        }
        
        $this->setData($data);
        
        if (!empty($data['street'])) {
            $this->setStreet($data['street']);
        }
        
        return $this;
    }
    
    /**
     * Delete row from database table
     *
     * @param   Mage_Customer_Model_Address|int $rowId
     */
    public function delete($addressId=null)
    {
        if (is_null($addressId)) {
            $addressId = $this->getAddressId();
        }
        $condition = self::$_write->quoteInto('address_id=?', $addressId);
        $result = self::$_write->delete(self::$_addressTable, $condition);
        $this->deleteTypes($this);
        return $this;
    }

    
    public function getTypesByCondition($condition)
    {
        // fetch all types for address
        $select = self::$_read->select()->from(self::$_typeLinkTable);
        $select->join(self::$_typeTable, 
            self::$_typeTable.".address_type_id=".self::$_typeLinkTable.".address_type_id", 
            self::$_typeTable.".address_type_code");
        $select->where($condition);
        $typesArr = self::$_read->fetchAll($select);
        return $typesArr;
    }
    
    public function getTypesByAddressId($addressId)
    {
        $condition = self::$_read->quoteInto(self::$_typeLinkTable.".address_id=?", $addressId);
        $typesArr = $this->getTypesByCondition($condition);
        
        // process result
        $types = array();
        foreach ($typesArr as $type) {
            $types[$type['address_type_code']] = array('is_primary'=>$type['is_primary']);
        }
        
        return $types;
    }
    
    public function getTypesByCustomerId($customerId)
    {
        $condition = self::$_read->quoteInto(self::$_typeLinkTable.".customer_id=?", $customerId);
        $typesArr = $this->getTypesByCondition($condition);
        
        // process result
        $types = array();
        foreach ($typesArr as $type) {
            $types[$type['address_id']][$type['address_type_code']] = array('is_primary'=>$type['is_primary']);
        }
        
        return $types;
    }
    
    /**
     * Retrieve available address types with their name by language
     * 
     * Use specified field for key
     *
     * @param string $by code|id
     * @param string $langCode en
     * @return array
     */
    public function getAvailableTypes($by='code', $langCode='en')
    {
        $langTable = Mage::registry('resources')->getTableName('customer', 'address_type_language');
        
        $select = self::$_read->select()->from(self::$_typeTable)
            ->join($langTable, "$langTable.address_type_id=".self::$_typeTable.".address_type_id", "$langTable.address_type_name");
            
        $typesArr = self::$_read->fetchAll($select);
        $types = array();
        foreach ($typesArr as $type) {
            $types[$type['address_type_'.$by]] = $type;
        }

        return $types;
    }
    
    /**
     * Address type can be identified by both id and name, choose the appropriate
     *
     * @param integer|string $id
     */
    public function getTypeIdCondition($id)
    {
        if (is_numeric($id)) {
            $condition = self::$_read->quoteInto(self::$_typeTable.".address_type_id=?", $id);
        } else {
            $condition = self::$_read->quoteInto(self::$_typeTable.".address_type_code=?", $id);
        }
    }
    
    public function insertTypes()
    {
        
    }
    
    public function updateTypes()
    {
        
    }
    
    public function deleteTypes()
    {
        
    }
}