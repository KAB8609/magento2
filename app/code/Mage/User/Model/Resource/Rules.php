<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_User
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Admin rule resource model
 *
 * @category    Mage
 * @package     Mage_User
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_User_Model_Resource_Rules extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Root ACL resource
     *
     * @var Mage_Core_Model_Acl_RootResource
     */
    protected $_rootResource;

    /**
     * Acl object cache
     *
     * @var Magento_Acl_CacheInterface
     */
    protected $_aclCache;

    /**
     * @param Mage_Core_Model_Resource $resource
     * @param Mage_Core_Model_Acl_RootResource $rootResource
     * @param Magento_Acl_CacheInterface $aclCache
     */
    public function __construct(
        Mage_Core_Model_Resource $resource,
        Mage_Core_Model_Acl_RootResource $rootResource,
        Magento_Acl_CacheInterface $aclCache
    ) {
        parent::__construct($resource);
        $this->_rootResource = $rootResource;
        $this->_aclCache = $aclCache;
    }

    /**
     * Define main table
     *
     */
    protected function _construct()
    {
        $this->_init('admin_rule', 'rule_id');
    }

    /**
     * Save ACL resources
     *
     * @param Mage_User_Model_Rules $rule
     * @throws Mage_Core_Exception
     */
    public function saveRel(Mage_User_Model_Rules $rule)
    {
        try {
            $adapter = $this->_getWriteAdapter();
            $adapter->beginTransaction();
            $roleId = $rule->getRoleId();

            $condition = array(
                'role_id = ?' => (int) $roleId,
            );

            $adapter->delete($this->getMainTable(), $condition);

            $postedResources = $rule->getResources();
            if ($postedResources) {
                $row = array(
                    'role_type'   => 'G',
                    'resource_id' => $this->_rootResource->getId(),
                    'privileges'  => '', // not used yet
                    'role_id'     => $roleId,
                    'permission'  => 'allow'
                );

                // If all was selected save it only and nothing else.
                if ($postedResources === array($this->_rootResource->getId())) {
                    $insertData = $this->_prepareDataForTable(new Varien_Object($row), $this->getMainTable());

                    $adapter->insert($this->getMainTable(), $insertData);
                } else {
                    $acl = Mage::getSingleton('Magento_Acl_Builder')->getAcl();
                    /** @var $resource Magento_Acl_Resource */
                    foreach ($acl->getResources() as $resourceId) {
                        $row['permission'] = in_array($resourceId, $postedResources) ? 'allow' : 'deny';
                        $row['resource_id'] = $resourceId;

                        $insertData = $this->_prepareDataForTable(new Varien_Object($row), $this->getMainTable());
                        $adapter->insert($this->getMainTable(), $insertData);
                    }
                }
            }

            $adapter->commit();
            $this->_aclCache->clean();
        } catch (Mage_Core_Exception $e) {
            $adapter->rollBack();
            throw $e;
        } catch (Exception $e){
            $adapter->rollBack();
            Mage::logException($e);
        }
    }
}
