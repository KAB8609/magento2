<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Catalog Product Eav Indexer Resource Model
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Catalog\Model\Resource\Product\Indexer;

class Eav extends \Magento\Catalog\Model\Resource\Product\Indexer\AbstractIndexer
{
    /**
     * EAV Indexers by type
     *
     * @var array
     */
    protected $_types;

    /**
     * Define main index table
     *
     */
    protected function _construct()
    {
        $this->_init('catalog_product_index_eav', 'entity_id');
    }

    /**
     * Retrieve array of EAV type indexers
     *
     * @return array
     */
    public function getIndexers()
    {
        if (is_null($this->_types)) {
            $this->_types   = array(
                'source'    => \Mage::getResourceModel('Magento\Catalog\Model\Resource\Product\Indexer\Eav\Source'),
                'decimal'   => \Mage::getResourceModel('Magento\Catalog\Model\Resource\Product\Indexer\Eav\Decimal'),
            );
        }

        return $this->_types;
    }

    /**
     * Retrieve indexer instance by type
     *
     * @param string $type
     * @return \Magento\Catalog\Model\Resource\Product\Indexer\Eav\AbstractEav
     */
    public function getIndexer($type)
    {
        $indexers = $this->getIndexers();
        if (!isset($indexers[$type])) {
            \Mage::throwException(__('We found an unknown EAV indexer type "%1".', $type));
        }
        return $indexers[$type];
    }

    /**
     * Process product save.
     * Method is responsible for index support
     * when product was saved and assigned categories was changed.
     *
     * @param \Magento\Index\Model\Event $event
     * @return \Magento\Catalog\Model\Resource\Product\Indexer\Eav
     */
    public function catalogProductSave(\Magento\Index\Model\Event $event)
    {
        $productId = $event->getEntityPk();
        $data = $event->getNewData();

        /**
         * Check if filterable attribute values were updated
         */
        if (!isset($data['reindex_eav'])) {
            return $this;
        }

        foreach ($this->getIndexers() as $indexer) {
            /** @var $indexer \Magento\Catalog\Model\Resource\Product\Indexer\Eav\AbstractEav */
            $indexer->reindexEntities($productId);
        }

        return $this;
    }

    /**
     * Process Product Delete
     *
     * @param \Magento\Index\Model\Event $event
     * @return \Magento\Catalog\Model\Resource\Product\Indexer\Eav
     */
    public function catalogProductDelete(\Magento\Index\Model\Event $event)
    {
        $data = $event->getNewData();
        if (empty($data['reindex_eav_parent_ids'])) {
            return $this;
        }

        foreach ($this->getIndexers() as $indexer) {
            /** @var $indexer \Magento\Catalog\Model\Resource\Product\Indexer\Eav\AbstractEav */
            $indexer->reindexEntities($data['reindex_eav_parent_ids']);
        }

        return $this;
    }

    /**
     * Process Product Mass Update
     *
     * @param \Magento\Index\Model\Event $event
     * @return \Magento\Catalog\Model\Resource\Product\Indexer\Eav
     */
    public function catalogProductMassAction(\Magento\Index\Model\Event $event)
    {
        $data = $event->getNewData();
        if (empty($data['reindex_eav_product_ids'])) {
            return $this;
        }

        foreach ($this->getIndexers() as $indexer) {
            /** @var $indexer \Magento\Catalog\Model\Resource\Product\Indexer\Eav\AbstractEav */
            $indexer->reindexEntities($data['reindex_eav_product_ids']);
        }

        return $this;
    }

    /**
     * Process Catalog Eav Attribute Save
     *
     * @param \Magento\Index\Model\Event $event
     * @return \Magento\Catalog\Model\Resource\Product\Indexer\Eav
     */
    public function catalogEavAttributeSave(\Magento\Index\Model\Event $event)
    {
        $data = $event->getNewData();
        if (empty($data['reindex_attribute'])) {
            return $this;
        }

        $indexer = $this->getIndexer($data['attribute_index_type']);

        $indexer->reindexAttribute($event->getEntityPk(), !empty($data['is_indexable']));

        return $this;
    }

    /**
     * Rebuild all index data
     *
     * @return \Magento\Catalog\Model\Resource\Product\Indexer\Eav
     */
    public function reindexAll()
    {
        $this->useIdxTable(true);
        foreach ($this->getIndexers() as $indexer) {
            /** @var $indexer \Magento\Catalog\Model\Resource\Product\Indexer\Eav\AbstractEav */
            $indexer->reindexAll();
        }

        return $this;
    }

    /**
     * Retrieve temporary source index table name
     *
     * @param string $table
     * @return string
     */
    public function getIdxTable($table = null)
    {
        if ($this->useIdxTable()) {
           return $this->getTable('catalog_product_index_eav_idx');
        }
        return $this->getTable('catalog_product_index_eav_tmp');
    }
}
