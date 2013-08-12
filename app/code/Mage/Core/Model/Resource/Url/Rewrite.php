<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Url rewrite resource model class
 *
 * @category    Mage
 * @package     Mage_Core
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Core_Model_Resource_Url_Rewrite extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Define main table
     *
     */
    protected function _construct()
    {
        $this->_init('core_url_rewrite', 'url_rewrite_id');
    }

    /**
     * Initialize array fields
     *
     * @return Mage_Core_Model_Resource_Url_Rewrite
     */
    protected function _initUniqueFields()
    {
        $this->_uniqueFields = array(
            array(
                'field' => array('id_path','store_id','is_system'),
                'title' => __('ID Path for Specified Store')
            ),
            array(
                 'field' => array('request_path','store_id'),
                 'title' => __('Request Path for Specified Store'),
            )
        );
        return $this;
    }

    /**
     * Retrieve select object for load object data
     *
     * @param string $field
     * @param mixed $value
     * @param Mage_Core_Model_Url_Rewrite $object
     * @return Zend_Db_Select
     */
    protected function _getLoadSelect($field, $value, $object)
    {
        /** @var $select Magento_DB_Select */
        $select = parent::_getLoadSelect($field, $value, $object);

        if (!is_null($object->getStoreId())) {
            $select->where('store_id IN(?)', array(Mage_Core_Model_AppInterface::ADMIN_STORE_ID, $object->getStoreId()));
            $select->order('store_id ' . Magento_DB_Select::SQL_DESC);
            $select->limit(1);
        }

        return $select;
    }

    /**
     * Retrieve request_path using id_path and current store's id.
     *
     * @param string $idPath
     * @param int|Mage_Core_Model_Store $store
     * @return string|false
     */
    public function getRequestPathByIdPath($idPath, $store)
    {
        if ($store instanceof Mage_Core_Model_Store) {
            $storeId = (int)$store->getId();
        } else {
            $storeId = (int)$store;
        }

        $select = $this->_getReadAdapter()->select();
        /** @var $select Magento_DB_Select */
        $select->from(array('main_table' => $this->getMainTable()), 'request_path')
            ->where('main_table.store_id = :store_id')
            ->where('main_table.id_path = :id_path')
            ->limit(1);

        $bind = array(
            'store_id' => $storeId,
            'id_path'  => $idPath
        );

        return $this->_getReadAdapter()->fetchOne($select, $bind);
    }

    /**
     * Load rewrite information for request
     * If $path is array - we must load all possible records and choose one matching earlier record in array
     *
     * @param   Mage_Core_Model_Url_Rewrite $object
     * @param   array|string $path
     * @return  Mage_Core_Model_Resource_Url_Rewrite
     */
    public function loadByRequestPath(Mage_Core_Model_Url_Rewrite $object, $path)
    {
        if (!is_array($path)) {
            $path = array($path);
        }

        $pathBind = array();
        foreach ($path as $key => $url) {
            $pathBind['path' . $key] = $url;
        }
        // Form select
        $adapter = $this->_getReadAdapter();
        $select  = $adapter->select()
            ->from($this->getMainTable())
            ->where('request_path IN (:' . implode(', :', array_flip($pathBind)) . ')')
            ->where('store_id IN(?)', array(Mage_Core_Model_AppInterface::ADMIN_STORE_ID, (int)$object->getStoreId()));

        $items = $adapter->fetchAll($select, $pathBind);

        // Go through all found records and choose one with lowest penalty - earlier path in array, concrete store
        $mapPenalty = array_flip(array_values($path)); // we got mapping array(path => index), lower index - better
        $currentPenalty = null;
        $foundItem = null;
        foreach ($items as $item) {
            if (!array_key_exists($item['request_path'], $mapPenalty)) {
                continue;
            }
            $penalty = $mapPenalty[$item['request_path']] << 1 + ($item['store_id'] ? 0 : 1);
            if (!$foundItem || $currentPenalty > $penalty) {
                $foundItem = $item;
                $currentPenalty = $penalty;
                if (!$currentPenalty) {
                    break; // Found best matching item with zero penalty, no reason to continue
                }
            }
        }

        // Set data and finish loading
        if ($foundItem) {
            $object->setData($foundItem);
        }

        // Finish
        $this->unserializeFields($object);
        $this->_afterLoad($object);

        return $this;
    }
}
