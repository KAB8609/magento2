<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Store group resource model
 *
 * @category    Magento
 * @package     Magento_Core
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Core_Model_Resource_Store_Group extends Magento_Core_Model_Resource_Db_Abstract
{
    /**
     * Define main table
     *
     */
    protected function _construct()
    {
        $this->_init('core_store_group', 'group_id');
    }

    /**
     * Update default store group for website
     *
     * @param Magento_Core_Model_Abstract $model
     * @return Magento_Core_Model_Resource_Store_Group
     */
    protected function _afterSave(Magento_Core_Model_Abstract $model)
    {
        $this->_updateStoreWebsite($model->getId(), $model->getWebsiteId());
        $this->_updateWebsiteDefaultGroup($model->getWebsiteId(), $model->getId());
        $this->_changeWebsite($model);

        return $this;
    }

    /**
     * Update default store group for website
     *
     * @param int $websiteId
     * @param int $groupId
     * @return Magento_Core_Model_Resource_Store_Group
     */
    protected function _updateWebsiteDefaultGroup($websiteId, $groupId)
    {
        $select = $this->_getWriteAdapter()->select()
            ->from($this->getMainTable(), 'COUNT(*)')
            ->where('website_id = :website');
        $count  = $this->_getWriteAdapter()->fetchOne($select, array('website' => $websiteId));

        if ($count == 1) {
            $bind  = array('default_group_id' => $groupId);
            $where = array('website_id = ?' => $websiteId);
            $this->_getWriteAdapter()->update($this->getTable('core_website'), $bind, $where);
        }
        return $this;
    }

    /**
     * Change store group website
     *
     * @param Magento_Core_Model_Abstract $model
     * @return Magento_Core_Model_Resource_Store_Group
     */
    protected function _changeWebsite(Magento_Core_Model_Abstract $model)
    {
        if ($model->getOriginalWebsiteId() && $model->getWebsiteId() != $model->getOriginalWebsiteId()) {
            $select = $this->_getWriteAdapter()->select()
               ->from($this->getTable('core_website'), 'default_group_id')
               ->where('website_id = :website_id');
            $groupId = $this->_getWriteAdapter()->fetchOne($select, array('website_id' => $model->getOriginalWebsiteId()));

            if ($groupId == $model->getId()) {
                $bind  = array('default_group_id' => 0);
                $where = array('website_id = ?' => $model->getOriginalWebsiteId());
                $this->_getWriteAdapter()->update($this->getTable('core_website'), $bind, $where);
            }
        }
        return $this;
    }

    /**
     * Update website for stores that assigned to store group
     *
     * @param int $groupId
     * @param int $websiteId
     * @return Magento_Core_Model_Resource_Store_Group
     */
    protected function _updateStoreWebsite($groupId, $websiteId)
    {
        $bind  = array('website_id' => $websiteId);
        $where = array('group_id = ?' => $groupId);
        $this->_getWriteAdapter()->update($this->getTable('core_store'), $bind, $where);
        return $this;
    }

    /**
     * Save default store for store group
     *
     * @param int $groupId
     * @param int $storeId
     * @return Magento_Core_Model_Resource_Store_Group
     */
    protected function _saveDefaultStore($groupId, $storeId)
    {
        $bind  = array('default_store_id' => $storeId);
        $where = array('group_id = ?' => $groupId);
        $this->_getWriteAdapter()->update($this->getMainTable(), $bind, $where);

        return $this;
    }

    /**
     * Count number of all entities in the system
     *
     * By default won't count admin store
     *
     * @param bool $countAdmin
     * @return int
     */
    public function countAll($countAdmin = false)
    {
        $adapter = $this->_getReadAdapter();
        $select = $adapter->select()->from(array('main' => $this->getMainTable()), 'COUNT(*)');
        if (!$countAdmin) {
            $select->joinLeft(
                array('core_website' => $this->getTable('core_website')),
                'core_website.website_id = main.website_id',
                null
            )
            ->where(sprintf('%s <> %s', $adapter->quoteIdentifier('code'), $adapter->quote('admin')));
        }
        return (int)$adapter->fetchOne($select);
    }
}
