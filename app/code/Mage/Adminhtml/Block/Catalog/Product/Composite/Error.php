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
 * Adminhtml block for showing product options fieldsets
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author    Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Catalog_Product_Composite_Error extends Mage_Core_Block_Template
{
    /**
     * Returns error message to show what kind of error happened during retrieving of product
     * configuration controls
     *
     * @return string
     */
    public function _toHtml()
    {
        $message = Mage::registry('composite_configure_result_error_message');
        return Mage::helper('Mage_Core_Helper_Data')->jsonEncode(array('error' => true, 'message' => $message));
    }
}