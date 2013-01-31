<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Api
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * ACL role resource
 *
 * @category    Mage
 * @package     Mage_Api
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Api_Model_Resource_Acl_Role extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Resource initialization
     *
     */
    protected function _construct()
    {
        $this->_init('api_role', 'role_id');
    }

    /**
     * Action before save
     *
     * @param Mage_Core_Model_Abstract $object
     * @return Mage_Api_Model_Resource_Acl_Role
     */
    protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        if (!$object->getId()) {
            $this->setCreated(Mage::getSingleton('Mage_Core_Model_Date')->gmtDate());
        }
        return $this;
    }
}
