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
 * Adminhtml sales order create
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Adminhtml_Block_Sales_Order_Create extends Mage_Adminhtml_Block_Widget_Form_Container
{
    protected function _construct()
    {
        $this->_objectId = 'order_id';
        $this->_controller = 'sales_order';
        $this->_mode = 'create';

        parent::_construct();

        $this->setId('sales_order_create');

        $customerId = $this->_getSession()->getCustomerId();
        $storeId    = $this->_getSession()->getStoreId();


        $this->_updateButton('save', 'label', Mage::helper('Mage_Sales_Helper_Data')->__('Submit Order'));
        $this->_updateButton('save', 'onclick', 'order.submit()');
        // Temporary solution, unset button widget. Will have to wait till jQuery migration is complete
        $this->_updateButton('save', 'data_attribute', array());

        $this->_updateButton('save', 'id', 'submit_order_top_button');
        if (is_null($customerId) || !$storeId) {
            $this->_updateButton('save', 'style', 'display:none');
        }

        $this->_updateButton('back', 'id', 'back_order_top_button');
        $this->_updateButton('back', 'onclick', 'setLocation(\'' . $this->getBackUrl() . '\')');

        $this->_updateButton('reset', 'id', 'reset_order_top_button');

        if (is_null($customerId)) {
            $this->_updateButton('reset', 'style', 'display:none');
        } else {
            $this->_updateButton('back', 'style', 'display:none');
        }

        $confirm = Mage::helper('Mage_Sales_Helper_Data')->__('Are you sure you want to cancel this order?');
        $this->_updateButton('reset', 'label', Mage::helper('Mage_Sales_Helper_Data')->__('Cancel'));
        $this->_updateButton('reset', 'class', 'cancel');
        $this->_updateButton('reset', 'onclick', 'deleteConfirm(\''.$confirm.'\', \'' . $this->getCancelUrl() . '\')');

        $pageTitle = $this->getLayout()->createBlock('Mage_Adminhtml_Block_Sales_Order_Create_Header')->toHtml();
        if (is_object($this->getLayout()->getBlock('page-title'))) {
            $this->getLayout()->getBlock('page-title')->setPageTitle($pageTitle);
        }
    }

    /**
     * Prepare header html
     *
     * @return string
     */
    public function getHeaderHtml()
    {
        $out = '<div id="order-header">'
            . $this->getLayout()->createBlock('Mage_Adminhtml_Block_Sales_Order_Create_Header')->toHtml()
            . '</div>';
        return $out;
    }

    /**
     * Prepare form html. Add block for configurable product modification interface
     *
     * @return string
     */
    public function getFormHtml()
    {
        $html = parent::getFormHtml();
        return $html;
    }

    public function getHeaderWidth()
    {
        return 'width: 70%;';
    }

    /**
     * Retrieve quote session object
     *
     * @return Mage_Adminhtml_Model_Session_Quote
     */
    protected function _getSession()
    {
        return Mage::getSingleton('Mage_Adminhtml_Model_Session_Quote');
    }

    public function getCancelUrl()
    {
        if ($this->_getSession()->getOrder()->getId()) {
            $url = $this->getUrl('*/sales_order/view', array(
                'order_id' => Mage::getSingleton('Mage_Adminhtml_Model_Session_Quote')->getOrder()->getId()
            ));
        } else {
            $url = $this->getUrl('*/*/cancel');
        }

        return $url;
    }

    /**
     * Get URL for back (reset) button
     *
     * @return string
     */
    public function getBackUrl()
    {
        return $this->getUrl('*/' . $this->_controller . '/');
    }
}
