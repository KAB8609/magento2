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
 * Customer edit block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Customer_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    protected function _construct()
    {
        $this->_objectId = 'id';
        $this->_controller = 'customer';

        if ($this->getCustomerId() &&
            Mage::getSingleton('Mage_Backend_Model_Auth_Session')->isAllowed('Mage_Sales::create')) {
            $this->_addButton('order', array(
                'label' => Mage::helper('Mage_Customer_Helper_Data')->__('Create Order'),
                'onclick' => 'setLocation(\'' . $this->getCreateOrderUrl() . '\')',
                'class' => 'add',
            ), 0);
        }

        parent::_construct();

        $this->_updateButton('save', 'label', Mage::helper('Mage_Customer_Helper_Data')->__('Save Customer'));
        $this->_updateButton('delete', 'label', Mage::helper('Mage_Customer_Helper_Data')->__('Delete Customer'));

        $customer = Mage::registry('current_customer');
        if ($customer && Mage::registry('current_customer')->isReadonly()) {
            $this->_removeButton('save');
            $this->_removeButton('reset');
        }

        if (!$customer || !Mage::registry('current_customer')->isDeleteable()) {
            $this->_removeButton('delete');
        }
    }

    public function getCreateOrderUrl()
    {
        return $this->getUrl('*/sales_order_create/start', array('customer_id' => $this->getCustomerId()));
    }

    public function getCustomerId()
    {
        return Mage::registry('current_customer') ? Mage::registry('current_customer')->getId() : false;
    }

    public function getHeaderText()
    {
        if (Mage::registry('current_customer')->getId()) {
            return $this->escapeHtml(Mage::registry('current_customer')->getName());
        }
        else {
            return Mage::helper('Mage_Customer_Helper_Data')->__('New Customer');
        }
    }

    /**
     * Prepare form html. Add block for configurable product modification interface
     *
     * @return string
     */
    public function getFormHtml()
    {
        $html = parent::getFormHtml();
        $html .= $this->getLayout()->createBlock('Mage_Adminhtml_Block_Catalog_Product_Composite_Configure')->toHtml();
        return $html;
    }

    public function getValidationUrl()
    {
        return $this->getUrl('*/*/validate', array('_current'=>true));
    }

    protected function _prepareLayout()
    {
        if (!Mage::registry('current_customer')->isReadonly()) {
            $this->_addButton('save_and_continue', array(
                'label'     => Mage::helper('Mage_Customer_Helper_Data')->__('Save and Continue Edit'),
                'onclick'   => 'saveAndContinueEdit(\''.$this->_getSaveAndContinueUrl().'\')',
                'class'     => 'save'
            ), 10);
        }

        return parent::_prepareLayout();
    }

    protected function _getSaveAndContinueUrl()
    {
        return $this->getUrl('*/*/save', array(
            '_current'  => true,
            'back'      => 'edit',
            'tab'       => '{{tab_id}}'
        ));
    }
}
