<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Search
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Catalog attribute layer filter
 *
 * @category   Magento
 * @package    Magento_Search
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Search_Block_Catalog_Layer_Filter_Attribute extends Magento_Catalog_Block_Layer_Filter_Abstract
{
    /**
     * Set model name
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_filterModelName = 'Magento_Search_Model_Catalog_Layer_Filter_Attribute';
    }

    /**
     * Set attribute model
     *
     * @return Magento_Search_Block_Catalog_Layer_Filter_Attribute
     */
    protected function _prepareFilter()
    {
        $this->_filter->setAttributeModel($this->getAttributeModel());
        return $this;
    }

    /**
     * Add params to faceted search
     *
     * @return Magento_Search_Block_Catalog_Layer_Filter_Attribute
     */
    public function addFacetCondition()
    {
        $this->_filter->addFacetCondition();
        return $this;
    }

    /**
     * Get filter items count
     *
     * @return int
     */
    public function getItemsCount()
    {
        $attributeIsFilterable = $this->getAttributeModel()->getIsFilterable();
        if ($attributeIsFilterable == Magento_Catalog_Model_Layer_Filter_Attribute::OPTIONS_ONLY_WITH_RESULTS) {
            return parent::getItemsCount();
        }

        $count = 0;
        foreach ($this->getItems() as $item) {
            if ($item->getCount()) {
                $count++;
            }
        }

        return $count;
    }
}