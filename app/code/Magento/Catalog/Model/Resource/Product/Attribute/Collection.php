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
 * Catalog product EAV additional attribute resource collection
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Catalog_Model_Resource_Product_Attribute_Collection
    extends Magento_Eav_Model_Resource_Entity_Attribute_Collection
{
    /**
     * Resource model initialization
     *
     */
    protected function _construct()
    {
        $this->_init('Magento_Catalog_Model_Resource_Eav_Attribute', 'Magento_Eav_Model_Resource_Entity_Attribute');
    }

    /**
     * initialize select object
     *
     * @return Magento_Catalog_Model_Resource_Product_Attribute_Collection
     */
    protected function _initSelect()
    {
        $entityTypeId = (int)Mage::getModel('Magento_Eav_Model_Entity')->setType(Magento_Catalog_Model_Product::ENTITY)
            ->getTypeId();
        $columns = $this->getConnection()->describeTable($this->getResource()->getMainTable());
        unset($columns['attribute_id']);
        $retColumns = array();
        foreach ($columns as $labelColumn => $columnData) {
            $retColumns[$labelColumn] = $labelColumn;
            if ($columnData['DATA_TYPE'] == Magento_DB_Ddl_Table::TYPE_TEXT) {
                $retColumns[$labelColumn] = 'main_table.' . $labelColumn;
            }
        }
        $this->getSelect()
            ->from(array('main_table' => $this->getResource()->getMainTable()), $retColumns)
            ->join(
                array('additional_table' => $this->getTable('catalog_eav_attribute')),
                'additional_table.attribute_id = main_table.attribute_id'
            )
            ->where('main_table.entity_type_id = ?', $entityTypeId);
        return $this;
    }

    /**
     * Specify attribute entity type filter.
     * Entity type is defined.
     *
     * @param  int $typeId
     * @return Magento_Catalog_Model_Resource_Product_Attribute_Collection
     */
    public function setEntityTypeFilter($typeId)
    {
        return $this;
    }

    /**
     * Return array of fields to load attribute values
     *
     * @return array
     */
    protected function _getLoadDataFields()
    {
        $fields = array_merge(
            parent::_getLoadDataFields(),
            array(
                'additional_table.is_global',
                'additional_table.is_html_allowed_on_front',
                'additional_table.is_wysiwyg_enabled'
            )
        );

        return $fields;
    }

    /**
     * Remove price from attribute list
     *
     * @return Magento_Catalog_Model_Resource_Product_Attribute_Collection
     */
    public function removePriceFilter()
    {
        return $this->addFieldToFilter('main_table.attribute_code', array('neq' => 'price'));
    }

    /**
     * Specify "is_visible_in_advanced_search" filter
     *
     * @return Magento_Catalog_Model_Resource_Product_Attribute_Collection
     */
    public function addDisplayInAdvancedSearchFilter()
    {
        return $this->addFieldToFilter('additional_table.is_visible_in_advanced_search', 1);
    }

    /**
     * Specify "is_filterable" filter
     *
     * @return Magento_Catalog_Model_Resource_Product_Attribute_Collection
     */
    public function addIsFilterableFilter()
    {
        return $this->addFieldToFilter('additional_table.is_filterable', array('gt' => 0));
    }

    /**
     * Add filterable in search filter
     *
     * @return Magento_Catalog_Model_Resource_Product_Attribute_Collection
     */
    public function addIsFilterableInSearchFilter()
    {
        return $this->addFieldToFilter('additional_table.is_filterable_in_search', array('gt' => 0));
    }

    /**
     * Specify filter by "is_visible" field
     *
     * @return Magento_Catalog_Model_Resource_Product_Attribute_Collection
     */
    public function addVisibleFilter()
    {
        return $this->addFieldToFilter('additional_table.is_visible', 1);
    }

    /**
     * Specify "is_searchable" filter
     *
     * @return Magento_Catalog_Model_Resource_Product_Attribute_Collection
     */
    public function addIsSearchableFilter()
    {
        return $this->addFieldToFilter('additional_table.is_searchable', 1);
    }

    /**
     * Specify filter for attributes that have to be indexed
     *
     * @param bool $addRequiredCodes
     * @return Magento_Catalog_Model_Resource_Product_Attribute_Collection
     */
    public function addToIndexFilter($addRequiredCodes = false)
    {
        $conditions = array(
            'additional_table.is_searchable = 1',
            'additional_table.is_visible_in_advanced_search = 1',
            'additional_table.is_filterable > 0',
            'additional_table.is_filterable_in_search = 1',
            'additional_table.used_for_sort_by = 1'
        );

        if ($addRequiredCodes) {
            $conditions[] = $this->getConnection()->quoteInto('main_table.attribute_code IN (?)',
                array('status', 'visibility'));
        }

        $this->getSelect()->where(sprintf('(%s)', implode(' OR ', $conditions)));

        return $this;
    }

    /**
     * Specify filter for attributes used in quick search
     *
     * @return Magento_Catalog_Model_Resource_Product_Attribute_Collection
     */
    public function addSearchableAttributeFilter()
    {
        $this->getSelect()->where(
            'additional_table.is_searchable = 1 OR '.
            $this->getConnection()->quoteInto('main_table.attribute_code IN (?)', array('status', 'visibility'))
        );

        return $this;
    }
}