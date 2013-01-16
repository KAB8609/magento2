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
     * Get html of element
     *
     * @return string
     */
    public function getElementHtml()
    {
        /** @var $coreHelper Mage_Core_Helper_Data */
        $coreHelper = Mage::helper('Mage_Core_Helper_Data');
        $treeOptions = $coreHelper->escapeHtml($coreHelper->jsonEncode(array(
            'jstree' => array(
                'plugins' => array('themes', 'html_data', 'ui', 'hotkeys')
            )
        )));

        return parent::getElementHtml() . "\n"
            . '<input id="' . $this->getHtmlId() . '-suggest" />' . "\n"
            . '<script id="' . $this->getHtmlId() . '-template" type="text/x-jquery-tmpl">'
            . '{{if $data.allShown()}}{{if typeof nested === "undefined"}}<div data-mage-init="' . $treeOptions . '">{{/if}}'
            . '<ul>{{each items}}'
            . '<li><a href="#">${$value.label}</a>'
            . '{{if $value.children && $value.children.length}}'
            . '{{tmpl(jQuery.extend({}, $data, {items: $value.children, nested: true})) template}}'
            . '{{/if}}'
            . '</li>{{/each}}</ul>'
            . '{{if typeof nested === "undefined"}}</div>{{/if}}{{else}}'
            . '<ul data-mage-init="{&quot;menu&quot;:[]}">'
            . '{{each items}}'
            . '<li><a href="#">${$value.label}</a></li>'
            . '{{/each}}</ul>{{/if}}'
            . '</script>' . "\n"
            . '<script>//<![CDATA[' . "\n"
            . 'jQuery(' . $coreHelper->jsonEncode('#' . $this->getHtmlId() . '-suggest') . ').multisuggest('
            . $coreHelper->jsonEncode($this->_getSelectorOptions()) . ')' . "\n"
            . '//]]></script>';
    }

    /**
     * Get selector options
     *
     * @return array
     */
    protected function _getSelectorOptions()
    {
        return array(
            'source' => Mage::helper('Mage_Backend_Helper_Data')->getUrl('adminhtml/catalog_category/suggestCategories'),
            'valueField' => '#' . $this->getHtmlId(),
            'template' => '#' . $this->getHtmlId() . '-template',
            'control' => 'jstree',
            'showRecent' => false
        );
    }
}
