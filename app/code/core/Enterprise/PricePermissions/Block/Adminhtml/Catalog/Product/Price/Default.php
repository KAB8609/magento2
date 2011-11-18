<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_PricePermission
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Default Product Price field renderer
*
 * @category    Enterprise
 * @package     Enterprise_PricePermissions
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_PricePermissions_Block_Adminhtml_Catalog_Product_Price_Default
    extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    /**
     * Render Default Product Price field as disabled if user does not have enough permissions
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        if (!Mage::helper('Enterprise_PricePermissions_Helper_Data')->getCanAdminEditProductPrice()) {
            $element->setReadonly(true, true);
        }
        return parent::_getElementHtml($element);
    }
}
