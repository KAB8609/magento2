<?php
/**
 * Country collection
 *
 * @package    Ecom
 * @subpackage Directory
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Directory_Resource_Model_Mysql4_Country_Collection extends Varien_Data_Collection_Db
{
    public function __construct($useDomainConfig=true) 
    {
        parent::__construct(Mage::getResourceModel('directory')->getReadConnection());
        
        $countryTable = Mage::registry('resources')->getTableName('directory', 'country');
        $countryNameTable = Mage::registry('resources')->getTableName('directory', 'country_name');
        $lang = Mage::registry('website')->getLanguage();
        
        $this->_sqlSelect->from($countryTable);
        $this->_sqlSelect->join($countryNameTable, "$countryNameTable.country_id=$countryTable.country_id AND $countryNameTable.language_code='$lang'");
        
        if ($useDomainConfig) {
            $config = Mage::getConfig()->getCurrentDomain();
            // TODO
        }
        
        $this->setItemObjectClass('Mage_Directory_Country');
    }
}