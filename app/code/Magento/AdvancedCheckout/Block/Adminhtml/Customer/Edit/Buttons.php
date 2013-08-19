<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_AdvancedCheckout
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Additional buttons on customer edit form
 *
 * @category    Magento
 * @package     Magento_AdvancedCheckout
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_AdvancedCheckout_Block_Adminhtml_Customer_Edit_Buttons extends Magento_Adminhtml_Block_Customer_Edit
{
    /**
     * Add "Manage Shopping Cart" button on customer management page
     *
     * @return Magento_AdvancedCheckout_Block_Adminhtml_Customer_Edit_Buttons
     */
    public function addButtons()
    {
        if (!$this->_authorization->isAllowed('Magento_AdvancedCheckout::view')
            && !$this->_authorization->isAllowed('Magento_AdvancedCheckout::update')
            || Mage::app()->getStore()->getWebsiteId() == Mage::registry('current_customer')->getWebsiteId()
        ) {
            return $this;
        }
        $container = $this->getParentBlock();
        if ($container instanceof Magento_Backend_Block_Template && $container->getCustomerId()) {
            $url = Mage::getSingleton('Magento_Backend_Model_Url')->getUrl('*/checkout/index', array(
                'customer' => $container->getCustomerId()
            ));
            $container->addButton('manage_quote', array(
                'label' => Mage::helper('Magento_AdvancedCheckout_Helper_Data')->__('Manage Shopping Cart'),
                'onclick' => "setLocation('" . $url . "')",
            ), 0);
        }
        return $this;
    }
}
