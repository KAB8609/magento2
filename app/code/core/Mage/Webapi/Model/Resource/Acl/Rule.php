<?php
/**
 * Resource model for ACL rule
 *
 * @copyright {}
 *
 * @method array getResources() getResources()
 * @method Mage_Webapi_Model_Resource_Acl_Rule setResources() setResources(array $resourcesList)
 * @method int getRoleId() getRoleId()
 * @method Mage_Webapi_Model_Resource_Acl_Rule setRoleId() setRoleId(int $roleId)
 */
class Mage_Webapi_Model_Resource_Acl_Rule extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Resource initialization
     */
    protected function _construct()
    {
        $this->_init('webapi_rule', 'rule_id');
    }

    /**
     * Get all rules from DB
     *
     * @return array
     */
    public function getRuleList()
    {
        $adapter = $this->getReadConnection();
        $select = $adapter->select()->from($this->getMainTable(), array('resource_id', 'role_id'));
        return $adapter->fetchAll($select);
    }

    /**
     * Get resource IDs assigned to role
     *
     * @param integer $roleId Web api user role ID
     * @return array
     */
    public function getResourceIdsByRole($roleId)
    {
        $adapter = $this->getReadConnection();
        $select = $adapter->select()
            ->from($this->getMainTable(), array('resource_id'))
            ->where('role_id = ?', (int)$roleId);
        return $adapter->fetchCol($select);
    }

    /**
     * Save resources
     *
     * @param Mage_Webapi_Model_Acl_Rule $rule
     * @throws Exception
     */
    public function saveResources(Mage_Webapi_Model_Acl_Rule $rule)
    {
        $roleId = $rule->getRoleId();
        if ($roleId > 0) {
            $adapter = $this->_getWriteAdapter();
            $adapter->beginTransaction();

            try {
                $adapter->delete($this->getMainTable(), array('role_id = ?' => (int)$roleId));

                $resources = $rule->getResources();
                if ($resources) {
                    $resourcesToInsert = array();
                    foreach ($resources as $resName) {
                        $resourcesToInsert[] = array(
                            'role_id'       => $roleId,
                            'resource_id'   => trim($resName)
                        );
                    }
                    $adapter->insertArray(
                        $this->getMainTable(),
                        array('role_id', 'resource_id'),
                        $resourcesToInsert
                    );
                }

                $adapter->commit();
            } catch (Exception $e) {
                $adapter->rollBack();
                throw $e;
            }
        }
    }
}
