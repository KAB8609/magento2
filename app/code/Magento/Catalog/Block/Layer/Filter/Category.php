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
 * Catalog layer category filter
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Catalog_Block_Layer_Filter_Category extends Magento_Catalog_Block_Layer_Filter_Abstract
{
    protected function _construct()
    {
        parent::_construct();
        $this->_filterModelName = 'Magento_Catalog_Model_Layer_Filter_Category';
    }
}