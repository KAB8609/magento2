<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Catalog Event resource model
 */
class Magento_CatalogEvent_Model_Resource_Event extends Magento_Core_Model_Resource_Db_Abstract
{
    const EVENT_FROM_PARENT_FIRST = 1;
    const EVENT_FROM_PARENT_LAST  = 2;

    /**
     * Child to parent list
     *
     * @var array
     */
    protected $_childToParentList;

    /**
     * var which represented catalogevent collection
     *
     * @var array
     */
    protected $_eventCategories;

    /**
     * Store model manager
     *
     * @var Magento_Core_Model_StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Category collection factory
     *
     * @var Magento_Catalog_Model_Resource_Category_CollectionFactory
     */
    protected $_categoryCollectionFactory;

    /**
     * Construct
     *
     * @param Magento_Core_Model_Resource $resource
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @param Magento_Catalog_Model_Resource_Category_CollectionFactory $categoryCollectionFactory
     */
    public function __construct(
        Magento_Core_Model_Resource $resource,
        Magento_Core_Model_StoreManagerInterface $storeManager,
        Magento_Catalog_Model_Resource_Category_CollectionFactory $categoryCollectionFactory
    ) {
        parent::__construct($resource);

        $this->_storeManager = $storeManager;
        $this->_categoryCollectionFactory = $categoryCollectionFactory;
    }

    /**
     * Initialize resource
     *
     */
    protected function _construct()
    {
        $this->_init('magento_catalogevent_event', 'event_id');
        $this->addUniqueField(
            array(
                'field' => 'category_id' , 
                'title' => __('Event for selected category'))
        );
    }

    /**
     * Before model save
     *
     * @param Magento_Core_Model_Abstract $object
     * @return Magento_CatalogEvent_Model_Resource_Event
     */
    protected function _beforeSave(Magento_Core_Model_Abstract $object)
    {
        if (strlen($object->getSortOrder()) === 0) {
            $object->setSortOrder(null);
        }

        return parent::_beforeSave($object);
    }

    /**
     * Retrieve category ids with events
     *
     * @param int|string|Magento_Core_Model_Store $storeId
     * @return array
     */
    public function getCategoryIdsWithEvent($storeId = null)
    {
        $rootCategoryId = $this->_storeManager->getStore($storeId)->getRootCategoryId();

        /* @var $select Magento_DB_Select */
        $select = $this->_categoryCollectionFactory->create()
            ->setStoreId($this->_storeManager->getStore($storeId)->getId())
            ->addIsActiveFilter()
            ->addPathsFilter(Magento_Catalog_Model_Category::TREE_ROOT_ID . '/' . $rootCategoryId)
            ->getSelect();

        $parts = $select->getPart(Zend_Db_Select::FROM);

        if (isset($parts['main_table'])) {
            $categoryCorrelationName = 'main_table';
        } else {
            $categoryCorrelationName = 'e';

        }

        $select->reset(Zend_Db_Select::COLUMNS);
        $select->columns(array('entity_id','level', 'path'), $categoryCorrelationName);

        $select
            ->joinLeft(
                array('event' => $this->getMainTable()),
                'event.category_id = ' . $categoryCorrelationName . '.entity_id',
                'event_id'
            )
            ->order($categoryCorrelationName . '.level ASC');

        $this->_eventCategories = $this->_getReadAdapter()->fetchAssoc($select);

        if (empty($this->_eventCategories)) {
            return array();
        }
        $this->_setChildToParentList();

        foreach ($this->_eventCategories as $categoryId => $category) {
            if ($category['event_id'] === null && isset($category['level']) && $category['level'] > 2) {
                $result[$categoryId] = $this->_getEventFromParent($categoryId, self::EVENT_FROM_PARENT_LAST);
            } else if ($category['event_id'] !== null) {
                $result[$categoryId] = $category['event_id'];
            } else {
                $result[$categoryId] = null;
            }
        }

        return $result;
    }

    /**
     * Method for building relates between child and parent node
     *
     * @return Magento_CatalogEvent_Model_Resource_Event
     */
    protected function _setChildToParentList()
    {
        if (is_array($this->_eventCategories)) {
            foreach ($this->_eventCategories as $row) {
                $category = explode('/', $row['path']);
                $amount = count($category);
                if ($amount > 2) {
                    $key = $category[$amount - 1];
                    $val = $category[$amount - 2];
                    if (empty($this->_childToParentList[$key])) {
                        $this->_childToParentList[$key] = $val;
                    }
                }
            }
        }
        return $this;
    }

    /**
     * Retrieve Event from close parent
     *
     * @param int $categoryId
     * @param int $flag
     * @return int
     */
    protected function _getEventFromParent($categoryId, $flag = 2)
    {
        if (isset($this->_childToParentList[$categoryId])) {
            $parentId = $this->_childToParentList[$categoryId];
        }
        if (!isset($parentId)) {
            return null;
        }
        $eventId = null;
        if (isset($this->_eventCategories[$parentId])) {
            $eventId = $this->_eventCategories[$parentId]['event_id'];
        }
        if ($flag == self::EVENT_FROM_PARENT_LAST) {
            if (isset($eventId) && ($eventId !== null)) {
                return $eventId;
            } else if ($eventId === null) {
                return $this->_getEventFromParent($parentId, $flag);
            }
        }
        return null;
    }

    /**
     * After model save (save event image)
     *
     * @param Magento_Core_Model_Abstract $object
     * @return Magento_CatalogEvent_Model_Resource_Event
     */
    protected function _afterSave(Magento_Core_Model_Abstract $object)
    {
        $where = array(
            $object->getIdFieldName() . '=?' => $object->getId(),
            'store_id = ?' => $object->getStoreId()
        );

        $write = $this->_getWriteAdapter();
        $write->delete($this->getTable('magento_catalogevent_event_image'), $where);

        if ($object->getImage() !== null) {
            $data = array(
                    $object->getIdFieldName() => $object->getId(),
                    'store_id' => $object->getStoreId(),
                    'image'    => $object->getImage()
            );

            $write->insert($this->getTable('magento_catalogevent_event_image'), $data);
        }
        return parent::_afterSave($object);
    }

    /**
     * After model load (loads event image)
     *
     * @param Magento_Core_Model_Abstract $object
     * @return Magento_CatalogEvent_Model_Resource_Event
     */
    protected function _afterLoad(Magento_Core_Model_Abstract $object)
    {
        $adapter = $this->_getReadAdapter();
        $select = $adapter->select()
            ->from($this->getTable('magento_catalogevent_event_image'), array(
                'type' => $adapter->getCheckSql('store_id = 0', "'default'", "'store'"),
                'image'
            ))
            ->where($object->getIdFieldName() . '=?', $object->getId())
            ->where('store_id IN (0, ?)', $object->getStoreId());

        $images = $adapter->fetchPairs($select);

        if (isset($images['store'])) {
            $object->setImage($images['store']);
            $object->setImageDefault(isset($images['default']) ? $images['default'] : '');
        }

        if (isset($images['default']) && !isset($images['store'])) {
            $object->setImage($images['default']);
        }

        return parent::_afterLoad($object);
    }
}
