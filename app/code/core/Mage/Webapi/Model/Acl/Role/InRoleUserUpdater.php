<?php
/**
 * Users in role grid "In Role User" column with checkbox updater.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Webapi_Model_Acl_Role_InRoleUserUpdater implements Mage_Core_Model_Layout_Argument_UpdaterInterface
{
    /**
     * @var int
     */
    protected $_roleId;

    /**
     * @var Mage_Webapi_Model_Resource_Acl_User
     */
    protected $_userResource;

    /**
     * Constructor.
     *
     * @param Mage_Core_Controller_Request_Http $request
     * @param Mage_Webapi_Model_Resource_Acl_User $userResource
     */
    public function __construct(
        Mage_Core_Controller_Request_Http $request,
        Mage_Webapi_Model_Resource_Acl_User $userResource
    ) {
        $this->_roleId = (int)$request->getParam('role_id');
        $this->_userResource = $userResource;
    }

    /**
     * Init values with users assigned to role.
     *
     * @param array|null $values
     * @return array|null
     */
    public function update($values)
    {
        if ($this->_roleId) {
            $values = $this->_userResource->getRoleUsers($this->_roleId);
        }
        return $values;
    }
}
