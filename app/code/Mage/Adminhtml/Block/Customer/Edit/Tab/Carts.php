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
 * Obtain all carts contents for specified client
 *
 */
class Mage_Adminhtml_Block_Customer_Edit_Tab_Carts extends Mage_Adminhtml_Block_Template
{
    /**
     * Add shopping cart grid of each website
     *
     * @return Mage_Adminhtml_Block_Customer_Edit_Tab_Carts
     */
    protected function _prepareLayout()
    {
        $sharedWebsiteIds = Mage::registry('current_customer')->getSharedWebsiteIds();
        $isShared = count($sharedWebsiteIds) > 1;
        foreach ($sharedWebsiteIds as $websiteId) {
            $blockName = 'customer_cart_' . $websiteId;
            $block = $this->getLayout()->createBlock('Mage_Adminhtml_Block_Customer_Edit_Tab_Cart',
                $blockName, array('data' => array('website_id' => $websiteId)));
            if ($isShared) {
                $block->setCartHeader(__('Shopping Cart from %s', Mage::app()->getWebsite($websiteId)->getName()));
            }
            $this->setChild($blockName, $block);
        }
        return parent::_prepareLayout();
    }

    /**
     * Just get child blocks html
     *
     * @return string
     */
    protected function _toHtml()
    {
        Mage::dispatchEvent('adminhtml_block_html_before', array('block' => $this));
        return $this->getChildHtml();
    }
}
