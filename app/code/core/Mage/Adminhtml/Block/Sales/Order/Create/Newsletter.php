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
 * Adminhtml sales order create newsletter block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Adminhtml_Block_Sales_Order_Create_Newsletter extends Mage_Adminhtml_Block_Sales_Order_Create_Abstract
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('sales_order_create_newsletter');
    }

    public function getHeaderText()
    {
        return Mage::helper('Mage_Sales_Helper_Data')->__('Newsletter Subscription');
    }

    public function getHeaderCssClass()
    {
        return 'head-newsletter-list';
    }

    protected function _toHtml()
    {
        return parent::_toHtml();
    }

}
