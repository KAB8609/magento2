<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Webapi
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Web API Rules Resource Collection
 *
 * @category    Mage
 * @package     Mage_Webpi
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Webapi_Model_Resource_Acl_Rule_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Resource collection initialization
     *
     */
    protected function _construct()
    {
        $this->_init('Mage_Webapi_Model_Acl_Rule', 'Mage_Webapi_Model_Resource_Acl_Rule');
    }

    /**
     * Retrieve rules by role
     *
     * @param int $id
     * @return Mage_Api_Model_Resource_Rules_Collection
     */
    public function getByRoles($id)
    {
        $this->getSelect()->where("role_id = ?", (int)$id);
        return $this;
    }

    /**
     * Add sort by length
     *
     * @return Mage_Api_Model_Resource_Rules_Collection
     */
    public function addSortByLength()
    {
        $this->getSelect()->columns(array('length' => $this->getConnection()->getLengthSql('resource_id')))
            ->order('length DESC');
        return $this;
    }
}
