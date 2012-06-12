<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Api2
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * API2 global ACL role resource collection model
 *
 * @category    Mage
 * @package     Mage_Api2
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Api2_Model_Resource_Acl_Global_Role_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Initialize collection model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Mage_Api2_Model_Acl_Global_Role', 'Mage_Api2_Model_Resource_Acl_Global_Role');
    }

    /**
     * Add filter by admin user id and join table with appropriate information
     *
     * @param int $id Admin user id
     * @return Mage_Api2_Model_Resource_Acl_Global_Role_Collection
     */
    public function addFilterByAdminId($id)
    {
        $this->getSelect()
            ->joinInner(
                array('api2_acl_user' => $this->getTable('api2_acl_user')),
                'main_table.entity_id = user.role_id',
                array('admin_id' => 'user.admin_id'))
            ->where('user.admin_id = ?', $id, Zend_Db::INT_TYPE);

        return $this;
    }
}
