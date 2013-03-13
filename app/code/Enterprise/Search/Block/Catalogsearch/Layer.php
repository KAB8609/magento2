<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Search
 * @copyright   {copyright}
 * @license     {license_link}
 */

 /**
 * Layered Navigation block for search
 *
 * @category    Enterprise
 * @package     Enterprise_Search
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Search_Block_Catalogsearch_Layer extends Mage_CatalogSearch_Block_Layer
{
    /**
     * Initialize blocks names
     */
    protected function _initBlocks()
    {
        parent::_initBlocks();

        if (Mage::helper('Enterprise_Search_Helper_Data')->getIsEngineAvailableForNavigation(false)) {
            $this->_categoryBlockName        = 'Enterprise_Search_Block_Catalog_Layer_Filter_Category';
            $this->_attributeFilterBlockName = 'Enterprise_Search_Block_Catalogsearch_Layer_Filter_Attribute';
            $this->_priceFilterBlockName     = 'Enterprise_Search_Block_Catalog_Layer_Filter_Price';
            $this->_decimalFilterBlockName   = 'Enterprise_Search_Block_Catalog_Layer_Filter_Decimal';
        }
    }

    /**
     * Prepare child blocks
     *
     * @return Enterprise_Search_Block_Catalog_Layer_View
     */
    protected function _prepareLayout()
    {
        $helper = Mage::helper('Enterprise_Search_Helper_Data');
        if ($helper->isThirdPartSearchEngine() && $helper->getIsEngineAvailableForNavigation(false)) {
            $stateBlock = $this->getLayout()->createBlock($this->_stateBlockName)
                ->setLayer($this->getLayer());

            $categoryBlock = $this->getLayout()->createBlock($this->_categoryBlockName)
                ->setLayer($this->getLayer())
                ->init();

            $filterableAttributes = $this->_getFilterableAttributes();
            $filters = array();
            foreach ($filterableAttributes as $attribute) {
                if ($attribute->getAttributeCode() == 'price') {
                    $filterBlockName = $this->_priceFilterBlockName;
                } elseif ($attribute->getBackendType() == 'decimal') {
                    $filterBlockName = $this->_decimalFilterBlockName;
                } else {
                    $filterBlockName = $this->_attributeFilterBlockName;
                }

                $filters[$attribute->getAttributeCode() . '_filter'] = $this->getLayout()->createBlock($filterBlockName)
                    ->setLayer($this->getLayer())
                    ->setAttributeModel($attribute)
                    ->init();
            }

            $this->setChild('layer_state', $stateBlock);
            $this->setChild('category_filter', $categoryBlock->addFacetCondition());

            foreach ($filters as $filterName => $block) {
                $this->setChild($filterName, $block->addFacetCondition());
            }

            $this->getLayer()->apply();
        } else {
            parent::_prepareLayout();
        }

        return $this;
    }

    /**
     * Check availability display layer block
     *
     * @return bool
     */
    public function canShowBlock()
    {
        $helper = Mage::helper('Enterprise_Search_Helper_Data');
        if ($helper->isThirdPartSearchEngine() && $helper->isActiveEngine()) {
            return ($this->canShowOptions() || count($this->getLayer()->getState()->getFilters()));
        }
        return parent::canShowBlock();
    }

    /**
     * Get layer object
     *
     * @return Mage_Catalog_Model_Layer
     */
    public function getLayer()
    {
        $helper = Mage::helper('Enterprise_Search_Helper_Data');
        if ($helper->isThirdPartSearchEngine() && $helper->isActiveEngine()) {
            return Mage::getSingleton('Enterprise_Search_Model_Search_Layer');
        }

        return parent::getLayer();
    }
}
