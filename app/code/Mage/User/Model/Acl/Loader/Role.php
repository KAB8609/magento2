<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_User
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_User_Model_Acl_Loader_Role implements Magento_Acl_LoaderInterface
{
    /**
     * @var Mage_Core_Model_Resource
     */
    protected $_resource;

    /**
     * @var Mage_User_Model_Acl_Role_GroupFactory
     */
    protected $_groupFactory;

    /**
     * @var Mage_User_Model_Acl_Role_UserFactory
     */
    protected $_roleFactory;

    /**
     * @param Mage_User_Model_Acl_Role_GroupFactory $groupFactory
     * @param Mage_User_Model_Acl_Role_UserFactory $roleFactory
     * @param Mage_Core_Model_Resource $resource
     */
    public function __construct(
        Mage_User_Model_Acl_Role_GroupFactory $groupFactory,
        Mage_User_Model_Acl_Role_UserFactory $roleFactory,
        Mage_Core_Model_Resource $resource
    ) {
        $this->_resource = $resource;
        $this->_groupFactory = $groupFactory;
        $this->_roleFactory = $roleFactory;
    }

    /**
     * Populate ACL with roles from external storage
     *
     * @param Magento_Acl $acl
     */
    public function populateAcl(Magento_Acl $acl)
    {
        $roleTableName = $this->_resource->getTableName('admin_role');
        $adapter = $this->_resource->getConnection('read');

        $select = $adapter->select()
            ->from($roleTableName)
            ->order('tree_level');

        foreach ($adapter->fetchAll($select) as $role) {
            $parent = ($role['parent_id'] > 0) ? Mage_User_Model_Acl_Role_Group::ROLE_TYPE . $role['parent_id'] : null;
            switch ($role['role_type']) {
                case Mage_User_Model_Acl_Role_Group::ROLE_TYPE:
                    $roleId = $role['role_type'] . $role['role_id'];
                    $acl->addRole(
                        $this->_groupFactory->create(array('roleId' => $roleId)),
                        $parent
                    );
                    break;

                case Mage_User_Model_Acl_Role_User::ROLE_TYPE:
                    $roleId = $role['role_type'] . $role['user_id'];
                    if (!$acl->hasRole($roleId)) {
                        $acl->addRole(
                            $this->_roleFactory->create(array('roleId' => $roleId)),
                            $parent
                        );
                    } else {
                        $acl->addRoleParent($roleId, $parent);
                    }
                    break;
            }
        }
    }
}
