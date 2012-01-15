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
 * Admin Checkout main form container
 *
 * @category    Enterprise
 * @package     Enterprise_Checkout
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Checkout_Block_Adminhtml_Manage extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('checkout_manage_container');

        if (Mage::getSingleton('Mage_Admin_Model_Session')->isAllowed('sales/order/actions/create')) {
            $this->_updateButton('save', 'label', Mage::helper('Mage_Sales_Helper_Data')->__('Create Order'));
            $this->_updateButton('save', 'onclick', 'setLocation(\'' . $this->getCreateOrderUrl() . '\');');
        } else {
            $this->_removeButton('save');
        }
        $this->_removeButton('reset');
        $this->_updateButton('back', 'onclick', 'setLocation(\'' . $this->getBackUrl() . '\');');
    }

    /**
     * Prepare layout, create buttons
     *
     * @return Mage_Core_Block_Abstract
     */
    protected function _prepareLayout()
    {
        if (!Mage::getSingleton('Mage_Admin_Model_Session')->isAllowed('sales/enterprise_checkout/update')) {
            return $this;
        }

        $this->setChild('add_products_button',
            $this->getLayout()->createBlock('Mage_Adminhtml_Block_Widget_Button')
                ->setData(array(
                    'label' => Mage::helper('Enterprise_Checkout_Helper_Data')->__('Add Products'),
                    'onclick' => 'checkoutObj.searchProducts()',
                    'class' => 'add',
                    'id' => 'add_products_btn'
                ))
        );

        $this->setChild('update_button',
            $this->getLayout()->createBlock('Mage_Adminhtml_Block_Widget_Button')
                ->setData(array(
                    'label' => Mage::helper('Enterprise_Checkout_Helper_Data')->__('Update Items and Qty\'s'),
                    'onclick' => 'checkoutObj.updateItems()',
                    'style' => 'float:right; margin-left: 5px;'
                ))
        );
        $deleteAllConfirmString = Mage::helper('enterprise_checkout')->__('Are you sure you want to delete all items from shopping cart?');
        $this->setChild('empty_customer_cart_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label' => Mage::helper('enterprise_checkout')->__('Clear Shopping Cart'),
                    'onclick' => 'confirm(\'' . $deleteAllConfirmString . '\') '
                        . ' && checkoutObj.updateItems({\'empty_customer_cart\': 1})',
                    'style' => 'float:right;'
                ))
        );

        $this->setChild('addto_cart_button',
            $this->getLayout()->createBlock('Mage_Adminhtml_Block_Widget_Button')
                ->setData(array(
                    'label' => Mage::helper('Enterprise_Checkout_Helper_Data')->__('Add Selected Product(s) to Shopping Cart'),
                    'onclick' => 'checkoutObj.addToCart()',
                    'class' => 'add button-to-cart'
                ))
        );

        $this->setChild('cancel_add_products_button',
            $this->getLayout()->createBlock('Mage_Adminhtml_Block_Widget_Button')
                ->setData(array(
                    'label' => Mage::helper('Enterprise_Checkout_Helper_Data')->__('Cancel'),
                    'onclick' => 'checkoutObj.cancelSearch()',
                    'class' => 'cancel'
                ))
        );

        return $this;
    }

    /**
     * Rewrite for getFormHtml()
     *
     * @return string
     */
    public function getFormHtml()
    {
        return '';
    }

    /**
     * Return header title
     *
     * @return string
     */
    public function getHeaderText()
    {
        $customer = $this->escapeHtml($this->_getCustomer()->getName());
        $store = $this->escapeHtml($this->_getStore()->getName());
        return Mage::helper('Enterprise_Checkout_Helper_Data')->__('Shopping Cart for %s in %s', $customer, $store);
    }

    /**
     * Return current customer from regisrty
     *
     * @return Mage_Customer_Model_Customer
     */
    protected function _getCustomer()
    {
        return Mage::registry('checkout_current_customer');
    }

    /**
     * Return current store from regisrty
     *
     * @return Mage_Core_Model_Store
     */
    protected function _getStore()
    {
        return Mage::registry('checkout_current_store');
    }

    /**
     * Return URL to customer edit page
     *
     * @return string
     */
    public function getBackUrl()
    {
        if ($this->_getCustomer() && $this->_getCustomer()->getId()) {
            return $this->getUrl('*/customer/edit', array('id' => $this->_getCustomer()->getId()));
        } else {
            return $this->getUrl('*/customer');
        }
    }

    /**
     * Return URL to controller action
     *
     * @return string
     */
    public function getActionUrl($action)
    {
        return $this->getUrl('*/*/' . $action, array('_current' => true));
    }

    /**
     * Return URL to admin order creation
     *
     * @return string
     */
    public function getCreateOrderUrl()
    {
        return $this->getUrl('*/*/createOrder', array('_current' => true));
    }

    /**
     * Retrieve url for loading blocks
     *
     * @return string
     */
    public function getLoadBlockUrl()
    {
        return $this->getUrl('*/*/loadBlock');
    }

    public function getOrderDataJson()
    {
        $actionUrls = array(
            'cart' => $this->getActionUrl('cart'),
            'applyCoupon' => $this->getActionUrl('applyCoupon'),
            'coupon' => $this->getActionUrl('coupon')
        );

        $messages = array(
            'chooseProducts' => $this->__('Choose some products to add to shopping cart.')
        );

        $data = array(
            'action_urls' => $actionUrls,
            'messages' => $messages,
            'customer_id' => $this->_getCustomer()->getId(),
            'store_id' => $this->_getStore()->getId()
        );

        return Mage::helper('Mage_Core_Helper_Data')->jsonEncode($data);
    }

    /**
     * Retrieve curency name by code
     *
     * @param   string $code
     * @return  string
     */
    public function getCurrencySymbol($code)
    {
        $currency = Mage::app()->getLocale()->currency($code);
        return $currency->getSymbol() ? $currency->getSymbol() : $currency->getShortName();
    }

    /**
     * Retrieve current order currency code
     *
     * @return string
     */
    public function getCurrentCurrencyCode()
    {
        return $this->_getStore()->getCurrentCurrencyCode();
    }
}
