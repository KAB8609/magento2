<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Enter description here ...
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_Model_Resource_Category_Indexer_Product extends Mage_Index_Model_Resource_Abstract
{
    /**
     * Enter description here ...
     *
     * @var unknown
     */
    protected $_categoryTable;

    /**
     * Enter description here ...
     *
     * @var unknown
     */
    protected $_categoryProductTable;

    /**
     * Enter description here ...
     *
     * @var unknown
     */
    protected $_productWebsiteTable;

    /**
     * Enter description here ...
     *
     * @var unknown
     */
    protected $_storeTable;

    /**
     * Enter description here ...
     *
     * @var unknown
     */
    protected $_groupTable;

    /**
     * Enter description here ...
     *
     */
    protected function _construct()
    {
        $this->_init('catalog/category_product_index', 'category_id');
        $this->_categoryTable = $this->getTable('catalog/category');
        $this->_categoryProductTable = $this->getTable('catalog/category_product');
        $this->_productWebsiteTable = $this->getTable('catalog/product_website');
        $this->_storeTable = $this->getTable('core/store');
        $this->_groupTable = $this->getTable('core/store_group');
    }

    /**
     * Process product save.
     * Method is responsible for index support
     * when product was saved and assigned categories was changed.
     *
     * @param Mage_Index_Model_Event $event
     * @return Mage_Catalog_Model_Resource_Category_Indexer_Product
     */
    public function catalogProductSave(Mage_Index_Model_Event $event)
    {
        $productId = $event->getEntityPk();
        $data = $event->getNewData();

        /**
         * Check if category ids were updated
         */
        if (!isset($data['category_ids'])) {
            return $this;
        }

        /**
         * Select relations to categories
         */
        $select = $this->_getWriteAdapter()->select()
            ->from(array('cp' => $this->_categoryProductTable), 'category_id')
            ->joinInner(array('ce' => $this->_categoryTable), 'ce.entity_id=cp.category_id', 'path')
            ->where('cp.product_id=:product_id');

        /**
         * Get information about product categories
         */
        $categories = $this->_getWriteAdapter()->fetchPairs($select, array('product_id' => $productId));
        $categoryIds = array();
        $allCategoryIds = array();

        foreach ($categories as $id=>$path) {
            $categoryIds[]  = $id;
            $allCategoryIds = array_merge($allCategoryIds, explode('/', $path));
        }
        $allCategoryIds = array_unique($allCategoryIds);
        $allCategoryIds = array_diff($allCategoryIds, $categoryIds);

        /**
         * Delete previous index data
         */
        $this->_getWriteAdapter()->delete(
            $this->getMainTable(),
            array('product_id=?' => $productId)
        );

        $this->_refreshAnchorRelations($allCategoryIds, $productId);
        $this->_refreshDirectRelations($categoryIds, $productId);
        $this->_refreshRootRelations($productId);
        return $this;
    }

    /**
     * Process Catalog Product mass action
     *
     * @param Mage_Index_Model_Event $event
     * @return Mage_Catalog_Model_Resource_Category_Indexer_Product
     */
    public function catalogProductMassAction(Mage_Index_Model_Event $event)
    {
        $data = $event->getNewData();

        /**
         * check is product ids were updated
         */
        if (!isset($data['product_ids'])) {
            return $this;
        }
        $productIds     = $data['product_ids'];
        $categoryIds    = array();
        $allCategoryIds = array();

        /**
         * Select relations to categories
         */
        $adapter = $this->_getWriteAdapter();
        $select  = $adapter->select()
            ->distinct(true)
            ->from(array('cp' => $this->_categoryProductTable), array('category_id'))
            ->join(
                array('ce' => $this->_categoryTable),
                'ce.entity_id=cp.category_id',
                array('path'))
            ->where('cp.product_id IN(?)', $productIds);
        $pairs   = $adapter->fetchPairs($select);
        foreach ($pairs as $categoryId => $categoryPath) {
            $categoryIds[] = $categoryId;
            $allCategoryIds = array_merge($allCategoryIds, explode('/', $categoryPath));
        }

        $allCategoryIds = array_unique($allCategoryIds);
        $allCategoryIds = array_diff($allCategoryIds, $categoryIds);

        /**
         * Delete previous index data
         */
        $this->_getWriteAdapter()->delete(
            $this->getMainTable(), array('product_id IN(?)' => $productIds)
        );

        $this->_refreshAnchorRelations($allCategoryIds, $productIds);
        $this->_refreshDirectRelations($categoryIds, $productIds);
        $this->_refreshRootRelations($productIds);
        return $this;
    }

    /**
     * Process category index after category save
     *
     * @param Mage_Index_Model_Event $event
     */
    public function catalogCategorySave(Mage_Index_Model_Event $event)
    {
        $data = $event->getNewData();

        /**
         * Check if we have reindex category move results
         */
        if (isset($data['affected_category_ids'])) {
            $categoryIds = $event->getNewData('affected_category_ids');
        } else if (isset($data['products_was_changed'])) {
            $categoryIds = array($event->getEntityPk());
        } else {
            return;
        }

        $select = $this->_getWriteAdapter()->select()
            ->from($this->_categoryTable, 'path')
            ->where('entity_id IN (?)', $categoryIds);
        $paths = $this->_getWriteAdapter()->fetchCol($select);
        $allCategoryIds = array();
        foreach ($paths as $path) {
            $allCategoryIds = array_merge($allCategoryIds, explode('/', $path));
        }
        $allCategoryIds = array_unique($allCategoryIds);

        /**
         * retrieve anchor category id
         */
        $anchorInfo = $this->_getAnchorAttributeInfo();
        $bind = array(
            'attribute_id' => $anchorInfo['id'],
            'store_id'     => 0,
            'e_value'      => 1
        );
        $select = $this->_getReadAdapter()->select()
            ->distinct(true)
            ->from(array('ce' => $this->_categoryTable), array('entity_id'))
            ->joinInner(
                array('dca'=>$anchorInfo['table']),
                "dca.entity_id=ce.entity_id AND dca.attribute_id=:attribute_id AND dca.store_id=:store_id",
                array())
             ->where('dca.value=:e_value')
             ->where('ce.entity_id IN (?)', $allCategoryIds);
        $anchorIds = $this->_getWriteAdapter()->fetchCol($select, $bind);
        /**
         * delete only anchor id and category ids
         */
        $deleteCategoryIds = array_merge($anchorIds,$categoryIds);

        $this->_getWriteAdapter()->delete(
            $this->getMainTable(),
            $this->_getWriteAdapter()->quoteInto('category_id IN(?)', $deleteCategoryIds)
        );

        $currCategoryAnchorIds = array_intersect($anchorIds, $categoryIds);
        $anchorIds = array_diff($anchorIds, $categoryIds);
        $this->_refreshAnchorRelations($anchorIds);
        $currCategoryAnchorIds
            ? $this->_refreshAnchorRelations($currCategoryAnchorIds)
            : $this->_refreshDirectRelations($categoryIds);
    }

    /**
     * Rebuild index for direct associations categories and products
     *
     * @param null|array $categoryIds
     * @param null|array $productIds
     * @return Mage_Catalog_Model_Resource_Category_Indexer_Product
     */
    protected function _refreshDirectRelations($categoryIds = null, $productIds = null)
    {
        if (!$categoryIds && !$productIds) {
            return $this;
        }

        $visibilityInfo = $this->_getVisibilityAttributeInfo();
        $statusInfo     = $this->_getStatusAttributeInfo();
        $adapter = $this->_getWriteAdapter();
        /**
         * Insert direct relations
         * product_ids (enabled filter) X category_ids X store_ids
         * Validate store root category
         */
        $isParent = new Zend_Db_Expr('1');
        $select = $adapter->select()
            ->from(array('cp' => $this->_categoryProductTable),
                array('category_id', 'product_id', 'position', $isParent))
            ->joinInner(array('pw'  => $this->_productWebsiteTable), 'pw.product_id=cp.product_id', array())
            ->joinInner(array('g'   => $this->_groupTable), 'g.website_id=pw.website_id', array())
            ->joinInner(array('s'   => $this->_storeTable), 's.group_id=g.group_id', array('store_id'))
            ->joinInner(array('rc'  => $this->_categoryTable), 'rc.entity_id=g.root_category_id', array())
            ->joinInner(
                array('ce'=>$this->_categoryTable),
                'ce.entity_id=cp.category_id AND ('.
                $adapter->quoteIdentifier('ce.path') . ' LIKE ' .
                $adapter->getConcatSql(array($adapter->quoteIdentifier('rc.path') , $adapter->quote('/%'))) .
                ' OR ce.entity_id=rc.entity_id)',
                array())
            ->joinLeft(
                array('dv'=>$visibilityInfo['table']),
                $adapter->quoteInto(
                    "dv.entity_id=cp.product_id AND dv.attribute_id=? AND dv.store_id=0",
                    $visibilityInfo['id']),
                array()
            )
            ->joinLeft(
                array('sv'=>$visibilityInfo['table']),
                $adapter->quoteInto(
                    "sv.entity_id=cp.product_id AND sv.attribute_id=? AND sv.store_id=s.store_id",
                    $visibilityInfo['id']),
                array('visibility' => $adapter->getCheckSql('sv.value_id IS NOT NULL',
                    $adapter->quoteIdentifier('sv.value'),
                    $adapter->quoteIdentifier('dv.value')
                ))
            )
            ->joinLeft(
                array('ds'=>$statusInfo['table']),
                "ds.entity_id=cp.product_id AND ds.attribute_id={$statusInfo['id']} AND ds.store_id=0",
                array())
            ->joinLeft(
                array('ss'=>$statusInfo['table']),
                "ss.entity_id=cp.product_id AND ss.attribute_id={$statusInfo['id']} AND ss.store_id=s.store_id",
                array())
            ->where(
                $adapter->getCheckSql('ss.value_id IS NOT NULL',
                    $adapter->quoteIdentifier('ss.value'),
                    $adapter->quoteIdentifier('ds.value')
                ) . ' = ?',
                Mage_Catalog_Model_Product_Status::STATUS_ENABLED
            );
        if ($categoryIds) {
            $select->where('cp.category_id IN (?)', $categoryIds);
        }
        if ($productIds) {
            $select->where('cp.product_id IN(?)', $productIds);
        }
        $sql = $select->insertFromSelect(
            $this->getMainTable(),
            array('category_id', 'product_id', 'position', 'is_parent', 'store_id', 'visibility'),
            true
        );
        $adapter->query($sql);
        return $this;
    }

    /**
     * Rebuild index for anchor categories and associated t child categories products
     *
     * @param null | array $categoryIds
     * @param null | array $productIds
     * @return Mage_Catalog_Model_Resource_Category_Indexer_Product
     */
    protected function _refreshAnchorRelations($categoryIds = null, $productIds = null)
    {
        if (!$categoryIds && !$productIds) {
            return $this;
        }

        $anchorInfo     = $this->_getAnchorAttributeInfo();
        $visibilityInfo = $this->_getVisibilityAttributeInfo();
        $statusInfo     = $this->_getStatusAttributeInfo();

        /**
         * Insert anchor categories relations
         */
        $adapter = $this->_getReadAdapter();
        $isParent = $adapter->getCheckSql('MIN(cp.category_id)=ce.entity_id', 1, 0); // new Zend_Db_Expr('IF (, 1, 0) AS is_parent');
        $position = $adapter->getCheckSql(
            'MIN(cp.category_id)=ce.entity_id',
            'MIN(cp.position)',
            'ROUND((MIN(cc.position) + 1) * (MIN(' . $adapter->quoteIdentifier('cc.level') . ') + 1) * 10000, 0)'.
            ' + MIN(cp.position)'
        );
        $select = $adapter->select()
            ->distinct(true)
            ->from(array('ce' => $this->_categoryTable), array('entity_id'))
            ->joinLeft(
                array('cc' => $this->_categoryTable),
                $adapter->quoteIdentifier('cc.path') .
                ' LIKE ('.$adapter->getConcatSql(array($adapter->quoteIdentifier('ce.path'),$adapter->quote('/%'))).')'
                , array()
            )
            ->joinInner(
                array('cp' => $this->_categoryProductTable),
                'cp.category_id=cc.entity_id OR cp.category_id=ce.entity_id',
                array('cp.product_id', 'position' => $position, 'is_parent' => $isParent)
            )
            ->joinInner(array('pw' => $this->_productWebsiteTable), 'pw.product_id=cp.product_id', array())
            ->joinInner(array('g'  => $this->_groupTable), 'g.website_id=pw.website_id', array())
            ->joinInner(array('s'  => $this->_storeTable), 's.group_id=g.group_id', array('store_id'))
            ->joinInner(array('rc' => $this->_categoryTable), 'rc.entity_id=g.root_category_id', array())
            ->joinLeft(
                array('dca'=>$anchorInfo['table']),
                "dca.entity_id=ce.entity_id AND dca.attribute_id={$anchorInfo['id']} AND dca.store_id=0",
                array())
            ->joinLeft(
                array('sca'=>$anchorInfo['table']),
                "sca.entity_id=ce.entity_id AND sca.attribute_id={$anchorInfo['id']} AND sca.store_id=s.store_id",
                array())
            ->joinLeft(
                array('dv'=>$visibilityInfo['table']),
                "dv.entity_id=pw.product_id AND dv.attribute_id={$visibilityInfo['id']} AND dv.store_id=0",
                array())
            ->joinLeft(
                array('sv'=>$visibilityInfo['table']),
                "sv.entity_id=pw.product_id AND sv.attribute_id={$visibilityInfo['id']} AND sv.store_id=s.store_id",
                array('visibility' => $adapter->getCheckSql('MIN(sv.value_id) IS NOT NULL', 'MIN(sv.value)', 'MIN(dv.value)'))
            )
            ->joinLeft(
                array('ds'=>$statusInfo['table']),
                "ds.entity_id=pw.product_id AND ds.attribute_id={$statusInfo['id']} AND ds.store_id=0",
                array())
            ->joinLeft(
                array('ss'=>$statusInfo['table']),
                "ss.entity_id=pw.product_id AND ss.attribute_id={$statusInfo['id']} AND ss.store_id=s.store_id",
                array())
            /**
             * Condition for anchor or root category (all products should be assigned to root)
             */
            ->where('('.
                $adapter->quoteIdentifier('ce.path') . ' LIKE ' .
                $adapter->getConcatSql(array($adapter->quoteIdentifier('rc.path'), $adapter->quote('/%'))) . ' AND ' .
                $adapter->getCheckSql('sca.value_id IS NOT NULL',
                    $adapter->quoteIdentifier('sca.value'),
                    $adapter->quoteIdentifier('dca.value')) . '=1) OR ce.entity_id=rc.entity_id'
            )
            ->where(
                $adapter->getCheckSql('ss.value_id IS NOT NULL', 'ss.value', 'ds.value') . '=?',
                Mage_Catalog_Model_Product_Status::STATUS_ENABLED
            )
            ->group(array('ce.entity_id', 'cp.product_id', 's.store_id'));
        if ($categoryIds) {
            $select->where('ce.entity_id IN (?)', $categoryIds);
        }
        if ($productIds) {
            $select->where('pw.product_id IN(?)', $productIds);
        }

        $sql = $select->insertFromSelect($this->getMainTable());
        $this->_getWriteAdapter()->query($sql);
        return $this;
    }

    /**
     * Add product association with root store category for products which are not assigned to any another category
     *
     * @param int | array $productIds
     * @return Mage_Catalog_Model_Resource_Category_Indexer_Product
     */
    protected function _refreshRootRelations($productIds)
    {
        $visibilityInfo = $this->_getVisibilityAttributeInfo();
        $statusInfo     = $this->_getStatusAttributeInfo();
        $adapter = $this->_getWriteAdapter();
        /**
         * Insert anchor categories relations
         */
        $isParent = new Zend_Db_Expr('0');
        $position = new Zend_Db_Expr('0');
        $select = $this->_getReadAdapter()->select()
            ->distinct(true)
            ->from(array('pw'  => $this->_productWebsiteTable), array())
            ->joinInner(array('g'   => $this->_groupTable), 'g.website_id=pw.website_id', array())
            ->joinInner(array('s'   => $this->_storeTable), 's.group_id=g.group_id', array())
            ->joinInner(array('rc'  => $this->_categoryTable), 'rc.entity_id=g.root_category_id',
                array('entity_id'))
            ->joinLeft(array('cp'   => $this->_categoryProductTable), 'cp.product_id=pw.product_id',
                array('pw.product_id', $position, $isParent, 's.store_id'))
            ->joinLeft(
                array('dv'=>$visibilityInfo['table']),
                "dv.entity_id=pw.product_id AND dv.attribute_id={$visibilityInfo['id']} AND dv.store_id=0",
                array())
            ->joinLeft(
                array('sv'=>$visibilityInfo['table']),
                "sv.entity_id=pw.product_id AND sv.attribute_id={$visibilityInfo['id']} AND sv.store_id=s.store_id",
                array('visibility' => $adapter->getCheckSql('sv.value_id IS NOT NULL',
                    $adapter->quoteIdentifier('sv.value'),
                    $adapter->quoteIdentifier('dv.value')
                ))
            )
            ->joinLeft(
                array('ds'=>$statusInfo['table']),
                "ds.entity_id=pw.product_id AND ds.attribute_id={$statusInfo['id']} AND ds.store_id=0",
                array())
            ->joinLeft(
                array('ss'=>$statusInfo['table']),
                "ss.entity_id=pw.product_id AND ss.attribute_id={$statusInfo['id']} AND ss.store_id=s.store_id",
                array())
            /**
             * Condition for anchor or root category (all products should be assigned to root)
             */
            ->where('cp.product_id IS NULL')
            ->where(
                    $adapter->getCheckSql('ss.value_id IS NOT NULL',
                        $adapter->quoteIdentifier('ss.value'),
                        $adapter->quoteIdentifier('ds.value')
                    ) . ' = ?', Mage_Catalog_Model_Product_Status::STATUS_ENABLED)
            ->where('pw.product_id IN(?)', $productIds);

        $sql = $select->insertFromSelect($this->getMainTable());
        $this->_getWriteAdapter()->query($sql);
        return $this;
    }

    /**
     * Get is_anchor category attribute information
     *
     * @return array array('id' => $id, 'table'=>$table)
     */
    protected function _getAnchorAttributeInfo()
    {
        $isAnchorAttribute = Mage::getSingleton('eav/config')->getAttribute('catalog_category', 'is_anchor');
        $info = array(
            'id'    => $isAnchorAttribute->getId() ,
            'table' => $isAnchorAttribute->getBackend()->getTable()
        );
        return $info;
    }

    /**
     * Get visibility product attribute information
     *
     * @return array array('id' => $id, 'table'=>$table)
     */
    protected function _getVisibilityAttributeInfo()
    {
        $visibilityAttribute = Mage::getSingleton('eav/config')->getAttribute('catalog_product', 'visibility');
        $info = array(
            'id'    => $visibilityAttribute->getId() ,
            'table' => $visibilityAttribute->getBackend()->getTable()
        );
        return $info;
    }

    /**
     * Get status product attribute information
     *
     * @return array array('id' => $id, 'table'=>$table)
     */
    protected function _getStatusAttributeInfo()
    {
        $statusAttribute = Mage::getSingleton('eav/config')->getAttribute('catalog_product', 'status');
        $info = array(
            'id'    => $statusAttribute->getId() ,
            'table' => $statusAttribute->getBackend()->getTable()
        );
        return $info;
    }

    /**
     * Rebuild all index data
     *
     * @return Mage_Catalog_Model_Resource_Category_Indexer_Product
     */
    public function reindexAll()
    {
        $this->useIdxTable(true);
        $this->clearTemporaryIndexTable();
        $idxTable = $this->getIdxTable();
        $idxAdapter = $this->_getIndexAdapter();
        $stores = $this->_getStoresInfo();
        /**
         * Build index for each store
         */
        foreach ($stores as $storeData) {
            $storeId    = $storeData['store_id'];
            $websiteId  = $storeData['website_id'];
            $rootPath   = $storeData['root_path'];
            $rootId     = $storeData['root_id'];
            /**
             * Prepare visibility for all enabled store products
             */
            $enabledTable = $this->_prepareEnabledProductsVisibility($websiteId, $storeId);
            /**
             * Select information about anchor categories
             */
            $anchorTable = $this->_prepareAnchorCategories($storeId, $rootPath);
            /**
             * Add relations between not anchor categories and products
             */
            $select = $idxAdapter->select();
            /* @var $select Varien_Db_Select */
            $select->from(
                array('cp' => $this->_categoryProductTable),
                array('category_id', 'product_id', 'position', 'is_parent' => new Zend_Db_Expr('1'),
                    'store_id' => new Zend_Db_Expr($storeId))
            )
            ->joinInner(array('pv' => $enabledTable), 'pv.product_id=cp.product_id', array('visibility'))
            ->joinLeft(array('ac' => $anchorTable), 'ac.category_id=cp.category_id', array())
            ->where('ac.category_id IS NULL');

            $sql = $select->insertFromSelect(
                $idxTable,
                array('category_id', 'product_id', 'position', 'is_parent', 'store_id', 'visibility'),
                false
            );
            $idxAdapter->query($sql);
            /**
             * Assign products not associated to any category to root category in index
             */

            $select = $idxAdapter->select();
            $select->from(
                array('pv' => $enabledTable),
                array(new Zend_Db_Expr($rootId), 'product_id', new Zend_Db_Expr('0'), new Zend_Db_Expr('1'),
                    new Zend_Db_Expr($storeId), 'visibility')
            )
            ->joinLeft(array('cp' => $this->_categoryProductTable), 'pv.product_id=cp.product_id', array())
            ->where('cp.product_id IS NULL');

            $sql = $select->insertFromSelect(
                $idxTable,
                array('category_id', 'product_id', 'position', 'is_parent', 'store_id', 'visibility'),
                false
            );
            $idxAdapter->query($sql);

            /**
             * Prepare anchor categories products
             */
            $anchorProductsTable = $this->_getAnchorCategoriesProductsTemporaryTable();
            $idxAdapter->delete($anchorProductsTable);

            $position = $idxAdapter->getCheckSql('ca.category_id=ce.entity_id',
                $idxAdapter->quoteIdentifier('cp.position'),
                'ROUND(('.$idxAdapter->quoteIdentifier('ce.position').' + 1) * '.
                '('.$idxAdapter->quoteIdentifier('ce.level').' + 1) * 10000, 0) + '.
                $idxAdapter->quoteIdentifier('cp.position')//.')'
                );

            $select = $idxAdapter->select()
            ->useStraightJoin(true)
            ->distinct(true)
            ->from(array('ca' => $anchorTable), array('category_id'))
            ->joinInner(
                array('ce' => $this->_categoryTable),
                $idxAdapter->quoteIdentifier('ce.path') . ' LIKE ' .
                $idxAdapter->quoteIdentifier('ca.path') . ' OR ce.entity_id = ca.category_id',
                array()
            )
            ->joinInner(
                array('cp' => $this->_categoryProductTable),
                'cp.category_id = ce.entity_id',
                array('product_id')
            )
            ->joinInner(array('pv' => $enabledTable), 'pv.product_id = cp.product_id', array('position' => $position));

            $sql = $select->insertFromSelect(
                $anchorProductsTable,
                array('category_id', 'product_id', 'position'),
                false
            );
            $idxAdapter->query($sql);

            /**
             * Add anchor categories products to index
             */
            $select = $idxAdapter->select()
            ->from(
                array('ap' => $anchorProductsTable),
                array('category_id', 'product_id', 'position',
                'is_parent' => $idxAdapter->getCheckSql('cp.product_id > 0', 1, 0),
                'store_id' => new Zend_Db_Expr($storeId))
            )
            ->joinLeft(
                array('cp' => $this->_categoryProductTable),
                'cp.category_id=ap.category_id AND cp.product_id=ap.product_id',
                array()
            )
            ->joinInner(array('pv' => $enabledTable), 'pv.product_id = ap.product_id', array('visibility'));

            $sql = $select->insertFromSelect(
                $idxTable,
                array('category_id', 'product_id', 'position', 'is_parent', 'store_id', 'visibility'),
                false
            );
            $idxAdapter->query($sql);
        }
        $this->syncData();

        /**
         * Clean up temporary tables
         */
        $this->clearTemporaryIndexTable();
        $idxAdapter->delete($enabledTable);
        $idxAdapter->delete($anchorTable);
        $idxAdapter->delete($anchorProductsTable);
        return $this;
    }

    /**
     * Get array with store|website|root_categry path information
     *
     * @return array
     */
    protected function _getStoresInfo()
    {
        $adapter = $this->_getReadAdapter();
        $select = $adapter->select()
            ->from(array('s' => $this->getTable('core/store')), array('store_id', 'website_id'))
            ->join(array('sg' => $this->getTable('core/store_group')), 'sg.group_id=s.group_id', array())
            ->join(
                array('c' => $this->getTable('catalog/category')),
                'c.entity_id=sg.root_category_id',
                array(
                    'root_path' => $adapter->quoteIdentifier('c.path'),
                    'root_id' => $adapter->quoteIdentifier('c.entity_id')
                )
            );
        $stores = $adapter->fetchAll($select);
        return $stores;
    }

    /**
     * Create temporary table with enabled products visibility info
     *
     * @param unknown_type $websiteId
     * @param unknown_type $storeId
     * @return string temporary table name
     */
    protected function _prepareEnabledProductsVisibility($websiteId, $storeId)
    {
        $statusAttribute = Mage::getSingleton('eav/config')->getAttribute('catalog_product', 'status');
        $visibilityAttribute = Mage::getSingleton('eav/config')->getAttribute('catalog_product', 'visibility');
        $statusAttributeId = $statusAttribute->getId();
        $visibilityAttributeId = $visibilityAttribute->getId();
        $statusTable = $statusAttribute->getBackend()->getTable();
        $visibilityTable = $visibilityAttribute->getBackend()->getTable();

        /**
         * Prepare temporary table
         */
        $tmpTable = $this->_getEnabledProductsTemporaryTable();
        $this->_getIndexAdapter()->delete($tmpTable);

        $adapter        = $this->_getIndexAdapter();
        $visibilityExpr = $adapter->getCheckSql('pvs.value_id>0', $adapter->quoteIdentifier('pvs.value'),
            $adapter->quoteIdentifier('pvd.value'));
        $select         = $adapter->select()
            ->from(array('pw' => $this->_productWebsiteTable), array('product_id', 'visibility' => $visibilityExpr))
            ->joinLeft(
                array('pvd' => $visibilityTable),
                $adapter->quoteInto('pvd.entity_id=pw.product_id AND pvd.attribute_id=? AND pvd.store_id=0',
                    $visibilityAttributeId),
                array())
            ->joinLeft(
                array('pvs' => $visibilityTable),
                $adapter->quoteInto('pvs.entity_id=pw.product_id AND pvs.attribute_id=? AND ', $visibilityAttributeId)
                    . $adapter->quoteInto('pvs.store_id=?', $storeId),
                array())
            ->joinLeft(
                array('psd' => $statusTable),
                $adapter->quoteInto('psd.entity_id=pw.product_id AND psd.attribute_id=? AND psd.store_id=0',
                    $statusAttributeId),
                array())
            ->joinLeft(
                array('pss' => $statusTable),
                    $adapter->quoteInto('pss.entity_id=pw.product_id AND pss.attribute_id=? AND ', $statusAttributeId)
                        . $adapter->quoteInto('pss.store_id=?', $storeId),
                array())
            ->where('pw.website_id=?',$websiteId)
            ->where($adapter->getCheckSql('pss.value_id>0',
                $adapter->quoteIdentifier('pss.value'),
                $adapter->quoteIdentifier('psd.value')) . ' = ?', Mage_Catalog_Model_Product_Status::STATUS_ENABLED);

        $query = $select->insertFromSelect($tmpTable, array('product_id' , 'visibility'), false);
        $adapter->query($query);
        return $tmpTable;
    }

    /**
     * Retrieve temporary table of category enabled products
     *
     * @return string
     */
    protected function _getEnabledProductsTemporaryTable()
    {
        if ($this->useIdxTable()) {
            return $this->getTable('catalog/category_product_enabled_indexer_idx');
        }
        return $this->getTable('catalog/category_product_enabled_indexer_tmp');
    }

    /**
     * Create temporary table with list of anchor categories
     *
     * @param int $storeId
     * @param unknown_type $rootPath
     * @return string temporary table name
     */
    protected function _prepareAnchorCategories($storeId, $rootPath)
    {
        $isAnchorAttribute = Mage::getSingleton('eav/config')->getAttribute('catalog_category', 'is_anchor');
        $anchorAttributeId = $isAnchorAttribute->getId();
        $anchorTable = $isAnchorAttribute->getBackend()->getTable();
        $adapter = $this->_getIndexAdapter();
        $tmpTable = $this->_getAnchorCategoriesTemporaryTable();
        $adapter->delete($tmpTable);

        $anchorExpr = $adapter->getCheckSql('cas.value_id>0', $adapter->quoteIdentifier('cas.value'),
            $adapter->quoteIdentifier('cad.value'));
        $pathConcat = $adapter->getConcatSql(array($adapter->quoteIdentifier('ce.path'), $adapter->quote('/%')));
        $select = $adapter->select()
            ->from(
                array('ce' => $this->_categoryTable),
                array('category_id' => 'ce.entity_id', 'path' => $pathConcat))
            ->joinLeft(
                array('cad' => $anchorTable),
                $adapter->quoteInto("cad.entity_id=ce.entity_id AND cad.attribute_id=? AND cad.store_id=0",
                    $anchorAttributeId),
                array())
            ->joinLeft(
                array('cas' => $anchorTable),
                $adapter->quoteInto("cas.entity_id=ce.entity_id AND cas.attribute_id=? AND ", $anchorAttributeId)
                    . $adapter->quoteInto('cas.store_id=?', $storeId),
                array())
            ->where("{$anchorExpr} = 1 AND {$adapter->quoteIdentifier('ce.path')} LIKE ?", $rootPath . '%1')
            ->orWhere('ce.path = ?', $rootPath);

        $query = $select->insertFromSelect($tmpTable, array('category_id' , 'path'), false);
        $adapter->query($query);
        return $tmpTable;
    }

    /**
     * Retrieve temporary table of anchor categories
     *
     * @return string
     */
    protected function _getAnchorCategoriesTemporaryTable()
    {
        if ($this->useIdxTable()) {
            return $this->getTable('catalog/category_anchor_indexer_idx');
        }
        return $this->getTable('catalog/category_anchor_indexer_tmp');
    }

    /**
     * Retrieve temporary table of anchor categories products
     *
     * @return string
     */
    protected function _getAnchorCategoriesProductsTemporaryTable()
    {
        if ($this->useIdxTable()) {
            return $this->getTable('catalog/category_anchor_products_indexer_idx');
        }
        return $this->getTable('catalog/category_anchor_products_indexer_tmp');
    }

    /**
     * Retrieve temporary decimal index table name
     *
     * @param unknown_type $table
     * @return string
     */
    public function getIdxTable($table = null)
    {
        if ($this->useIdxTable()) {
            return $this->getTable('catalog/category_product_indexer_idx');
        }
        return $this->getTable('catalog/category_product_indexer_tmp');
    }
}
