<?php

/**
 * Mysqi Resource
 * 
 * @package    Mage
 * @module     Core
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Core_Model_Resource_Type_Db_Mysqli extends Mage_Core_Model_Resource_Type_Db
{
    public function getConnection($config)
    {
        $conn = new Varien_Db_Adapter_Mysqli((array)$config);
        
        if (!empty($config->initStatements) && $conn) {
            $conn->query((string)$config->initStatements);
        }

        return $conn;
    }
}