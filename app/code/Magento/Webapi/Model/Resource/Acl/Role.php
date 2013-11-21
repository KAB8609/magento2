<?php
/**
 * Web API ACL role resource.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Webapi\Model\Resource\Acl;

class Role extends \Magento\Core\Model\Resource\Db\AbstractDb
{
    /**
     * Class constructor.
     *
     * @param \Magento\App\Resource $resource
     */
    public function __construct(\Magento\App\Resource $resource)
    {
        parent::__construct($resource);
    }

    /**
     * Resource initialization.
     */
    protected function _construct()
    {
        $this->_init('webapi_role', 'role_id');
    }

    /**
     * Initialize unique fields.
     *
     * @return \Magento\Webapi\Model\Resource\Acl\Role
     */
    protected function _initUniqueFields()
    {
        $this->_uniqueFields = array(
            array(
                'field' => 'role_name',
                'title' => __('Role Name')
            ),
        );
        return $this;
    }

    /**
     * Get roles list for selects.
     *
     * @return array
     */
    public function getRolesList()
    {
        $adapter = $this->getReadConnection();
        $select = $adapter->select()
            ->from($this->getMainTable(), array($this->getIdFieldName(), 'role_name'))
            ->order('role_name');
        return $adapter->fetchPairs($select);
    }

    /**
     * Get all roles IDs.
     *
     * @return array
     */
    public function getRolesIds()
    {
        $adapter = $this->getReadConnection();
        $select = $adapter->select()->from($this->getMainTable(), array($this->getIdFieldName()));
        return $adapter->fetchCol($select);
    }
}
