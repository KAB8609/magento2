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
 * "Add by SKU" accordion
 *
 * @category    Enterprise
 * @package     Enterprise_Checkout
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Checkout_Block_Adminhtml_Sales_Order_Create_Sku
    extends Mage_Adminhtml_Block_Sales_Order_Create_Abstract
{
    /**
     * Define ID
     */
    protected function _construct()
    {
        $this->setId('sales_order_create_sku');
    }

    /**
     * Retrieve accordion header
     *
     * @return string
     */
    public function getHeaderText()
    {
        return $this->__('Add to Order by SKU');
    }

    /**
     * Retrieve CSS class for header
     *
     * @return string
     */
    public function getHeaderCssClass()
    {
        return 'head-catalog-product';
    }

    /**
     * Retrieve "Add to order" button
     *
     * @return string
     */
    public function getButtonsHtml()
    {
        $addButtonData = array(
            'label' => $this->__('Add to Order'),
            'onclick' => 'addBySku.submitSkuForm()',
            'class' => 'action-add',
        );
        return $this->getLayout()->createBlock('Mage_Adminhtml_Block_Widget_Button')->setData($addButtonData)->toHtml();
    }
}
