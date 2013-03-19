<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Checkout
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Form for adding products by SKU
 *
 * @category    Enterprise
 * @package     Enterprise_Checkout
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Checkout_Block_Adminhtml_Sales_Order_Create_Sku_Add
    extends Enterprise_Checkout_Block_Adminhtml_Sku_Abstract
{
    /**
     * Returns JavaScript variable name of AdminCheckout or AdminOrder instance
     *
     * @return string
     */
    public function getJsOrderObject()
    {
        return 'order';
    }

    /**
     * Returns HTML ID of the error grid
     *
     * @return string
     */
    public function getErrorGridId()
    {
        return 'order_errors';
    }

    /**
     * Retrieve file upload URL
     *
     * @return string
     */
    public function getFileUploadUrl()
    {
        return $this->getUrl('*/sales_order_create/processData');
    }

    /**
     * Retrieve context specific JavaScript
     *
     * @return string
     */
    public function getContextSpecificJs()
    {
        return '
            var parentAreasLoaded = ' . $this->getJsOrderObject() . '.areasLoaded;
            ' . $this->getJsOrderObject() . '.areasLoaded = function () {
                initSku();
                parentAreasLoaded();
            };';
    }
}
