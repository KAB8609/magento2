<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_CatalogSearch
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_CatalogSearch_Model_Layer extends Mage_Catalog_Model_Layer
{
    const XML_PATH_DISPLAY_LAYER_COUNT = 'catalog/search/use_layered_navigation_count';

    /**
     * Get current layer product collection
     *
     * @return Mage_Catalog_Model_Resource_Eav_Resource_Product_Collection
     */
    public function getProductCollection()
    {
        if (isset($this->_productCollections[$this->getCurrentCategory()->getId()])) {
            $collection = $this->_productCollections[$this->getCurrentCategory()->getId()];
        } else {
            $collection = Mage::getResourceModel('Mage_CatalogSearch_Model_Resource_Fulltext_Collection');
            $this->prepareProductCollection($collection);
            $this->_productCollections[$this->getCurrentCategory()->getId()] = $collection;
        }
        return $collection;
    }

    /**
     * Prepare product collection
     *
     * @param Mage_Catalog_Model_Resource_Eav_Resource_Product_Collection $collection
     * @return Mage_Catalog_Model_Layer
     */
    public function prepareProductCollection($collection)
    {
        $collection
            ->addAttributeToSelect(Mage::getSingleton('Mage_Catalog_Model_Config')->getProductAttributes())
            ->addSearchFilter(Mage::helper('Mage_CatalogSearch_Helper_Data')->getQuery()->getQueryText())
            ->setStore(Mage::app()->getStore())
            ->addMinimalPrice()
            ->addFinalPrice()
            ->addTaxPercents()
            ->addStoreFilter()
            ->addUrlRewrite()
            ->setVisibility(Mage::getSingleton('Mage_Catalog_Model_Product_Visibility')->getVisibleInSearchIds());

        return $this;
    }

    /**
     * Get layer state key
     *
     * @return string
     */
    public function getStateKey()
    {
        if ($this->_stateKey === null) {
            $this->_stateKey = 'Q_' . Mage::helper('Mage_CatalogSearch_Helper_Data')->getQuery()->getId()
                . '_'. parent::getStateKey();
        }
        return $this->_stateKey;
    }

    /**
     * Get default tags for current layer state
     *
     * @param   array $additionalTags
     * @return  array
     */
    public function getStateTags(array $additionalTags = array())
    {
        $additionalTags = parent::getStateTags($additionalTags);
        $additionalTags[] = Mage_CatalogSearch_Model_Query::CACHE_TAG;
        return $additionalTags;
    }

    /**
     * Add filters to attribute collection
     *
     * @param   Mage_Catalog_Model_Resource_Eav_Resource_Product_Attribute_Collection $collection
     * @return  Mage_Catalog_Model_Resource_Eav_Resource_Product_Attribute_Collection
     */
    protected function _prepareAttributeCollection($collection)
    {
        $collection->addIsFilterableInSearchFilter()
            ->addVisibleFilter();
        return $collection;
    }

    /**
     * Prepare attribute for use in layered navigation
     *
     * @param   Mage_Eav_Model_Entity_Attribute $attribute
     * @return  Mage_Eav_Model_Entity_Attribute
     */
    protected function _prepareAttribute($attribute)
    {
        $attribute = parent::_prepareAttribute($attribute);
        $attribute->setIsFilterable(Mage_Catalog_Model_Layer_Filter_Attribute::OPTIONS_ONLY_WITH_RESULTS);
        return $attribute;
    }
}
