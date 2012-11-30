<?php
/**
 * Web API User Resource Collection.
 *
 * @copyright {}
 */
class Mage_Webapi_Model_Resource_Acl_User_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Resource collection initialization.
     */
    protected function _construct()
    {
        $this->_init('Mage_Webapi_Model_Acl_User', 'Mage_Webapi_Model_Resource_Acl_User');
    }
}
