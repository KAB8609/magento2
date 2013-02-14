<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Product form category field helper
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Catalog_Product_Helper_Form_Category extends Varien_Data_Form_Element_Multiselect
{
    /**
     * Get values for select
     * @return array
     */
    public function getValues()
    {
        $collection = $this->_getCategoriesCollection();
        $values = $this->getValue();
        if (!is_array($values)) {
            $values = explode(',', $values);
        }
        $collection->addAttributeToSelect('name');
        $collection->addIdFilter($values);

        $options = array();

        foreach ($collection as $category) {
            $options[] = array(
                'label' => $category->getName(),
                'value' => $category->getId()
            );
        }
        return $options;
    }

    /**
     * Get categories collection
     * @return Mage_Catalog_Model_Resource_Category_Collection
     */
    protected function _getCategoriesCollection()
    {
        return Mage::getResourceModel('Mage_Catalog_Model_Resource_Category_Collection');
    }

    /**
     * Attach category suggest widget initialization
     *
     * @return string
     */
    public function getAfterElementHtml()
    {
        /** @var $coreHelper Mage_Core_Helper_Data */
        $coreHelper = Mage::helper('Mage_Core_Helper_Data');
        $treeOptions = $coreHelper->escapeHtml($coreHelper->jsonEncode(array(
            'jstree' => array(
                'plugins' => array('themes', 'html_data', 'ui', 'hotkeys'),
                'themes' => array(
                    'theme' => 'default',
                    'dots' => false,
                    'icons' => false,
                ),
            )
        )));

        $htmlId = $this->getHtmlId();
        $suggestPlaceholder = Mage::helper('Mage_Catalog_Helper_Data')->__('start typing to search category');
        $selectorOptions = $coreHelper->jsonEncode($this->_getSelectorOptions());
        $newCategoryCaption = Mage::helper('Mage_Catalog_Helper_Data')->__('New Category');

        return <<<HTML
    <input id="{$htmlId}-suggest" placeholder="$suggestPlaceholder" />
    <script>
        jQuery('#{$htmlId}-suggest').treeSuggest({$selectorOptions});
    </script>
    <button title="{$newCategoryCaption}" type="button" onclick="jQuery('#new-category').dialog('open')">
        <span><span><span>{$newCategoryCaption}</span></span></span>
    </button>
HTML;
    }

    /**
     * Get selector options
     *
     * @return array
     */
    protected function _getSelectorOptions()
    {
        return array(
            'source' => Mage::helper('Mage_Backend_Helper_Data')->getUrl('*/catalog_category/suggestCategories'),
            'valueField' => '#' . $this->getHtmlId(),
            'className' => 'category-select',
            'multiselect' => true,
        );
    }
}
