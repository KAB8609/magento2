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
 * Catalog Category *_sort_by Attributes Source Model
 *
 * @category   Magento
 * @package    Magento_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Catalog_Model_Category_Attribute_Source_Sortby
    extends Magento_Eav_Model_Entity_Attribute_Source_Abstract
{
    /**
     * Retrieve Catalog Config Singleton
     *
     * @return Magento_Catalog_Model_Config
     */
    protected function _getCatalogConfig() {
        return Mage::getSingleton('Magento_Catalog_Model_Config');
    }

    /**
     * Retrieve All options
     *
     * @return array
     */
    public function getAllOptions()
    {
        if (is_null($this->_options)) {
            $this->_options = array(array(
                'label' => __('Position'),
                'value' => 'position'
            ));
            foreach ($this->_getCatalogConfig()->getAttributesUsedForSortBy() as $attribute) {
                $this->_options[] = array(
                    'label' => __($attribute['frontend_label']),
                    'value' => $attribute['attribute_code']
                );
            }
        }
        return $this->_options;
    }
}